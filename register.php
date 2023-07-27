<?php
require_once "mysqlConnect.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/register.css">
</head>

<body>
    <h1>注册</h1>
    <form action="proceed.php" method="post">
        <table>
            <tr>
                <td>真实姓名：</td>
                <td><input name="real-name" maxlength="8" /></td>
            </tr>
            <tr>
                <td>邮箱：</td>
                <td><input name="email" type="email" maxlength="40" required /></td>
                <td id="is-existed"></td>
            </tr>
            <tr>
                <td>密码：</td>
                <td><input name="password" type="password" required /></td>

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
</body>
<script src="js/required.js"></script>
<script src="js/isExistedEmail.js"></script>

</html>