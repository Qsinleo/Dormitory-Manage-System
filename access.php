<?php
include_once "embed/sidenav.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/pages/access.css">

    <title>登录 / 注册</title>
</head>

<body>
    <main>
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->
                <h2 class="active tabs underline-hover"> 登录 </h2>
                <h2 class="inactive tabs underline-hover"> 注册 </h2>
                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="img/stuff.webp" id="icon" alt="User Icon" />
                </div>
                <div id="main-form-container">
                    <div>
                        <!-- Login Form -->
                        <form action="process.php" method="post">
                            <input type="hidden" value="login" name="type" />
                            <input type="text" id="login" class="fadeIn second" required name="email" placeholder="邮箱">
                            <input type="password" id="password" class="fadeIn third" required name="password" placeholder="密码">
                            <input type="submit" class="fadeIn fourth" value="登录">
                        </form>

                        <!-- Remind Passowrd -->
                        <div id="formFooter">
                            <a class="underline-hover" href="#">忘记密码</a>
                        </div>
                    </div>
                    <div>
                        <form action="process.php" method="post">
                            <input name="type" value="register" type="hidden">
                            <input type="text" name="realname" maxlength="8" placeholder="*真名" required class="fadeIn first" />
                            <input name="email" type="email" maxlength="40" placeholder="*邮箱" required class="fadeIn first" id="email-input" />
                            <div id="email-is-existed"></div>
                            <input name="password" type="password" placeholder="*密码" required class="fadeIn second" id="register-password" />
                            <div id="password-info"></div>
                            <input id="password-retype" type="password" placeholder="*重复密码" required class="fadeIn second" />
                            <input type="text" name="department" placeholder="部门" class="fadeIn third" />
                            <input name="workid" type="text" placeholder="工号" class="fadeIn third" />
                            <select name="access" id="access" class="fadeIn third">
                                <option value="staff" selected>员工</option>
                                <option value="admin">中级管理员</option>
                            </select>
                            <input type="submit" id="register-submit" class="fadeIn fourth" value="注册">
                        </form>
                        <!-- Remind Passowrd -->
                        <div id="formFooter">
                            注册完成后需要<b>管理员</b>批准！
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/xhr.js"></script>
    <script src="js/register.js"></script>

</body>

</html>