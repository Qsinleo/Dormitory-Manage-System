<?php
require_once "mysqlConnect.php";
session_start();
if (!array_key_exists("loginas", $_SESSION)) {
    $_SESSION["loginas"] = null;
}
if (!array_key_exists("loginid", $_SESSION)) {
    $_SESSION["loginid"] = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMS - 宿舍管理系统</title>
    <!--- Link CSS --->
    <link rel="stylesheet" href="./css/homepage.css">
    <link rel="stylesheet" href="./css/nav.css">
</head>

<body>
    <div id="main">
        <section id="title">
            <div style="scale:0.5;"><span class="st">D</span>ormitory
                &nbsp;<span class="st">M</span>anage
                &nbsp;<span class="st">S</span>ystem</div>

        </section>
        <section id="rooms">
            <span style="text-align:center;display:block;font-size:2em;font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">房间</span>
            <div id="remain">
                <div style="text-align:center;margin-top:70px;">
                    剩余房间:
                    <div id="remainNum">
                        <?php
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
            <?php
            /*
            null：访客
            */
            if ($_SESSION["loginas"] != null && $_SESSION["loginas"] != "failed") {
            ?>
                <div id="book" class="buttons">
                    预定房间
                </div>
                <div id="checkout" class="buttons">
                    退还房间
                </div>
            <?php
            } else {
            ?>
                <div id="login" onclick='document.location="/login.php"' class="buttons">
                    登录
                </div>
                <div id="register" onclick='document.location="/register.php"' class="buttons">
                    注册
                </div>
            <?php
            }
            ?>
        </section>

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
<!--- NSBA SCRIPT --->
<script>
    function getBrowserVersion() {
        var userAgent = window.navigator.userAgent;

        // IE
        if (/MSIE|Trident/.test(userAgent)) {
            window.location.href = 'error_browser.html'; //遇到IE 就放弃
        }

        // Chrome
        if (/Chrome\/(\d+)/.test(userAgent)) {
            var chromeVersion = parseInt(RegExp.$1);
            var minimumChromeVersion = 70; // 设置最低支持的版本

            if (chromeVersion < minimumChromeVersion) {
                window.location.href = 'error_browser.html';
            }
        }

        // Firefox
        if (/Firefox\/(\d+)/.test(userAgent)) {
            var firefoxVersion = parseInt(RegExp.$1);
            var minimumFirefoxVersion = 60; // 设置最低支持的版本

            if (firefoxVersion < minimumFirefoxVersion) {
                window.location.href = 'error_browser.html';
            }
        }

        // Safari
        if (/Safari\/(\d+)/.test(userAgent)) {
            var safariVersion = parseInt(RegExp.$1);
            var minimumSafariVersion = 12; // 设置最低支持的版本
            if (safariVersion < minimumSafariVersion) {
                window.location.href = 'error_browser.html';
            }
        }

        // Edge
        if (/Edg\/(\d+)/.test(userAgent)) {
            var edgeVersion = parseInt(RegExp.$1);
            var minimumEdgeVersion = 80; // 设置最低支持的版本

            if (edgeVersion < minimumEdgeVersion) {
                window.location.href = 'error_browser.html';
            }
        }

        // Opera
        if (/OPR\/(\d+)/.test(userAgent)) {
            var operaVersion = parseInt(RegExp.$1);
            var minimumOperaVersion = 60; // 设置最低支持的版本

            if (operaVersion < minimumOperaVersion) {
                window.location.href = 'error_browser.html';
            }
        }
    }

    window.onload = function() {
        getBrowserVersion();

    };
</script>

</html>