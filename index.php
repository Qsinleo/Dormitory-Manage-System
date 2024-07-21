<?php
require_once "embed/sidenav.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethroom [<?php echo service_title; ?>]</title>
    <!--- Link CSS --->
    <link rel="stylesheet" href="css/pages/homepage.css">
</head>

<body>
    <!-- Main -->
    <main>
        <div id="title">
            <!-- Title -->
            <div style="width: 40%; height: 65vh; left: 30vw; top: 15vh; position: absolute; background: linear-gradient(271deg, #541FA9 0%, rgba(83.58, 31.36, 168.79, 0) 100%);; border-radius: 110px"></div>
            <div id="title-text" style="left: 16vh; top: 27vh; position: absolute; color: white; font-size: 5em; font-family: Inter; font-weight: 400; word-wrap: break-word">EthRoom</div>
            <div id="title-text2" style="height: 11vh; left: 17vw; top: 51vh; position: absolute; color: rgba(255, 255, 255, 0.40); font-size: 50px; font-family: Inter; font-weight: 400; letter-spacing: 20px; word-wrap: break-word">
                <?php echo service_title; ?>
            </div>
        </div>
        <section id="rooms">
            <span id="rooms-title">房间</span>
            <div id="remain">
                <div style="text-align:center;margin-top:70px;">
                    剩余房间:
                    <div id="remainNum">
                        <?php
                        echo $con->query("SELECT * FROM `rooms` WHERE `status` = 'normal'")->num_rows;
                        ?>
                    </div>
                </div>
            </div>
            <div id="total">
                <div style="text-align:center;margin-top:70px;">
                    房间总数:
                    <div id="totalNum">
                        <?php
                        echo $con->query("SELECT * FROM `rooms`")->num_rows;
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
                <button class="buttons" data-href="room-list.php">
                    预定房间/退还房间
                </button>
                <button class="buttons" data-href="manage.php">
                    管理我的账号
                </button>
                <?php
                if ($usertype == "admin" || $userinfo == "system-admin") {
                ?>
                    <button class="buttons" data-href="accept.php">
                        批准
                    </button>
                <?php
                }
                ?>


            <?php
            } else {
            ?>
                <button class="buttons" data-href="access.php">
                    登录 / 注册
                </button>
            <?php
            }
            ?>
        </section>
    </main>
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
        for (const iterator of document.getElementsByClassName("buttons")) {
            iterator.addEventListener("click", () => {
                location.href = iterator.dataset.href;
            })
        }
    </script>
</body>
<!--- NSBA SCRIPT --->
<script src="js/browserChecker.js"></script>
<script>
    var fontMatcher = () => {
        var screenDiv = window.innerWidth / 1920;
        document.getElementById("title-text").style.fontSize = (220 * screenDiv) + "px"
        document.getElementById("title-text2").style.fontSize = (50 * screenDiv) + "px"
    }
    setInterval(fontMatcher, 100);
</script>

</html>