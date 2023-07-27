<?php
require_once "mysqlConnect.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>注册</h1>
    <form action="proceed.php" method="post">
        <fieldset>
            <label>真实姓名：<input name="real-name" maxlength="8" /></label>
            <label>邮箱：<input name="email" type="email" maxlength="40" required />
            </label>
            <label>密码：<input name="password" type="password" required /></label>
        </fieldset>
        <fieldset>
            <label>工作部门：
                <!-- 长度100% -->
                <input name="department" />
            </label>
            <label>工号：
                <input name="workid" type="number" />
            </label>
        </fieldset>
        <fieldset>
            <select>
                <option value="staff" selected>员工</option>
                <option value="admin">中级管理员</option>
            </select>
        </fieldset>
        <input type="hidden" name="type" value="register" />
        <input type="submit" />
    </form>
</body>

</html>