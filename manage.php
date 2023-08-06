<?php
/*

loginas:以……登录（null=访客，failed=登录失败，inactived=登录但未激活）
loginid:登录id（null=访客）

*/
require_once "header.php";
if (is_null($_SESSION["loginid"])) {
    header("Location: index.php");
}

function data_uri($contents, $mime)
{
    return ('data:' . $mime . ';base64,' . base64_encode($contents));
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="css/manage.css">
    <title>管理</title>
</head>
<script src="js/dialogueOpenAndClose.js"></script>

<body>
    <h1>管理</h1>
    <div id="hide-screen">&nbsp;</div>
    <div id="dialogue">
        <button class="close" onclick="hideDialog()">×</button>
        <button class="collapse" onclick="smallDialog()">-</button>
        <form action="proceed.php" method="post" enctype="multipart/form-data" id="upload-header">
            <header>上传头像</header>
            <input type="hidden" name="type" value="change-header">
            <div>仅支持.png、.jpg和.jpeg的不超过5MB的图片文件！（注：图片较大时，传输时间可能较多。）</div>
            <input type="file" id="image" accept="images" name="header" />
            <div id="drop">拖到此处以上传</div>
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-realname">
            <header>更改真实名称</header>
            <input type="hidden" name="type" value="change-realname">
            <input type="text" name="realname" maxlength="8" value="<?php echo $userinfo["realname"]; ?>" required />
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-access">
            <header>更改权限</header>
            <?php if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'upgrade-to-admin'")) == 0) { ?>
                <input type="hidden" name="type" value="change-access">
                <input type="hidden" name="toaccess" value="<?php echo $userinfo["accessment"] == "staff" ? "admin" : "staff" ?>">
                <div>确定要将权限修改为“<?php echo $userinfo["accessment"] == "staff" ? "中级管理员" : "员工" ?>”吗？<?php echo $userinfo["accessment"] == "staff" ? "这将需要系统管理员批准。" : "你将失去管理员权限！" ?></div>
                <input type="submit" value="Go(/≧▽≦)/">
            <?php
            } else { ?>
                <div>您已经发送过修改权限申请，请等待管理员同意。</div>
            <?php
            } ?>
        </form>
        <form action="proceed.php" method="post" id="change-depart">
            <header>修改部门</header>
            <input type="hidden" name="type" value="change-depart">
            <input type="text" name="depart" value="<?php echo $userinfo["department"]; ?>">
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-workid">
            <header>修改工号</header>
            <input type="hidden" name="type" value="change-workid">
            <input type="number" name="workid" value="<?php echo $userinfo["workid"]; ?>">
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-liveroom">
            <header>修改住所房间号</header>
            <input type="hidden" name="type" value="change-liveroom">
            <input type="text" name="liveinroom" value="<?php if (!is_null($userinfo["liveinroom"])) echo mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $_SESSION["liveinroom"]))["number"]; ?>" />
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-password">
            <header>修改密码</header>
            <input type="hidden" name="type" value="change-password">
            <table>
                <tr>
                    <td>请输入旧密码：</td>
                    <td><input type="password" id="old-password" required /></td>
                </tr>
                <tr>
                    <td>请输入新密码：</td>
                    <td><input type="password" name="new-password" required /></td>
                    <td id="password-info3"></td>
                </tr>
                <tr>
                    <td>请再次输入新密码：</td>
                    <td><input type="password" id="new-password-retype" required /></td>
                    <td id="password-info2"></td>
                </tr>
            </table>
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
    </div>
    <div>
        <h2>我的账号</h2>
        <?php
        if ($usertype == "inactived") {
        ?>
            <div class="non-actived">您的帐户尚未被批准。请检查邮箱，以便获取是否被通过（注册时已经自动发送了申请）。</div>
            <?php
            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'register-allow'")) == 0) {
            ?>
                <form action="proceed.php" method="post">
                    <input type="hidden" name="type" value="request-register-allow">
                    <input type="hidden" name="request-to-be" value="<?php echo $userinfo["accessment"] ?>">
                    <input type="submit" value="申请批准" />
                </form>
            <?php
            } else {
                echo "<div>您已经发送过注册申请，请等待管理员同意。</div>";
            }
        } else {
            ?>
            <table>
                <tr>
                    <td><?php
                        if ($userinfo["header"] == null) {
                            echo "无头像";
                        } else {
                            echo "<img src='" . data_uri($userinfo["header"], "image/png") . "' />";
                        }
                        ?>
                        <button onclick="openDialog('upload-header')">更改</button>
                    </td>
                    <td><?php echo $userinfo["realname"] ?>(ID:<?php echo $userinfo["id"] ?>)<button onclick="openDialog('change-realname')">更改</button></td>
                </tr>
                <tr>
                    <td><?php
                        if ($usertype == "staff") {
                            echo "员工";
                        } elseif ($usertype == "admin") {
                            echo "中级管理员";
                        } else {
                            echo "系统管理员";
                        }
                        if (!($usertype == "system-admin")) {
                        ?><button onclick="openDialog('change-access')">更改</button>
                        <?php } ?>
                    </td>
                    <td>工号：<?php echo $userinfo["workid"]; ?><button onclick="openDialog('change-workid')">更改</button></td>
                </tr>
                <tr>
                    <td>部门：<?php echo $userinfo["department"]; ?><button onclick="openDialog('change-depart')">更改</button></td>
                    <td>住在房间号：<?php
                                echo is_null($userinfo["liveinroom"]) ? "无" : mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $userinfo["liveinroom"]))["number"];
                                ?><button onclick="openDialog('change-liveroom')">更改</button></td>

                </tr>
                <tr>
                    <td>管理区域：
                        <?php
                        if (is_null($userinfo["managepartid"]))
                            echo "无";
                        else {
                            foreach (explode(",", $userinfo["managepartid"]) as $from_info) {
                                echo "<span class='manage-id'>", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $from_info))["number"], "</span>";
                            }
                        }
                        ?>
                    </td>
                    <td>最近登录时间：
                        <?php echo $userinfo["logintime"]; ?>
                    </td>
                </tr>
            </table>
        <?php
        }
        if ($usertype == "admin" || $usertype == "system-admin") {
        ?>
            <div><a href="accept.php">批准</a></div>
        <?php } ?>
        <button onclick="openDialog('change-password')">更改密码</button>
        <form action="proceed.php" method="post">
            <input type="submit" name="logout" value="退出登录" />
        </form>
        <?php if (!$usertype == "system-admin") { ?>
            <form action="proceed.php" method="post">
                <input type="submit" name="delete-account" value="删除账户" />
            </form>
        <?php } ?>
    </div>
</body>
<script src="js/sha1.js"></script>
<script src="js/maxSizeOfImage.js"></script>
<script src="js/dropToUpload.js"></script>
<script src="js/verifyPassword.js"></script>

</html>