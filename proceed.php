<?php
require_once "mysqlConnect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_REQUEST["type"] == "register"){
        mysqli_query("INSERT INTO `users` VALUES ($_REQUEST[")")
    }
}?>