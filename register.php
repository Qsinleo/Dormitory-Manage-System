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
    <h1>注册</h1>
    <form action="proceed.php" method="post">
        <table>
            <tr>
                <td>真实姓名：</td>
                <td><input name="realname" maxlength="8" required /></td>
            </tr>
            <tr>
                <td>邮箱：</td>
                <td><input name="email" type="email" maxlength="40" required /></td>
                <td id="is-existed"></td>
            </tr>
            <tr>
                <td>密码：</td>
                <td><input name="password" type="password" required /></td>
                <td id="is-long"></td>
            </tr>
            <tr>
                <td>重新输入密码：</td>
                <td><input id="password-retype" type="password" required /></td>
                <td id="is-same"></td>
            </tr>
            <tr>
                <td>工作部门：</td>
                <td><input name="department" /></td>
            </tr>
            <tr>
                <td>工号：</td>
                <td><input name="workid" type="number" /></td>
            </tr>
            <tr>
                <td>
                    身份：
                </td>
                <td><select name="access" id="access">
                        <option value="staff" selected>员工</option>
                        <option value="admin">中级管理员</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">注册完成后需要<b>管理员</b>批准！</td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="注册" id="submit" />
                </td>
            </tr>
            <td>
            <td colspan="2" id="message"></td>
            </td>
        </table>
        <input type="hidden" name="type" value="register" />
    </form>
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
                <input type="text" id="login" class="fadeIn second" name="login" placeholder="login">
                <input type="text" id="password" class="fadeIn third" name="login" placeholder="password">
                <input type="submit" class="fadeIn fourth" value="Log In">
            </form>

            <!-- Remind Passowrd -->
            <div id="formFooter">
                <a class="underlineHover" href="#">Forgot Password?</a>
            </div>

        </div>
    </div>
</body>
<script src="js/required.js"></script>
<script src="js/isExistedEmail.js"></script>
<script src="js/verifyPasswordWhenRegister.js"></script>

</html>