<?php
require_once "header.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DMS - 宿舍管理系统</title>
    <!--- Link CSS --->
    <link rel="stylesheet" href="./css/homepage.css">
    <link rel="stylesheet" href="./css/nav.css">
    <!--- 图标组件库 --->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>

<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="" alt="">
                </span>
                <div class="text logo-text">
                    <span class="name">Phoenix</span>
                    <span class="profession">Web Dev</span>
                </div>
            </div>
            <i class="bx bx-chevron-right toggle"></i>
        </header>
        <div class="menu-bar">
            <div class="menu">
                <li class="seach-box">
                    <i class="bx bx-search icon"></i>
                    <input type="text">
                </li>
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="#">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="main">
        <section id="title">
            <!-- Title -->
            <div style="font-size:2em;">D M S</div>
            <div style="font-size: 0.25em !important;font-weight: bold !important;">
                宿 舍 管 理 系 统
            </div>
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
            if (!is_null($usertype) && $usertype != "failed") {
            ?>
                <button id="book" class="buttons">
                    预定房间
                </button>
                <button id="checkout" class="buttons">
                    退还房间
                </button>
                <button id="manage" class="buttons">
                    管理我的账号
                </button>
                <?php
                if ($usertype != "inactived") {
                ?>
                    <button id="accept" class="buttons">
                        批准
                    </button>
                <?php
                }
                ?>


            <?php
            } else {
            ?>
                <button id="login" onclick='document.location="/login.php"' class="buttons">
                    登录
                </button>
                <button id="register" onclick='document.location="/register.php"' class="buttons">
                    注册
                </button>
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
            document.getElementById("remain").style.background = "rgb(186, 255, 181)";
        } else if (remain.innerText / total.innerText >= 0.25) {
            document.getElementById("remain").style.background = "rgb(255, 222, 181)"
        } else {
            document.getElementById("remain").style.background = "rgb(255, 181, 181)"
        }
    </script>
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