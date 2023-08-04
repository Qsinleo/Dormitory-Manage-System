<?php
require_once "mysqlConnect.php";
session_start();
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
        mysqli_query($con, "DELETE FROM `users` WHERE id=" . $_SESSION["loginid"]);
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
    }
}
