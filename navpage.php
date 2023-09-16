<?php
function data_uri($contents, $mime)
{
    return ('data:' . $mime . ';base64,' . base64_encode($contents));
}
if (!is_null($usertype)) {
?>

    <style>
        /* YOUR CSS */
        nav {
            background-color: aquamarine;
            border-radius: 5px;
            margin: 5px;
            padding: 5px;
            display: inline-block;
            width: calc(100% - 65px);
            vertical-align: middle;
        }

        .inline-title {
            font-size: large;
            font-weight: bold;
        }

        nav a {
            text-decoration: none;
            background-color: green;
            color: whitesmoke;
            margin: 3px;
            padding: 3px;
            border-radius: 5px;
            transition-duration: .2s;
        }

        nav a:hover {
            background-color: blueviolet;
        }

        nav a:active {
            background-color: purple;
        }

        .left-side {
            display: inline-block;
            text-align: left;
            width: 60%;
        }

        .right-side {
            display: inline-block;
            width: 30%;
            margin-left: 8%;
            text-align: right;
        }

        .name {
            font-size: large;
            font-weight: bold;
        }

        .access-user {
            font-size: small;
            font-style: italic;
        }

        .header {
            width: 35px;
            height: 35px;
            border-radius: 50px;
            border: 1px solid;
            vertical-align: middle;
        }
    </style>
    <div class="top">
        <nav>
            <div class="left-side">
                <span class="inline-title">导航</span>
                <a href="index.php">首页</a>
                <a href="manage.php">管理</a>
                <a href="roomlist.php">房间列表</a>
                <a href="userlist.php">用户列表</a>
                <a href="accept.php">批准</a>
                <a href="logout.php">退出登录</a>
            </div>
            <div class="right-side">
                <span class="name"><?php echo is_null($usertype) ? "访客" : (($usertype == "inactived") ? "[未激活]" : "" . $userinfo["realname"]) ?></span>
                <span class="access-user"><?php
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
        </nav>
        <img src="<?php echo data_uri($userinfo["header"], "image/jpeg") ?>" class="header" />
    </div>
    <script>
        const aTags = document.getElementsByTagName("nav")[0].getElementsByTagName("a");
        switch (location.href.split("/")[location.href.split("/").length - 1]) {
            case "manage.php":
                aTags[1].style.backgroundColor = "orangered";
                aTags[1].href = "#";
                break;
            case "roomlist.php":
                aTags[2].style.backgroundColor = "orangered";
                aTags[2].href = "#";
                break;
            case "userlist.php":
                aTags[3].style.backgroundColor = "orangered";
                aTags[3].href = "#";
                break;
            case "accept.php":
                aTags[4].style.backgroundColor = "orangered";
                aTags[4].href = "#";
                break;
            default:
                break;
        }
    </script>
<?php } ?>