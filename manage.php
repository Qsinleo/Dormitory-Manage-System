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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <title>管理</title>
</head>

<body>
    <h1>管理</h1>
    <div>
        <h2>我的账号</h2>
        <?php
        $userinfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
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
                        ?></td>
                    <td><?php echo $userinfo["realname"] ?></td>
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
                    <td><?php echo $userinfo["workid"]; ?></td>
                </tr>
                <tr>
                    <td><?php echo $userinfo["department"]; ?></td>
                    <td>住在房间号：<?php
                                echo is_null($userinfo["liveinroom"]) ? "无" : mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $userinfo["liveinroom"]))["number"];
                                ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        if (is_null($userinfo["managepartid"]))
                            echo "无";
                        else
                            foreach (explode(",", $userinfo["managepartid"]) as $key => $value) {
                                echo "<span class='manage-id-part'>", $value, "</span>";
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

</html>