<?php
require_once "header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>登录</title>
</head>

<body>
    <h1>登录</h1>
    <form action="proceed.php" method="post">
        <table>
            <tr>
                <td>邮箱：</td>
                <td><input type="email" required name="mail" /></td>
            </tr>
            <tr>
                <td>密码：</td>
                <td><input type="password" required name="password" /></td>
            </tr>
        </table>
        <input type="hidden" value="login" name="type" />
        <input type="submit" />
    </form>
</body>
<script src="js/required.js"></script>

</html>