<?php
require_once "mysqlConnect.php";
require_once "mail.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (key_exists("logout", $_REQUEST)) {
        session_destroy();
        header("Location: index.php");
    } else
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
                "',NULL,NULL,0,NULL)"
        );
        $_SESSION["loginid"] = mysqli_fetch_row(mysqli_query($con, "SELECT last_insert_id()"))[0];
        $_SESSION["loginas"] = $_REQUEST["access"];
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "login") {
        //登录
        $login = mysqli_fetch_assoc(mysqli_query(
            $con,
            "SELECT * FROM `users` WHERE mail='" . $_REQUEST["mail"] . "'"
        ));
        if (sha1($_REQUEST["password"]) == $login["password"]) {
            $_SESSION["loginas"] = $login["accessment"];
            $_SESSION["loginid"] = $login["id"];
            header("Location: manage.php");
        } else {
            $_SESSION["loginas"] = "failed";
            $_SESSION["loginid"] = null;
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

        $value = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
        $tovalue = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
        $format_string = "<h1>您的账号已删除</h1><p>尊敬的" . $tovalue["realname"] . "，您的账号（ID：" . $tovalue["id"] . "）<b>已被删除</b>。感谢你的使用，期待再会。</p>";
        send_mail($format_string, $value["mail"], "您的账号删除成功");
        mysqli_query($con, "DELETE FROM `upgrade` WHERE requestid=" . $_SESSION["loginid"]);
        mysqli_query($con, "DELETE FROM `users` WHERE id=" . $_SESSION["loginid"]);
        session_destroy();
        header("Location: index.php");
    } elseif ($_REQUEST["type"] == "change-header") {
        //改变头像
        mysqli_query($con, "UPDATE `users` SET header = '" . mysqli_escape_string($con, file_get_contents($_FILES['header']['tmp_name'])) . "' WHERE id = " . $_SESSION["loginid"]);
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-realname") {
        //改变真名
        mysqli_query($con, "UPDATE `users` SET realname = '" . mysqli_escape_string($con, $_REQUEST["realname"]) . "' WHERE id = " . $_SESSION["loginid"]);
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-workid") {
        //改变工号
        if ($_REQUEST["workid"] != "")
            mysqli_query($con, "UPDATE `users` SET workid = '" . mysqli_escape_string($con, $_REQUEST["workid"]) . "' WHERE id = " . $_SESSION["loginid"]);
        else
            mysqli_query($con, "UPDATE `users` SET workid = NULL WHERE id = " . $_SESSION["loginid"]);
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-depart") {
        //改变部门
        mysqli_query($con, "UPDATE `users` SET department = '" . mysqli_escape_string($con, $_REQUEST["depart"]) . "' WHERE id = " . $_SESSION["loginid"]);
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "change-password") {
        //改变密码
        if (sha1($_REQUEST["new-password"]) == mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]))["password"])
            mysqli_query($con, "UPDATE `users` SET department = '" . mysqli_escape_string($con, $_REQUEST["depart"]) . "' WHERE id = " . $_SESSION["loginid"]);
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "register-allow") {
        if ($_REQUEST["action"] == "accept") {
            mysqli_query($con, "UPDATE `users` SET actived = 1 WHERE id = " . $_REQUEST["id"]);
            $value = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $tovalue = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的账号已经被批准</h1><p>尊敬的" . $tovalue["realname"] . "，您的账号（ID：" . $tovalue["id"] . "）已经被管理员（ID：" . $value['id'] . "）" . $value['realname'] . "<b>批准</b>。现在，您可以正式成为" . ($tovalue["accessment"] == "staff" ? "员工" : "中级管理员") . "了。</p>";
            send_mail($format_string, $value["mail"], "您的账号已被批准");
        } else {
            $value = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $tovalue = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的账号注册申请被驳回</h1><p>尊敬的" . $tovalue["realname"] . "，您的账号（ID：" . $tovalue["id"] . "）被管理员（ID：" . $value['id'] . "）" . $value['realname'] . "<b>驳回</b>。如果您需要再次发送申请邮件，请先删除账号再重新注册。</p>";
            send_mail($format_string, $value["mail"], "您的账号注册申请被驳回");
        }
        header("Location: acception.php");
    } elseif ($_REQUEST["type"] == "change-access") {
        //升级
        if ($_REQUEST["toaccess"] == "staff") {
            //无需批准
            mysqli_query($con, "UPDATE `users` SET accessment = 'staff' WHERE id = " . $_SESSION["loginid"]);
        } else {
            //需要批准
            mysqli_query($con, "INSERT INTO `upgrade` VALUES (NULL," . $_SESSION["loginid"] . ")");
        }
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "upgrade-allow") {
        //升级
        if ($_REQUEST["action"] == "accept") {
            mysqli_query($con, "DELETE FROM `upgrade` WHERE requestid = " . $_REQUEST["id"]);
            mysqli_query($con, "UPDATE `users` SET accessment = 'admin' WHERE id = " . $_REQUEST["id"]);
            $value = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $tovalue = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限升级申请已经被批准</h1><p>尊敬的" . $tovalue["realname"] . "，您的账号（ID：" . $tovalue["id"] . "）的升级申请已经被管理员（ID：" . $value['id'] . "）" . $value['realname'] . "<b>批准</b>。现在，您可以正式成为中级管理员了。</p>";
            send_mail($format_string, $value["mail"], "您的账号已被升级");
        } else {
            $value = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
            $tovalue = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_REQUEST["id"]));
            $format_string = "<h1>您的权限升级申请已经被批准</h1><p>尊敬的" . $tovalue["realname"] . "，您的账号（ID：" . $tovalue["id"] . "）的升级申请被管理员（ID：" . $value['id'] . "）" . $value['realname'] . "<b>驳回</b>。</p>";
            send_mail($format_string, $value["mail"], "您的账号升级申请被驳回");
        }

        header("Location: acception.php");
    }
}
