<?php

try {
    require_once "env_config.php";
} catch (\Throwable $th) {
    header("Location: install.php");
}
// 连接数据库
$con = new mysqli(db_host, db_username, db_password, db_databasename);
// 开始会话
session_start();
// 检测登录信息
if (!array_key_exists("loginid", $_SESSION)) {
    $_SESSION["loginid"] = null;
}
if (!array_key_exists("message", $_SESSION)) {
    $_SESSION["message"] = null;
}
// $_SESSION["message"] = "测试123456748";

$usertype = null;

if (!is_null($_SESSION["loginid"])) {
    $userinfo = $con->query("SELECT * FROM `users` where `id` = " . $_SESSION["loginid"])->fetch_assoc();
    if (!is_null($userinfo)) {
        if ($userinfo["actived"] == 0) {
            $usertype = "inactived";
        } else {
            $usertype = $userinfo["accessment"];
        }
    } else {
        $usertype = null;
        $_SESSION["loginid"] = null;
    }
}
