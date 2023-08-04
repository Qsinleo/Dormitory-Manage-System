<?php
/*

loginas:以……登录（null=访客）
loginid:登录id（null=访客）

*/
session_start();
if (!(array_key_exists("loginas", $_SESSION)) || $_SESSION["loginas"] == "failed") {
    header("Location: index.php");
}
require_once "mysqlConnect.php";
function data_uri($contents, $mime)
{
    return ('data:' . $mime . ';base64,' . base64_encode($contents));
}
$userinfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
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
            <input type="file" id="image" accept="images" name="header" />
            <div id="drop">拖到此处以上传</div>
        </form>
        <form action="proceed.php" method="post" id="change-realname">
            <header>更改真实名称</header>
            <input type="hidden" name="type" value="change-realname">
            <input type="text" name="realname" maxlength="8" value="<?php echo $userinfo["realname"]; ?>" required />
        </form>
        <form action="proceed.php" method="post" id="change-access">
            <header>更改权限</header>
            <input type="hidden" name="type" value="change-access">
            <input type="hidden" name="toaccess" value="<?php ?>">
            <div>确定要将权限修改为“中级管理员”吗？这将需要中级管理员/系统管理员批准。</div>
        </form>
        <form action="proceed.php" method="post" id="change-depart">
            <header>修改部门</header>
            <input type="hidden" name="type" value="change-depart">
            <input type="number" name="depart" value="<?php echo $userinfo["department"]; ?>">
        </form>
        <form action="proceed.php" method="post" id="change-workid">
            <header>修改工号</header>
            <input type="hidden" name="type" value="change-workid">
            <input type="number" name="workid" value="<?php echo $userinfo["workid"]; ?>">
        </form>
        <form action="proceed.php" method="post" id="change-liveroom">
            <header>修改住所房间号</header>
            <input type="hidden" name="type" value="change-liveroom">
            <input type="text" name="liveinroom" value="<?php if (!is_null($userinfo["liveinroom"])) echo mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $_SESSION["liveinroom"]))["number"]; ?>" />
        </form>
        <form action="proceed.php" method="post" id="change-password">
            <header>修改密码</header>
            <input type="hidden" name="type" value="change-password">
            <table>
                <tr>
                    <td>请输入旧密码：</td>
                    <td><input type="password" id="old-password" key="<?php echo $userinfo["password"]; ?>" required /></td>
                    <td id="password-info"></td>
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
        </form>
    </div>
    <div>
        <h2>我的账号</h2>
        <?php
        if ($userinfo["actived"] == 0) {
        ?>
            <div class="non-actived">您的帐户尚未被批准！</div>
        <?php
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
                        if ($_SESSION["loginas"] == "staff") {
                            echo "员工";
                        } elseif ($_SESSION["loginas"] == "admin") {
                            echo "管理员";
                        } else {
                            echo "系统管理员";
                        }
                        ?></td>
                    <td>工号：<?php echo $userinfo["workid"]; ?><button onclick="openDialog('change-workid')">更改</button></td>
                </tr>
                <tr>
                    <td>部门：<?php echo $userinfo["department"]; ?><button onclick="openDialog('change-depart')">更改</button></td>
                    <td>住在房间号：<?php
                                echo is_null($userinfo["liveinroom"]) ? "无" : mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $userinfo["liveinroom"]))["number"];
                                ?><button onclick="openDialog('change-liveroom')">更改</button></td>

                </tr>
                <tr>
                    <td colspan="2">管理区域：
                        <?php
                        if (is_null($userinfo["managepartid"]))
                            echo "无";
                        else {
                            foreach (explode(",", $userinfo["managepartid"]) as $value) {
                                echo "<span class='manage-id'>", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $value))["number"], "</span>";
                            }
                        }
                        ?>
                    </td>
                </tr>
            </table>
        <?php
        }
        ?>
        <button onclick="openDialog('change-password')">更改密码</button>
        <form action="proceed.php" method="post">
            <input type="submit" name="delete-account" value="删除账户" />
        </form>
    </div>
</body>
<script src="js/sha1.js"></script>
<script src="js/maxSizeOfImage.js"></script>
<script src="js/dropToUpload.js"></script>
<script src="js/verifyPassword.js"></script>

</html>