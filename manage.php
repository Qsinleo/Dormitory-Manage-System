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
        <form action="proceed.php" method="post" enctype="multipart/form-data">
            <div id="upload-header">
                <header>上传头像</header>
                <input type="hidden" name="type" value="change-header">
                <input type="file" id="image" accept="images" name="header" />
                <div id="drop">拖到此处以上传</div>
            </div>
            <div id="change-realname">
                <header>更改真实名称</header>
                <input type="hidden" name="type" value="change-realname">
                <input type="text" name="realname" maxlength="8" value="<?php echo $userinfo["realname"]; ?>" />
            </div>
            <input type="reset" value="重置" />
            <input type="submit" value="Go(～￣▽￣)～">
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
                    <td><?php echo $userinfo["realname"] ?><button onclick="openDialog('change-realname')">更改</button></td>
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
                    <td>工号：<?php echo $userinfo["workid"]; ?></td>
                </tr>
                <tr>
                    <td>部门：<?php echo $userinfo["department"]; ?></td>
                    <td>住在房间号：<?php
                                echo is_null($userinfo["liveinroom"]) ? "无" : mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $userinfo["liveinroom"]))["number"];
                                ?></td>
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
        <form action="proceed.php" method="post">
            <input type="submit" name="delete-account" value="删除账户" />
        </form>
    </div>
</body>
<script src="js/maxSizeOfImage.js"></script>
<script src="js/dropToUpload.js"></script>

</html>