<?php
require_once "header.php";
function data_uri($contents, $mime)
{
    return ('data:' . $mime . ';base64,' . base64_encode($contents));
}

?>

<script src="js/loading.js"></script>
<link rel="stylesheet" href="css/font.css">
<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/nav.css">


<!-- Nav -->
<nav class="sidebar close">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="<?php echo (is_null($usertype) || $usertype == "inactived" || is_null($userinfo["header"])) ? "img/stuff.webp" : data_uri($userinfo["header"], "image/png"); ?>" alt="头像" class="header-img-small">
            </span>
            <div class="text logo-text">
                <span class="name">
                    <?php echo is_null($usertype) ? "访客" : (($usertype == "inactived") ? "[未激活]" : "" . $userinfo["realname"]) ?></span>
                <span class="profession"><?php
                                            switch ($usertype) {
                                                case 'inactived':
                                                    echo "未激活";
                                                    break;
                                                case 'staff':
                                                    echo "员工";
                                                    break;
                                                case 'admin':
                                                    echo "管理员";
                                                    break;
                                                case 'system-admin':
                                                    echo "系统管理员";
                                                    break;
                                                default:
                                                    echo "访客";
                                                    break;
                                            }
                                            ?></span>
            </div>
        </div>

        <i class="bx bx-chevron-right toggle"></i>
    </header>
    <div class="menu-bar">
        <div class="menu">
            <li class="search-box">
                <i class="bx bx-search icon"></i>
                <input type="text" placeholder="Search...">
            </li>

            <ul class="menu-links">
                <li class="nav-link">
                    <a href="index.php">
                        <i class="bx bx-home-alt icon"></i>
                        <span class="text nav-text">首页</span>
                    </a>
                </li>
                <?php if (!is_null($usertype)) { ?>
                    <li class="nav-link">
                        <a href="manage.php">
                            <i class='bx bx-wallet icon'></i>
                            <span class="text nav-text">管理</span>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($usertype == "admin" || $usertype == "system-admin") { ?>
                    <li class="nav-link">
                        <a href="accept.php">
                            <i class='bx bx-bar-chart-alt-2 icon'></i>
                            <span class="text nav-text">批准</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="roomlist.php">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">房间列表</span>
                        </a>
                    </li>
                <?php }
                if ($usertype == "system-admin") { ?>
                    <li class="nav-link">
                        <a href="userlist.php">
                            <i class='bx bx-pie-chart-alt icon'></i>
                            <span class="text nav-text">用户列表</span>
                        </a>
                    </li>
                <?php }
                if (is_null($usertype)) { ?>
                    <li class="nav-link">
                        <a href="access.php">
                            <i class='bx bx-pie-chart-alt icon'></i>
                            <span class="text nav-text">登录 / 注册</span>
                        </a>
                    </li>
                <?php } ?>
                <!-- <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-wallet icon'></i>
                            <span class="text nav-text">？？？</span>
                        </a>
                    </li> -->
            </ul>
        </div>
        <div class="bottom-content">
            <?php if (!is_null($usertype)) { ?>
                <li class="logout-btn">
                    <a onclick="confirmLogout()"><i class="bx bx-log-out icon"></i><span class="text nav-text">退出登录</span></a>
                </li>
            <?php } ?>
            <li class="mode">
                <div class="sun-moon">
                    <i class="bx bx-moon icon moon"></i>
                    <i class="bx bx-sun icon sun"></i>
                </div>
                <span class="mode-text text">明亮模式</span>
                <div class="toggle-switch">
                    <span class="switch"></span>
                </div>
            </li>
        </div>
    </div>
</nav>
<script>
    const body = document.querySelector("body");
    const sidebar = body.querySelector("nav");
    const toggle = body.querySelector(".toggle");
    const searchBtn = body.querySelector(".search-box");
    const modeSwitch = body.querySelector(".toggle-switch");
    const modeText = body.querySelector(".mode-text");

    function checkDarkMode() {
        if (
            window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
        ) {
            return true;
        } else {
            return false;
        }
    }

    function clearMessage() {
        if (document.querySelector(".top-message")) {
            const mess = document.querySelector(".top-message");
            if (mess.style.opacity == 1 || (!mess.style.opacity)) {
                mess.style.opacity = 1;
                const handler = setInterval(() => {
                    mess.style.opacity -= 0.1;
                    if (mess.style.opacity <= 0) {
                        clearInterval(handler);
                        mess.remove();
                    }
                }, 30);
            }
        }
    }

    function confirmLogout() {
        if (confirm("你确定要退出登录吗？")) {
            location.href = "logout.php";
        }
    }

    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("close");
    });
    searchBtn.addEventListener("click", () => {
        sidebar.classList.remove("close");
    });
    if (checkDarkMode())
        body.classList.toggle("dark");
    if (body.classList.contains("dark")) {
        modeText.innerText = "暗黑模式";
    } else {
        modeText.innerText = "明亮模式";
    }
    modeSwitch.addEventListener("click", () => {
        body.classList.toggle("dark");
        if (body.classList.contains("dark")) {
            modeText.innerText = "暗黑模式";
        } else {
            modeText.innerText = "明亮模式";
        }
    });
    window.addEventListener("load", () => {
        <?php
        if (!is_null($_SESSION["message"])) {
            echo '
            const messager = document.createElement("div");
            messager.className = "top-message";
            messager.innerHTML = "<div class=\"top-message\">' .
                htmlentities($_SESSION["message"]) .
                '<button></button></div>";
            document.body.appendChild(messager);
            messager.querySelector("button").addEventListener("click", () => {
                messager.querySelector("button").disabled = true;
                clearMessage();
            });
        ';
            unset($_SESSION["message"]);
        } ?>
        if (document.querySelector("main")) {
            document.querySelector("main").addEventListener("click", () => {
                document.querySelector("nav").classList.add("close");
            });
        }

    });
    setTimeout(() => {
        if (document.querySelector(".top-message")) {
            clearMessage();
        }
    }, 10000);

    function secondConfirm() {
        return confirm("你确定这么做吗？");
    }
</script>
<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css" />