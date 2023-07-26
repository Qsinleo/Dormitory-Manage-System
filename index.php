<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMS - 宿舍管理系统</title>
    <link rel="stylesheet" href="./css/homepage.css">
    <link rel="stylesheet" href="./css/notSupportedBrowserAlert.css">
</head>

<body>
    <div id="main">
        <div id="notSupportedBrowerAlert">
            <h1 id="NSBA-title">警告:您正在使用不被支持的浏览器</h1>
            <div id="NSBA-SupportedBrowsersList">请使用IE9+,Firefox3.5+,Chrome4+,Safari3+,Opera10+,iOS Mobile Safari4.2+</div>
            <button id="NSBA-canel">忽略警告</button>
        </div>
        <h1>
            DMS 宿舍管理系统
        </h1>
        <table>
            <?php
            $con = mysqli_connect("localhost", "root", database: "dms-data");
            echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM `rooms` WHERE status='empty'"));
            ?>
        </table>
        <a href="login.php">登录</a>
        <a href="register.php">注册</a>
    </div>
    <script src="./js/isNotSupportedBrower.js"></script>
</body>

</html>