<?php
require_once "header.php";
include_once "navpage.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/pages/register.css">
</head>

<body>
    <main>
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->
                <h2 class="inactive underlineHover" onclick="location.href='login.php'">登录</h2>
                <h2 class="active">注册</h2>

                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="./img/biLogo.svg" id="icon" alt="User Icon" />
                </div>

                <!-- Login Form -->
                <form action="proceed.php" method="post">
                    <input name="type" value="register" type="hidden">
                    <input type="text" name="realname" maxlength="8" placeholder="*真名" required />
                    <input name="email" type="email" maxlength="40" placeholder="*邮箱" required />
                    <div id="is-existed"></div>
                    <input name="password" type="password" placeholder="*密码" required />
                    <div id="is-long"></div>
                    <input id="password-retype" type="password" placeholder="*重复密码" required />
                    <div id="is-same"></div>
                    <input type="text" name="department" placeholder="部门" />
                    <input name="workid" type="number" placeholder="工号" />
                    <select name="access" id="access">
                        <option value="staff" selected>员工</option>
                        <option value="admin">中级管理员</option>
                    </select>
                    <input type="submit" id="submit" class="fadeIn fourth" value="注册">
                </form>

                <!-- Remind Passowrd -->
                <div id="formFooter">
                    注册完成后需要<b>管理员</b>批准！
                </div>

            </div>
        </div>
    </main>

</body>
<script src="js/isExistedEmail.js"></script>
<script src="js/verifyPasswordWhenRegister.js"></script>

</html>