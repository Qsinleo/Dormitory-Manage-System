<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <title>DMS - 宿舍管理系统</title>
</head>

<body>
    <h1>DMS宿舍管理系统</h1>
    <div>
        <h2>宿舍列表</h2>
        <table>
            <?php
            $con = mysqli_connect("localhost", "root", database: "dms-data");
            var_dump(mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `rooms`")));
            ?>
        </table>
    </div>
</body>

</html