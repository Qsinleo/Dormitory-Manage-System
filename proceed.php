<?php
require_once "mysqlConnect.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_REQUEST["type"] == "register") {
        //注册
        mysqli_query(
            $con,
            "INSERT INTO `users` VALUES (NULL,'" .
                $_REQUEST["email"] .
                "','" .
                $_REQUEST["real-name"] .
                "',SHA1('" .
                $_REQUEST["password"] .
                "')," .
                ($_REQUEST["workid"] == "" ? "NULL" : $_REQUEST["workid"]) .
                ",'" .
                $_REQUEST["department"] .
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
    }
}
