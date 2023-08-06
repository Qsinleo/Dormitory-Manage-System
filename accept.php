<?php
if (!($usertype == "admin" || $usertype == "system-admin")) {
    header("Location: manage.php");
} elseif (is_null($usertype)) {
    header("Location: index.php");
}
require_once "header.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>批准</title>
</head>

<body>
    <h1>批准</h1>
    <div>登录为：<?php echo $usertype == "admin" ? "中级管理员" : "系统管理员" ?></div>
    <div>
        <ul>
            <?php
            function print_accept_or_reject($method, $requestid): void
            {
                echo '
                    <form action="proceed.php" method="post">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="id" value="', $requestid, '">
                        <input type="submit" value="通过">
                    </form>
                    '; //输出同意
                echo '
                    <form action="proceed.php" method="post">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="id" value="', $requestid, '">
                        <input type="submit" value="驳回">
                    </form>
                    '; //输出反对
            }
            foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `requests`")) as $value) {
                $params = json_decode($value, true);
                $readuser = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` WHERE id = " . $value["requestid"]));
                echo "<li>(ID:", $value["requestid"], ")", $readuser["realname"], "请求";
                if ($usertype == "system-admin") {
                    if ($value["type"] == "upgrade") {
                        echo "升级权限至中级管理员";
                        print_accept_or_reject("upgrade-allow", $readuser["id"]);
                    }
                }
                if ($value["type"] == "register-allow") {
                    echo "注册成为", $params["request-to-be"] == "staff" ? "员工" : "中级管理员";
                    print_accept_or_reject("register-allow", $readuser["id"]);
                }
                echo "【<a href=\"mailto:", $readuser["mail"], "\">使用邮件联系</a>】";
            }

            //输出反对
            echo "</li>";
            ?>
        </ul>
    </div>
</body>

</html>