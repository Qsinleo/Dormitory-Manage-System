<?php
require_once "header.php";
require_once "mail.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_REQUEST["type"] == "register") {
        //注册
        mysqli_query(
            $con,
            "INSERT INTO `users` VALUES (NULL,'" .
                mysqli_escape_string($con, $_REQUEST["email"]) .
                "','" .
                mysqli_escape_string($con, $_REQUEST["realname"])  .
                "',SHA1('" .
                mysqli_escape_string($con, $_REQUEST["password"])  .
                "')," .
                ($_REQUEST["workid"] == "" ? "NULL" : $_REQUEST["workid"]) .
                ",'" .
                mysqli_escape_string($con, $_REQUEST["department"])  .
                "','" .
                $_REQUEST["access"] .
                "',NULL,NULL,0,NULL,CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())"
        );
        $_SESSION["loginid"] = mysqli_fetch_row(mysqli_query($con, "SELECT last_insert_id()"))[0];
        $_SESSION["message"] = "注册成功";
        mysqli_query(
            $con,
            "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'register-allow','" . mysqli_escape_string($con, json_encode([
                "request-to-be" => $_REQUEST["access"]
            ])) . "',CURRENT_TIMESTAMP())"
        );
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "login") {
        //登录
        $login = mysqli_fetch_assoc(mysqli_query(
            $con,
            "SELECT * FROM `users` WHERE mail='" . $_REQUEST["mail"] . "'"
        ));
        if (sha1($_REQUEST["password"]) == $login["password"]) {
            $_SESSION["loginid"] = $login["id"];
            $_SESSION["message"] = "登录成功";
            mysqli_query(
                $con,
                "UPDATE `users` SET logintime = CURRENT_TIMESTAMP() WHERE id = " . $_SESSION["loginid"]
            );
            header("Location: manage.php");
        } else {
            $_SESSION["loginid"] = null;
            $_SESSION["message"] = "登录失败：账号或密码错误";
            header("Location: login.php");
        }
    } elseif ($_REQUEST["type"] == "queryemail") {
        //查询邮箱
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `users` WHERE mail='" . $_REQUEST["email"] . "'")) == 0) {
            echo "true";
        } else {
            echo "false";
        }
    } elseif (key_exists("delete-account", $_REQUEST)) {
        //账号删除
        $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
        $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
        $format_string = "<h1>您的账号已删除</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）<b>已被删除</b>。感谢你的使用，期待再会。</p>";
        send_mail($format_string, $send_to_info["mail"], "您的账号已被删除");
        mysqli_query($con, "DELETE FROM `requests` WHERE requestid=" . $_SESSION["loginid"]);
        mysqli_query($con, "DELETE FROM `users` WHERE id=" . $_SESSION["loginid"]);
        unset($_SESSION["loginid"]);
        $_SESSION["message"] = "账号删除成功";
        header("Location: index.php");
    } elseif ($_REQUEST["type"] == "change-header") {
        //改变头像
        mysqli_query($con, "UPDATE `users` SET header = '" . mysqli_escape_string($con, file_get_contents($_FILES['header']['tmp_name'])) . "' WHERE id = " . $_SESSION["loginid"]);
        $_SESSION["message"] = "头像更改成功";
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-realname") {
        //改变真名
        mysqli_query($con, "UPDATE `users` SET realname = '" . mysqli_escape_string($con, $_REQUEST["realname"]) . "' WHERE id = " . $_SESSION["loginid"]);
        $_SESSION["message"] = "真名成功更改为" . $_REQUEST["realname"];
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-workid") {
        //改变工号
        if ($_REQUEST["workid"] != "") {
            mysqli_query($con, "UPDATE `users` SET workid = '" . mysqli_escape_string($con, $_REQUEST["workid"]) . "' WHERE id = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "工号成功更改为" . $_REQUEST["workid"];
        } else {
            mysqli_query($con, "UPDATE `users` SET workid = NULL WHERE id = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "工号成功置空";
        }
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-depart") {
        //改变部门
        mysqli_query($con, "UPDATE `users` SET mail = '" . mysqli_escape_string($con, $_REQUEST["email"]) . "' WHERE id = " . $_SESSION["loginid"]);
        $_SESSION["message"] = "邮箱成功更改为" . $_REQUEST["email"];
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-password") {
        //改变密码
        if (sha1($_REQUEST["new-password"]) == mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]))["password"]) {
            mysqli_query($con, "UPDATE `users` SET department = '" . mysqli_escape_string($con, $_REQUEST["depart"]) . "' WHERE id = " . $_SESSION["loginid"]);
            $_SESSION["message"] = "密码成功更改";
        } else {
            $_SESSION["message"] = "密码更改失败：与原密码不符";
        }
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "register-allow") {
        mysqli_query($con, "DELETE FROM `requests` WHERE type = 'register-allow' AND requestid = " . $_REQUEST["id"]);
        if ($_REQUEST["action"] == "accept") {
            mysqli_query($con, "UPDATE `users` SET actived = 1 WHERE id = " . $_REQUEST["id"]);
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的账号已经被批准</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）已经被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>批准</b>。现在，您可以正式成为" . ($send_to_info["accessment"] == "staff" ? "员工" : "中级管理员") . "了。</p>";
            send_mail($format_string, $send_to_info["mail"], "您的账号已被批准");
            $_SESSION["message"] = "成功批准" . $send_to_info["realname"] . "的请求";
        } else {
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的账号注册申请被驳回</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>驳回</b>。如果您需要再次发送申请邮件，请在管理界面申请重新发送。</p>";
            send_mail($format_string, $send_to_info["mail"], "您的账号注册申请被驳回");
            $_SESSION["message"] = "成功驳回" . $send_to_info["realname"] . "的请求";
        }
        header("Location: accept.php");
    } elseif ($_REQUEST["type"] == "change-access") {
        //升级
        if ($_REQUEST["toaccess"] == "staff") {
            //无需批准
            mysqli_query($con, "UPDATE `users` SET accessment = 'staff' WHERE id = " . $_SESSION["loginid"]);
        } else {
            //需要批准
            mysqli_query($con, "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'upgrade-to-admin',NULL)");
            $_SESSION["message"] = "成功生成升级权限的请求";
        }
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "upgrade-allow") {
        //升级
        mysqli_query($con, "DELETE FROM `requests` WHERE type = 'upgrade-to-admin' AND requestid = " . $_REQUEST["id"]);
        if ($_REQUEST["action"] == "accept") {
            mysqli_query($con, "UPDATE `users` SET accessment = 'admin' WHERE id = " . $_REQUEST["id"]);
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限升级申请已经被批准</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）的升级申请已经被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>批准</b>。现在，您可以正式成为中级管理员了。</p>";
            $_SESSION["message"] = "成功批准" . $send_to_info["realname"] . "的请求";
            send_mail($format_string, $send_to_info["mail"], "您的账号已被升级");
        } else {
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限升级申请已经被批准</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）的升级申请被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>驳回</b>。</p>";
            $_SESSION["message"] = "成功驳回升级权限的请求";
            send_mail($format_string, $send_to_info["mail"], "您的账号升级申请被驳回");
        }
        header("Location: accept.php");
    } elseif ($_REQUEST["type"] == "request-register-allow") {
        mysqli_query(
            $con,
            "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'register-allow','" . mysqli_escape_string($con, json_encode([
                "request-to-be" => $_REQUEST["request-to-be"]
            ])) . "',CURRENT_TIMESTAMP())"
        );
        $_SESSION["message"] = "提交申请成功，请查看邮箱以了解后续进展";
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "queryroom") {
        //查询邮箱
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `rooms` WHERE `number`=" . $_REQUEST["roomnumber"])) != 0) {
            echo "true";
        } else {
            echo "false";
        }
    } elseif ($_REQUEST["type"] == "change-manage") {
        //更改管理房间
        mysqli_query($con, "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'change-manage','" . mysqli_escape_string($con, json_encode([
            "change-to-rooms" => explode(",", $_REQUEST["room-data"])
        ])) . "',CURRENT_TIMESTAMP())");
        $_SESSION["message"] = "成功生成更改管理区域的请求，请等待系统管理员批准";
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-manage-allow") {
        if ($_REQUEST["action"] == "accept") {
            $new_change = [];
            foreach (json_decode(mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `requests` where `type` = 'change-manage' AND requestid = " . $_REQUEST["id"]))["param"], true)["change-to-rooms"] as $value) {
                array_push($new_change, mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where `number` = " . $value))["id"]);
            }
            mysqli_query($con, "UPDATE `users` SET managepartid = " .
                (array_count_values($new_change) != 0 ? "'" . implode(",", $new_change) . "'" : "NULL") .
                " WHERE id = " . $_REQUEST["id"]);
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限更改申请已经被批准</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）已经被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>批准</b>。</p>";
            send_mail($format_string, $from_info["mail"], "您的账号已被批准");
            $_SESSION["message"] = "成功批准" . $send_to_info["realname"] . "的请求";
        } else {
            $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限更改申请被驳回</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）被管理员（ID：" . $from_info['id'] . "）" . $from_info['realname'] . "<b>驳回</b>。如果您需要再次发送申请邮件，请在管理界面申请重新发送。</p>";
            send_mail($format_string, $from_info["mail"], "您的权限更改申请被驳回");
            $_SESSION["message"] = "成功驳回" . $send_to_info["realname"] . "的请求";
        }
        mysqli_query($con, "DELETE FROM `requests` WHERE type = 'change-manage' AND requestid = " . $_REQUEST["id"]);
        header("Location: accept.php");
    } elseif ($_REQUEST["type"] == "delete-room") {
        //房间删除
        mysqli_query($con, "DELETE FROM `requests` WHERE type = 'change-manage' AND `param` like '%\\\"" . mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` WHERE id = " . $_REQUEST["id"]))["number"] . "\\\"%'");
        mysqli_query($con, "UPDATE `users` SET managepartid = NULL WHERE managepartid = " . $_REQUEST["id"]);
        foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `users` WHERE managepartid like '%," . $_REQUEST["id"] . "%'"), MYSQLI_ASSOC) as $value) {
            $manage_now = explode(",", $value["managepartid"]);
            unset($manage_now[array_search($_REQUEST["id"], $manage_now)]);
            mysqli_query($con, "UPDATE `users` SET managepartid = '" . implode(",", $manage_now) . "' WHERE id = " . $value["id"]);
        }
        mysqli_query($con, "DELETE FROM `rooms` WHERE id = " . $_REQUEST["id"]);
        $_SESSION["message"] = "房间删除成功";
        header("Location: roomlist.php");
    } elseif ($_REQUEST["type"] == "delete-user") {
        //系统管理员删除账号
        $from_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
        $send_to_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
        $format_string = "<h1>您的账号已被管理员删除</h1><p>尊敬的" . $send_to_info["realname"] . "，您的账号（ID：" . $send_to_info["id"] . "）<b>已被删除</b>。感谢你的使用，期待再会。</p>";
        send_mail($format_string, $send_to_info["mail"], "您的账号已被管理员删除");
        mysqli_query($con, "DELETE FROM `requests` WHERE requestid=" . $_SESSION["loginid"]);
        mysqli_query($con, "DELETE FROM `users` WHERE id=" . $_SESSION["loginid"]);
        unset($_SESSION["loginid"]);
        $_SESSION["message"] = "删除账号成功";
        header("Location: userlist.php");
    } elseif ($_REQUEST["type"] == "add-room") {
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `rooms` WHERE `number` = " . $_REQUEST["room-number"])) == 0) {
            mysqli_query($con, "INSERT INTO `rooms` VALUES (NULL," . $_REQUEST["room-number"] . ",'empty',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())");
            $_SESSION["message"] = "添加房间成功";
        } else {
            $_SESSION["message"] = "添加房间失败：房间重复！";
        }
        header("Location: roomlist.php");
    } elseif ($_REQUEST["type"] == "querymanage") {
        $res = mysqli_fetch_assoc(mysqli_query($con, "SELECT managepartid FROM `users` WHERE id = " . $_REQUEST["id"]))["managepartid"];
        if (is_null($res)) {
            echo "null";
        } else {
            foreach (explode(",", $res) as $value) {
                echo mysqli_fetch_assoc(mysqli_query($con, "SELECT `number` FROM `rooms` WHERE id = " . $value))["number"], ",";
            }
        }
    } elseif ($_REQUEST["type"] == "change-user-manage") {
        $manage_now = [];
        if ($_REQUEST["room-data"] != "") {
            foreach (explode(",", $_REQUEST["room-data"]) as $value) {
                array_push($manage_now, mysqli_fetch_assoc(mysqli_query($con, "SELECT `number` FROM `rooms` WHERE id = " . $value))["id"]);
            }
        }
        mysqli_query($con, "UPDATE `users` SET managepartid = " . ($manage_now == [] ? "NULL" : "'" . implode(",", $manage_now) . "'") . " WHERE id = " . $_REQUEST["setid"]);
        $_SESSION["message"] = "更改权限成功";
        header("Location: userlist.php");
    } elseif ($_REQUEST["type"] == "request-check-in") {
        mysqli_query($con, "INSERT INTO `requests` VALUES (NULL," . $_SESSION["loginid"] . ",'check-in','" . mysqli_escape_string($con, json_encode(
            [
                "start-time" => $_REQUEST["start-time"],
                "end-time" => $_REQUEST["end-time"],
                "roomnumber" => $_REQUEST["room-num"],
                "reason" => $_REQUEST["reason"]
            ]
        )) . "',CURRENT_TIMESTAMP())");
        $_SESSION["message"] = "提交入住申请成功";
        header("Location: roomlist.php");
    } elseif ($_REQUEST["type"] == "check-in-allow") {
        $params = json_decode(mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_REQUEST["id"] . " AND `type` = 'check-in'"))["param"], true);
        mysqli_query($con, "INSERT INTO `checkios` VALUES (NULL," . $_REQUEST["id"] . "," . mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` WHERE `number` = " . $params["roomnumber"]))["id"] . ",'" . $params["start-time"] . "','" . $params["end-time"] . "','" . mysqli_escape_string($con, $params["reason"]) . "')");
        mysqli_query($con, "DELETE FROM `requests` WHERE requestid = " . $_REQUEST["id"] . " AND `type` = 'check-in'");
        mysqli_query($con, "UPDATE `rooms` SET `status` = 'occupied' WHERE `number` = " . $params["roomnumber"]);
        $_SESSION["message"] = "批准入住申请成功";
        header("Location: accept.php");
    } elseif ($_REQUEST["type"] == "check-out") {
        mysqli_query($con, "DELETE FROM `checkios` WHERE requestid = " . $_SESSION["loginid"]);
        $_SESSION["message"] = "成功办理签出";
        header("Location: roomlist.php");
    }
}
