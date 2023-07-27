<?php
require_once "mysqlConnect.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_REQUEST["type"] == "register") {
        echo  "INSERT INTO `users` VALUES (NULL,'" .
            $_REQUEST["email"] .
            "','" .
            $_REQUEST["real-name"] .
            "',MD5('" .
            $_REQUEST["password"] .
            "')," .
            ($_REQUEST["workid"] == "" ? "NULL" : $_REQUEST["workid"]) .
            ",'" .
            $_REQUEST["department"] .
            "','" .
            $_REQUEST["access"] .
            "',NULL,NULL,0,NULL)";
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
                "',NULL,NULL,0)"
        );
        $_SESSION["loginid"] = mysqli_fetch_row(mysqli_query($con, "SELECT last_insert_id()"))[0];
        mysqli_query(
            $con,
            "INSERT INTO `messages` VALUES (NULL," .
                json_encode([
                    "fromid" => $_SESSION["loginid"],
                    "to" => ($_REQUEST["access"] == "staff" ? "admin" : "system-admin"),
                    "content" => "active-asking"
                ]) .
                ")"
        );
        header("Location: manage.php");
    } elseif ($_REQUEST["type"] == "login") {
        $login = mysqli_fetch_assoc(mysqli_query(
            $con,
            "SELECT * FROM `users` WHERE mail=" . $_REQUEST["mail"]
        ));
        if (sha1($_REQUEST["password"]) == $login["password"]) {
            $_SESSION["loginas"] = $login["access"];
            header("Location: manage.php");
        } else {
            header("Locatio: index.php");
        }
    } elseif ($_REQUEST["type"] == "queryemail") {
        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `users` WHERE mail='" . $_REQUEST["email"] . "'")) == 0) {
            echo "true";
        } else {
            echo "false";
        }
    }
}
