<?php
session_start();
unset($_SESSION["loginid"]);
$_SESSION["message"]  = "退出登录成功";
header("Location: index.php");
