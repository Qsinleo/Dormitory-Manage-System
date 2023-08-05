<?php
session_start();
if (!($_SESSION["loginas"] == "admin" || $_SESSION["loginas"] == "system-admin")) {
    header("Location: manage.php");
} elseif (is_null($_SESSION["loginas"]) || $_SESSION["loginas"] == "failed") {
    header("Location: index.php");
}
include_once "mysqlConnect.php";
function print_info($requestid, $willing, $method): void
{
    global $con;
    echo "<li>(ID:", $requestid, ")", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` WHERE id = " . $requestid))["realname"], "想要", $willing;
    echo "<a href=\"mailto:", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` WHERE id = " . $requestid))["mail"], "\">使用邮件联系</a>";
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
    echo "</li>";
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
    <h1>批准</h1>
    <div>登录为：<?php echo $_SESSION["loginas"] == "admin" ? "中级管理员" : "系统管理员" ?></div>
    <section>
        <header>用户注册批准</header>
        <ul>
            <?php
            if ($_SESSION["loginas"] == "system-admin") {
                foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `users` WHERE actived = 0 AND accessment = 'admin'"), MYSQLI_ASSOC) as $value) {
                    print_info($value["id"], "成为中级管理员", "register-allow");
                }
            }
            foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `users` WHERE actived = 0 AND accessment = 'staff'"), MYSQLI_ASSOC) as $value) {
                print_info($value["id"], "成为员工", "register-allow");
            }
            echo "没有更多了~";
            ?>

        </ul>
    </section>
    <?php if ($_SESSION["loginas"] == "system-admin") { ?>
        <section>
            <header>用户升级批准</header>
            <ul>
                <?php
                foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `upgrade`"), MYSQLI_ASSOC) as $value) {
                    print_info($value["requestid"], "升级为中级管理员", "upgrade-allow");
                }
                echo "没有更多了~";
                ?>
            </ul>
        </section>
    <?php } ?>
</body>

</html>