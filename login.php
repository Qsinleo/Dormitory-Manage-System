<?php
require_once "header.php";
include_once "navpage.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/pages/login.css">

    <title>登录</title>
</head>

<body>
    <main>
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->
                <h2 class="active" style="cursor: pointer;"> 登录 </h2>
                <h2 class="inactive underlineHover" style="cursor: pointer;" onclick="window.location='register.php'"> 注册 </h2>

                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="http://danielzawadzki.com/codepen/01/icon.svg" id="icon" alt="User Icon" />
                </div>

                <!-- Login Form -->
                <form action="proceed.php" method="post">
                    <input type="hidden" value="login" name="type" />

                    <input type="text" id="login" class="fadeIn second" name="mail" placeholder="邮箱">
                    <input type="text" id="password" class="fadeIn third" name="password" placeholder="密码">
                    <input type="submit" class="fadeIn fourth" value="登录">
                </form>

                <!-- Remind Passowrd -->
                <div id="formFooter">
                    <a class="underlineHover" href="#">忘记密码</a>
                </div>

            </div>
        </div>
    </main>
</body>
<!-- <script src="js/required.js"></script> -->

</html>