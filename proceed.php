<?php
require_once "mysqlConnect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_REQUEST["type"] == "register") {
        mysqli_query(
            $con,
            "INSERT INTO `users` VALUES (NULL,'" .
                $_REQUEST["email"] .
                "','" .
                $_REQUEST["real-name"] .
                "',MD5('" .
                $_REQUEST["password"] .
                "')," .
                ($_REQUEST["workid"] == "" ? "NULL" : $_REQUEST["workid"]) .
                ",'" .
                $_REQUEST["department"] .
                "','staff',NULL,NULL)"
        );
    }
}
