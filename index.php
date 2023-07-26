<?php
require_once "mysqlConnect.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMS - 宿舍管理系统</title>
    <!--- Link CSS --->
    <link rel="stylesheet" href="./css/homepage.css">
    <link rel="stylesheet" href="./css/notSupportedBrowserAlert.css">
    <link rel="stylesheet" href="./css/nav.css">
</head>

<body>
    <!--- Nav --->
    <iframe src="/nav/index.html" frameborder="0" style="height:48px;width:100vw;" id="main-nav" scrolling="no"></iframe>
    <div id="main">
        <!--- NSBA --->
        <div id="notSupportedBrowerAlert">
            <h1 id="NSBA-title">警告:您正在使用不被支持的浏览器</h1>
            <div id="NSBA-SupportedBrowsersList">请使用IE9+,Firefox3.5+,Chrome4+,Safari3+,Opera10+,iOS Mobile
                Safari4.2+
            </div>
            <div style="text-align: center;"><button id="NSBA-canel">忽略警告</button></div>
        </div>
        <h1 class="title">
            DMS 宿舍管理系统
        </h1>
        <div id="rooms">
            <div id="remain">
                <div style="text-align:center;margin-top:70px;">
                    剩余房间:
                    <div id="remainNum">
                        <?php
                        $con = mysqli_connect("localhost", "root", password: "123456", database: "dms-data");
                        echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM `rooms` WHERE status='empty'"));
                        ?>
                    </div>
                </div>

            </div>
            <div id="total">
                <div style="text-align:center;margin-top:70px;">
                    房间总数:
                    <div id="totalNum">
                        <?php
                        echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM `rooms`"));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--- Scripts Below --->
    <script>
        var remain = document.getElementById("remainNum");
        var total = document.getElementById("totalNum");
        // console.log(remain.innerText+total.innerText);
        if (remain.innerText / total.innerText >= 0.75) {
            console.log(remain.innerText / total.innerText);
            document.getElementById("remain").style.background = "rgb(186, 255, 181)";
        } else if (remain.innerText / total.innerText >= 0.25) {
            console.log(remain.innerText / total.innerText);
            document.getElementById("remain").style.background = "rgb(255, 222, 181)"
        } else {
            console.log(remain.innerText / total.innerText);
            document.getElementById("remain").style.background = "rgb(255, 181, 181)"
        }
    </script>
    <script src="./js/isNotSupportedBrower.js"></script>
</body>

</html>