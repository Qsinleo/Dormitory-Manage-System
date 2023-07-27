<?php
session_start();
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
    <title>Document</title>
</head>

<body>
    <h1>管理</h1>
    <div>
        <h2>我的账号</h2>
        <?php
        var_dump($_SESSION);
        $userinfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
        var_dump($userinfo);
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
                    <td><?php echo $userinfo["name"] ?></td>
                </tr>
                <tr>
                    <td><?php ?></td>
                    <td><?php ?></td>
                </tr>
                <tr>
                    <td><?php ?></td>
                    <td><?php ?></td>
                </tr>
            </table>
        <?php
        }
        ?>
    </div>
</body>

</html>