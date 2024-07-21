<?php
require_once "embed/header.php";
require_once "embed/mail.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists("req-id", $_REQUEST)) {
        $_REQUEST["id"] = $con->query("SELECT * FROM `requests` where `id` = " . $_REQUEST["req-id"])->fetch_assoc()["requestid"];
    }
    if (array_key_exists("id", $_REQUEST)) {
        $email_to_info = $con->query("SELECT * FROM `users` where `id` = " . $_REQUEST["id"])->fetch_assoc();
    }
    function check_access($res)
    {
        global $usertype;
        switch ($res) {
            case 1:
                if (!is_null($usertype)) return;
                break;
            case 2:
                if (!is_null($usertype) && $usertype != "inactived") return;
                break;
            case 3:
                if ($usertype == "admin" || $usertype == "system-admin") return;
                break;
            case 4:
                if ($usertype == "system-admin") return;
                break;
        }
        $_SESSION["message"] = "权限错误！";
        header("Location: index.php");
        exit;
    }
    function check_user_exists($id)
    {
        global $con, $email_to_info;
        if ($con->query("SELECT * FROM `users` WHERE `id` = " . $id)->num_rows) {
            $email_to_info = $con->query("SELECT * FROM `users` where `id` = " . $id)->fetch_assoc();
            return true;
        }
        $_SESSION["message"] = "错误：用户不存在！";
        return false;
    }
    function containsAnyRegex($string, $chars)
    {
        $pattern = '/[' . preg_quote($chars, '/') . ']/'; // 使用 preg_quote 来转义特殊字符
        if (preg_match($pattern, $string)) {
            return true;
        }
        return false;
    }
    switch ($_REQUEST["type"]) {
        case "query-email":
            //查询邮箱
            if ($con->query("SELECT * FROM `users` WHERE `email` = '" . $con->real_escape_string(urldecode($_REQUEST["email"])) . "'")->num_rows == 0) {
                echo "true";
            } else {
                echo "false";
            }
            break;
        case "register":
            //注册
            $con->query(
                "INSERT INTO `users` VALUES (NULL,'" .
                    $con->real_escape_string($_REQUEST["email"]) .
                    "','" .
                    $con->real_escape_string($_REQUEST["realname"])  .
                    "',SHA1('" .
                    $con->real_escape_string($_REQUEST["password"])  .
                    "')," .
                    ($_REQUEST["workid"] == "" ? "NULL" : "'" . $con->real_escape_string($_REQUEST["workid"]) . "'") .
                    ",'" .
                    $con->real_escape_string($_REQUEST["department"])  .
                    "','" .
                    $_REQUEST["access"] .
                    "',NULL,0,NULL,CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())"
            );
            $_SESSION["loginid"] = $con->insert_id;
            $_SESSION["message"] = "注册成功！现在需要等待批准~";
            $con->query(
                "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'register-allow','" . $con->real_escape_string(json_encode([
                    "request-to-be" => $_REQUEST["access"]
                ])) . "',CURRENT_TIMESTAMP())"
            );
            header("Location: manage.php");
            break;
        case "login":
            //登录
            $login = $con->query("SELECT * FROM `users` WHERE `email` = '" . $con->real_escape_string($_REQUEST["email"]) . "'")->fetch_assoc();
            if (sha1($_REQUEST["password"]) == $login["password"]) {
                $_SESSION["loginid"] = $login["id"];
                $_SESSION["message"] = "登录成功！";
                $con->query(
                    "UPDATE `users` SET logintime = CURRENT_TIMESTAMP() WHERE `id` = " . $_SESSION["loginid"]
                );
                header("Location: manage.php");
            } else {
                $_SESSION["loginid"] = null;
                $_SESSION["message"] = "登录失败：账号或密码错误";
                header("Location: access.php");
            }
            break;
        case "delete-account":
            $format_string = "<h1>您的账号已删除</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）<b>已被删除</b>。感谢你的使用，期待再会。</p>";
            send_mail($format_string, $email_to_info["email"], "您的账号已被删除");
            $con->query("DELETE FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"]);
            $con->query("DELETE FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"]);
            $con->query("DELETE FROM `users` WHERE `id` = " . $_SESSION["loginid"]);
            unset($_SESSION["loginid"]);
            $_SESSION["message"] = "账号删除成功";
            header("Location: index.php");
            break;
        case "change-header":
            //改变头像
            if (is_null($_FILES['header'])) {
                $con->query("UPDATE `users` SET header = NULL WHERE `id` = " . $_SESSION["loginid"]);
                $_SESSION["message"] = "头像删除成功";
            } else {
                if (!($_FILES['header']['type'] == "image/png" || $_FILES['header']['type'] == "image/jpg" || $_FILES['header']['type'] == "image/jpeg")) {
                    $_SESSION["message"] = "头像更改失败：文件类型不符";
                } else if ($_FILES['header']['size'] > 1100000) {
                    $_SESSION["message"] = "头像更改失败：文件大小超出额定范围";
                } else {
                    $con->query("UPDATE `users` SET header = '" . $con->real_escape_string(file_get_contents($_FILES['header']['tmp_name'])) . "' WHERE `id` = " . $_SESSION["loginid"]);
                    $_SESSION["message"] = "头像更改成功";
                }
            }
            header("Location: manage.php");
            break;
        case "change-realname":
            //改变真名
            $con->query("UPDATE `users` SET realname = '" . $con->real_escape_string($_REQUEST["realname"]) . "' WHERE `id` = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "真名成功更改为" . $_REQUEST["realname"];
            header("Location: manage.php");
            break;
        case "change-workid":
            //改变工号
            if ($_REQUEST["workid"] != "") {
                $con->query("UPDATE `users` SET `workid` = '" . $con->real_escape_string($_REQUEST["workid"]) . "' WHERE `id` = " . $_SESSION["loginid"]);
                $_SESSION["message"] = "工号成功更改为" . $_REQUEST["workid"];
            } else {
                $con->query("UPDATE `users` SET `workid` = NULL WHERE `id` = " . $_SESSION["loginid"]);
                $_SESSION["message"] = "工号成功置空";
            }
            header("Location: manage.php");
            break;
        case "change-email":
            //改变部门
            $con->query("UPDATE `users` SET `email` = '" . $con->real_escape_string($_REQUEST["email"]) . "' WHERE `id` = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "邮箱成功更改为" . $_REQUEST["email"];
            header("Location: manage.php");
            break;
        case "change-depart":
            //改变部门
            $con->query("UPDATE `users` SET `department` = '" . $con->real_escape_string($_REQUEST["depart"]) . "' WHERE `id` = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "部门成功更改为" . $_REQUEST["depart"];
            header("Location: manage.php");
            break;
        case "change-password":
            //改变密码
            if (sha1($_REQUEST["old-password"]) == $con->query("SELECT * FROM `users` where `id` = " . $_SESSION["loginid"])->fetch_assoc()["password"]) {
                $con->query("UPDATE `users` SET `password` = '" . sha1($_REQUEST["new-password"]) . "' WHERE `id` = " . $_SESSION["loginid"]);
                $_SESSION["message"] = "密码成功更改";
            } else {
                $_SESSION["message"] = "密码更改失败：与原密码不符";
            }
            // header("Location: manage.php");
            break;
        case "change-access":
            check_access(2);
            //更换权限
            if ($_REQUEST["toaccess"] == "staff") {
                //无需批准
                $con->query("UPDATE `users` SET `accessment` = 'staff' WHERE `id` = " . $_SESSION["loginid"]);
            } else {
                //需要批准
                $con->query("INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'upgrade-to-admin',NULL)");
                $_SESSION["message"] = "成功生成升级权限的请求";
            }
            header("Location: manage.php");
            break;
        case "request-register":
            $con->query(
                "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'register-allow','" . $con->real_escape_string(json_encode([
                    "request-to-be" => $_REQUEST["request-to-be"]
                ])) . "',CURRENT_TIMESTAMP())"
            );
            $_SESSION["message"] = "提交申请成功，请查看邮箱以了解后续进展";
            header("Location: manage.php");
            break;
        case "query-room-number":
            if ($_REQUEST["room-number"] == "") {
                echo json_encode([]);
            } else {
                echo json_encode($con->query("SELECT `number` FROM `rooms` WHERE `number` LIKE '%" . $con->real_escape_string(urldecode($_REQUEST["room-number"])) . "%'")->fetch_all(MYSQLI_ASSOC));
            }
            break;
        case "request-manage-change":
            //请求更改管理区域
            check_access(3);
            $con->query("INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'change-manage','" . $con->real_escape_string(json_encode([
                "area" => $_REQUEST["area-name"]
            ])) . "',CURRENT_TIMESTAMP())");
            $_SESSION["message"] = "成功生成更改管理区域的请求，请等待系统管理员批准";
            header("Location: manage.php");
            break;
        case "delete-room":
            check_access(4);
            //房间删除
            $userinfo = $con->query("SELECT * FROM `users` where `id` = " . $_SESSION["loginid"])->fetch_assoc();
            foreach ($con->query("SELECT * FROM `areas`")->fetch_all(MYSQLI_ASSOC) as $value) {
                $manage_now = explode(",", $value["includes"]);
                if (array_search($_REQUEST["number"], $manage_now) !== false) {
                    unset($manage_now[array_search($_REQUEST["number"], $manage_now)]);
                    $con->query("UPDATE `areas` SET `includes` = '" . $con->real_escape_string(implode(",", $manage_now)) . "' WHERE `name` = '" . $con->real_escape_string($value["name"]) . "'");
                }
            }
            foreach ($con->query("SELECT * FROM `checkios` WHERE `room` = '" . $con->real_escape_string($_REQUEST["number"]) . "'")->fetch_all(MYSQLI_ASSOC) as $key => $value) {
                $email_to_info = $con->query("SELECT * FROM `users` WHERE `id` = " . $value["requestid"])->fetch_assoc()["email"];
                $format_string = "<h1>您的入住被取消</h1><p>尊敬的" . $email_to_info["realname"] . "，由于管理员" . $userinfo["realname"] . "（ID：" . $userinfo["id"] . "删除了您的账号（ID：" . $email_to_info["id"] . "）的入住房间" . $_REQUEST["number"] . "，您的入住已被<b>取消</b>。</p>";
            }
            $con->query("DELETE FROM `rooms` WHERE `number` = '" . $con->real_escape_string($_REQUEST["number"]) . "'");
            $_SESSION["message"] = "房间删除成功";
            header("Location: roomlist.php");
            break;
        case "delete-user":
            check_access(4);
            //系统管理员删除账号
            if (check_user_exists($_REQUEST["id"])) {
                $format_string = "<h1>您的账号已被管理员删除</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）<b>已被系统管理员删除</b>。感谢你的使用，期待再会。</p>";
                send_mail($format_string, $email_to_info["email"], "您的账号已被管理员删除");
                $con->query("DELETE FROM `checkios` WHERE `requestid` = " . $_REQUEST["id"]);
                $con->query("DELETE FROM `requests` WHERE `requestid` = " . $_REQUEST["id"]);
                $con->query("DELETE FROM `users` WHERE `id` = " . $_REQUEST["id"]);
                $_SESSION["message"] = "删除账号成功";
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            header("Location: userlist.php");
            break;
        case "add-room":
            // 添加房间
            check_access(4);
            if (containsAnyRegex($_REQUEST["room-number"], "'\",%\\=")) {
                $_SESSION["message"] = "添加房间失败：含有特殊字符！";
            } else if ($con->query("SELECT * FROM `rooms` WHERE `number` = '" . $con->real_escape_string($_REQUEST["room-number"]) . "'")->num_rows == 0) {
                $con->query("INSERT INTO `rooms` VALUES ('" . $con->real_escape_string($_REQUEST["room-number"]) . "','normal','" . $con->real_escape_string($_REQUEST["location"]) . "'," . $_REQUEST["max-ps"] . ",CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())");
                $_SESSION["message"] = "添加房间成功";
            } else {
                $_SESSION["message"] = "添加房间失败：房间重复！";
            }
            header("Location: roomlist.php");
            break;
        case "query-user-info":
            // 查询用户管理区域名
            echo json_encode(array_merge($con->query("SELECT * FROM `users` WHERE `id` = " . $_REQUEST["id"])->fetch_assoc(), ["allparts" => $con->query("SELECT * FROM `areas`")->fetch_all(MYSQLI_ASSOC)]));
            break;
        case "change-user-manage":
            // 更改管理员管理区域
            check_access(4);
            if (check_user_exists($_REQUEST["id"])) {
                if ($_REQUEST["area-name"] == "[null]") {
                    $con->query("UPDATE `users` SET `managepart` = NULL WHERE `id` = " . $_REQUEST["id"]);
                    $_SESSION["message"] = "更改用户权限成功";
                } else {
                    if ($con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($_REQUEST["area-name"]) . "'")->num_rows == 0) {
                        $_SESSION["message"] = "更改用户权限失败：不存在的区域";
                    } else {
                        $con->query("UPDATE `users` SET `managepart` = '" . $con->real_escape_string($_REQUEST["area-name"]) . "' WHERE `id` = " . $_REQUEST["id"]);
                        $_SESSION["message"] = "更改用户权限成功";
                    }
                }
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            header("Location: userlist.php");
            break;
        case "request-check-in":
            check_access(2);
            $_REQUEST["end-time"] = date('Y-m-d', strtotime($_REQUEST["start-time"] . ' + ' . $_REQUEST["during-time"] . ' days'));
            if ($usertype == "system-admin") {
                $con->query("INSERT INTO `checkios` VALUES (" . $_SESSION["loginid"] . ",'" . $con->real_escape_string($_REQUEST["room-number"]) . "','" . $_REQUEST["start-time"] . "','" . $_REQUEST["end-time"] . "','" . $con->real_escape_string($_REQUEST["reason"]) . "')");
                $_SESSION["message"] = "入住成功";
            } else {
                $con->query("INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'check-in','" . $con->real_escape_string(json_encode($_REQUEST)) . "',CURRENT_TIMESTAMP())");
                $_SESSION["message"] = "提交入住申请成功";
            }
            header("Location: roomlist.php");
            break;
        case "check-out":
            $con->query("DELETE FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "成功办理签出";
            header("Location: roomlist.php");
            break;
        case "change-room-status":
            check_access(3);
            $con->query("UPDATE `rooms` SET `status` = '" . $_REQUEST["change-to-status"] . "' WHERE `number` = '" . $con->real_escape_string($_REQUEST["number"]) . "'");
            $con->query("UPDATE `rooms` SET `updatetime` = CURRENT_TIMESTAMP() WHERE `number` = '" . $con->real_escape_string($_REQUEST["number"]) . "'");
            $_SESSION["message"] = "成功更改房间状态";
            header("Location: roomlist.php");
            break;
        case "add-area":
            check_access(4);
            if (containsAnyRegex($_REQUEST["name"], "'\"\\=[]")) {
                $_SESSION["message"] = "添加区域失败：名称含有特殊字符！";
            } else if ($con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($_REQUEST["name"]) . "'")->num_rows) {
                $_SESSION["message"] = "添加管理区域失败：区域已存在";
            } else {
                $invalid = null;
                if (strlen($_REQUEST["includes"]) > 0) {
                    foreach (explode(',', $_REQUEST["includes"]) as $value) {
                        if ($con->query("SELECT * FROM `rooms` WHERE `number` = '" . $con->real_escape_string($value) . "'")->num_rows == 0) {
                            $invalid = $value;
                            break;
                        }
                    }
                }
                if (!is_null($invalid)) {
                    $_SESSION["message"] = "添加管理区域失败：房间" . $invalid . "不存在";
                } else {
                    $con->query("INSERT INTO `areas` VALUES ('" . $con->real_escape_string($_REQUEST["name"]) . "','" . $con->real_escape_string($_REQUEST["includes"]) . "')");
                    $_SESSION["message"] = "添加管理区域成功";
                }
            }
            header("Location: roomlist.php");
            break;
        case "query-area-includes":
            $res = $con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($_REQUEST["name"]) . "'")->fetch_assoc()["includes"];
            if (strlen($res)) {
                echo json_encode(explode(',', $res));
            } else {
                echo json_encode([]);
            }
            break;
        case "modify-area":
            check_access(4);
            if (containsAnyRegex($_REQUEST["new-name"], "'\"\\=[]")) {
                $_SESSION["message"] = "更新区域失败：名称含有特殊字符！";
            } else {
                $invalid = null;
                if (strlen($_REQUEST["includes"]) > 0) {
                    foreach (explode(',', $_REQUEST["includes"]) as $value) {
                        if ($con->query("SELECT * FROM `rooms` WHERE `number` = '" . $con->real_escape_string($value) . "'")->num_rows == 0) {
                            $invalid = $value;
                            break;
                        }
                    }
                }
                if (is_null($invalid)) {
                    $con->query("UPDATE `areas` SET `name` = '" . $con->real_escape_string($_REQUEST["new-name"]) . "', `includes` = '" . $con->real_escape_string($_REQUEST["includes"]) . "' WHERE `name` = '" . $con->real_escape_string($_REQUEST["old-name"]) . "'");
                    $_SESSION["message"] = "更改管理区域成功";
                } else {
                    $_SESSION["message"] = "更改管理区域失败：房间" . $invalid . "不存在";
                }
            }
            header("Location: roomlist.php");
            break;
        case "query-room-detail-info":
            $people = [];
            foreach ($con->query("SELECT * FROM `checkios` WHERE `room` = '" . $con->real_escape_string(urldecode($_REQUEST["number"])) . "'")->fetch_all(MYSQLI_ASSOC) as $key => $value) {
                array_push($people, array_merge($value, $con->query("SELECT `id`,`realname` FROM `users` WHERE `id` = " . $value["requestid"])->fetch_assoc()));
            }
            echo json_encode(array_merge(
                $con->query("SELECT * FROM `rooms` WHERE `number` = '" . $con->real_escape_string(urldecode($_REQUEST["number"])) . "'")->fetch_assoc(),
                ["people" => $people],
                ["usertype" => $usertype]
            ));
            break;
        case "change-room-location":
            $con->query("UPDATE `rooms` SET `location` = '" . $con->real_escape_string($_REQUEST["location"]) . "' WHERE `number` = '" . $con->real_escape_string("number") . "'");
            $_SESSION["message"] = "更改房间地址成功";
            header("Location: roomlist.php");
            break;
        case "delete-area":
            $con->query("UPDATE `users` SET `managepart` = NULL WHERE `managepart` = '" . $con->real_escape_string($_REQUEST["name"]) . "'");
            $con->query("DELETE FROM `areas` WHERE `name` = '" . $con->real_escape_string($_REQUEST["name"]) . "'");
            $_SESSION["message"] = "删除区域成功";
            header("Location: roomlist.php");
            break;
        case "change-room-max":
            if ($con->query("SELECT * FROM `checkios` WHERE `room` = '" . $con->real_escape_string($_REQUEST["number"]) . "'")->num_rows > $_REQUEST["max-ps"]) {
                $_SESSION["message"] = "更改房间最大人数失败：人数过小！";
            } else {
                $con->query("UPDATE `rooms` SET `max` = " . $_REQUEST["max-ps"] . " WHERE `number` = '" . $con->real_escape_string($_REQUEST["number"]) . "'");
                $_SESSION["message"] = "更改房间最大人数成功";
            }
            header("Location: roomlist.php");
            break;
        case "remove-living-person":
            $format_string = "<h1>您的入住被取消</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的入住房间" . $_REQUEST["number"] . "被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>取消</b>。</p>";
            send_mail($format_string, $email_to_info["email"], "您的账号入住被取消");
            $con->query("DELETE FROM `checkios` WHERE `requestid` = '" . $con->real_escape_string($_REQUEST["id"]) . "'");
            $_SESSION["message"] = "成功移除居住人";
            header("Location: roomlist.php");
            break;
        case "remove-all-living-people":
            foreach ($con->query("SELECT * FROM `checkios` WHERE `room` = '" . $con->real_escape_string($_REQUEST["number"]) . "'") as $key => $value) {
                $email_to_info = $con->query("SELECT * FROM `users` WHERE id = " . $value["requestid"])->fetch_assoc()["email"];
                $format_string = "<h1>您的入住被取消</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的入住房间" . $_REQUEST["number"] . "被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>取消</b>。</p>";
                send_mail($format_string, $email_to_info["email"], "您的账号入住被取消");
            }
            $con->query("DELETE FROM `checkios` WHERE `room` = '" . $con->real_escape_string($_REQUEST["number"]) . "'");
            $_SESSION["message"] = "成功清空居住人";
            header("Location: roomlist.php");
            break;
            // 批准相关
        case "check-in-allow":
            check_access(4);
            if (check_user_exists($_REQUEST["id"])) {
                if ($_REQUEST["action"] == "accept") {
                    $params = json_decode($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_REQUEST["req-id"] . " AND `type` = 'check-in'")->fetch_assoc()["param"], true);
                    $con->query("INSERT INTO `checkios` VALUES (" . $_REQUEST["id"] . ",'" . $con->real_escape_string($params["room-number"]) . "','" . $params["start-time"] . "','" . $params["end-time"] . "','" . $con->real_escape_string($params["reason"]) . "')");
                    $format_string = "<h1>您的入住申请已经被批准</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的入住房间" . $params["roomnumber"] . "申请被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>批准</b>。</p>";
                    send_mail($format_string, $email_to_info["email"], "您的账号入住申请被批准");
                    $_SESSION["message"] = "批准入住申请成功";
                } else {
                    $format_string = "<h1>您的入住申请被驳回</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的入住房间" . $params["roomnumber"] . "申请被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>驳回</b>。</p>";
                    send_mail($format_string, $email_to_info["email"], "您的账号入住申请被驳回");
                    $_SESSION["message"] = "驳回入住申请成功";
                }
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            $con->query("DELETE FROM `requests` WHERE `id` = " . $_REQUEST["req-id"] . " AND `type` = 'check-in'");
            header("Location: accept.php");
            break;
        case "register-allow":
            check_access(3);
            if (check_user_exists($_REQUEST["id"])) {
                if ($_REQUEST["action"] == "accept") {
                    $con->query("UPDATE `users` SET `actived` = 1 WHERE `id` = " . $_REQUEST["id"]);
                    $format_string = "<h1>您的账号注册申请已经被批准</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）已经被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>批准</b>。现在，您可以正式成为" . ($email_to_info["accessment"] == "staff" ? "员工" : "中级管理员") . "了。</p>";
                    send_mail($format_string, $email_to_info["email"], "您的账号已被批准");
                    $_SESSION["message"] = "成功批准" . $email_to_info["realname"] . "的请求";
                } else {
                    $format_string = "<h1>您的账号注册申请被驳回</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>驳回</b>。如果您需要再次发送申请邮件，请在管理界面申请重新发送。</p>";
                    send_mail($format_string, $email_to_info["email"], "您的账号注册申请被驳回");
                    $_SESSION["message"] = "成功驳回" . $email_to_info["realname"] . "的请求";
                }
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            $con->query("DELETE FROM `requests` WHERE `type` = 'register-allow' AND `id` = " . $_REQUEST["req-id"]);
            header("Location: accept.php");
            break;
        case "change-manage-allow":
            check_access(4);
            var_dump($_REQUEST["id"]);
            if (check_user_exists($_REQUEST["id"])) {
                if ($_REQUEST["action"] == "accept") {
                    $format_string = "<h1>您的权限更改申请已经被批准</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号权限更改申请（ID：" . $email_to_info["id"] . "）已经被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>批准</b>。</p>";
                    send_mail($format_string, $userinfo["email"], "您的权限更改申请已被批准");
                    $_SESSION["message"] = "成功批准" . $email_to_info["realname"] . "的请求";
                } else {
                    $format_string = "<h1>您的权限更改申请被驳回</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>驳回</b>。</p>";
                    send_mail($format_string, $userinfo["email"], "您的权限更改申请被驳回");
                    $_SESSION["message"] = "成功驳回" . $email_to_info["realname"] . "的请求";
                }
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            $con->query("DELETE FROM `requests` WHERE `type` = 'change-manage' AND `id` = " . $_REQUEST["req-id"]);
            header("Location: accept.php");
            break;
        case "upgrade-allow":
            check_access(4);
            if (check_user_exists($_REQUEST["id"])) {
                if ($_REQUEST["action"] == "accept") {
                    $con->query("UPDATE `users` SET accessment = 'admin' WHERE `id` = " . $_REQUEST["id"]);
                    $format_string = "<h1>您的账号升级申请已经被批准</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的升级申请已经被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>批准</b>。现在，您可以成为中级管理员了。</p>";
                    $_SESSION["message"] = "成功批准" . $email_to_info["realname"] . "的请求";
                    send_mail($format_string, $email_to_info["email"], "您的账号升级申请被批准");
                } else {

                    $format_string = "<h1>您的权限升级申请已经被批准</h1><p>尊敬的" . $email_to_info["realname"] . "，您的账号（ID：" . $email_to_info["id"] . "）的升级申请被管理员" . $userinfo['realname'] . "（ID：" . $userinfo['id'] . "）<b>驳回</b>。</p>";
                    $_SESSION["message"] = "成功驳回升级权限的请求";
                    send_mail($format_string, $email_to_info["email"], "您的账号升级申请被驳回");
                }
            } else {
                $_SESSION["message"] = "错误：用户不存在！";
            }
            $con->query("DELETE FROM `requests` WHERE `type` = 'upgrade-to-admin' AND `id` = " . $_REQUEST["req-id"]);
            header("Location: accept.php");
            break;
        default:
            header("Location: index.php");
            break;
    }
} else {
    $_SESSION["message"] = "请求错误！";
    header("Location: index.php");
}
