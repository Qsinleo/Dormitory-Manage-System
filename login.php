<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <title>登录</title>
</head>

<body>
    <h1>登录</h1>
    <div class="error"><?php if ($_SESSION["loginas"] != null && $_SESSION["loginas"] == "failed") echo "登录失败，账号或密码错误"; ?></div>
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

</html>