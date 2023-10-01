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
                <h2 class="inactive underlineHover"> Sign In </h2>
                <h2 class="active"> Sign Up </h2>

                <!-- Icon -->
                <div class="fadeIn first">
                    <img src="./img/biLogo.svg" id="icon" alt="User Icon" />
                </div>

                <!-- Login Form -->
                <form>
                    <input type="text" name="realname" maxlength="8" placeholder="真名" />
                    <input name="email" type="email" maxlength="40" placeholder="邮箱" />
                    <input name="password" type="password" placeholder="密码" />
                    <input id="password-retype" type="password" placeholder="重复密码" />
                    <input type="text" name="department" placeholder="部门" />
                    <input name="workid" type="number" placeholder="工号" />
                    <select name="access" id="access">
                        <option value="staff" selected>员工</option>
                        <option value="admin">中级管理员</option>
                    </select>
                    <input type="submit" class="fadeIn fourth" value="注册">
                </form>

                <!-- Remind Passowrd -->
                <div id="formFooter">
                    注册完成后需要<b>管理员</b>批准！
                </div>

            </div>
        </div>
    </main>

</body>
<script src="js/required.js"></script>
<script src="js/isExistedEmail.js"></script>
<script src="js/verifyPasswordWhenRegister.js"></script>

</html>