<?php
require_once "header.php";
include_once "navpage.php";
if (!($usertype == "admin" || $usertype == "system-admin")) {
    header("Location: manage.php");
} elseif (is_null($usertype)) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>批准</title>
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/accept.css">
</head>

<body>
    <main>
        <h1>批准</h1>
        <div>
            <ul>
                <?php
                function print_accept_or_reject($method): void
                {
                    global $readuser; //读取的用户数据
                    echo '
                    <form action="proceed.php" method="post" class="inline">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="id" value="', $readuser["id"], '">
                        <input type="submit" value="通过" class="accept-btn">
                    </form>
                    '; //输出同意
                    echo '
                    <form action="proceed.php" method="post" class="inline">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="id" value="', $readuser["id"], '">
                        <input type="submit" value="驳回" class="reject-btn">
                    </form>
                    '; //输出反对
                }
                foreach (mysqli_fetch_all(mysqli_query($con, "SELECT * FROM `requests`"), MYSQLI_ASSOC) as $value) {
                    $params = json_decode($value["param"], true);
                    $readuser = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` WHERE id = " . $value["requestid"]));
                    echo "<li class='request-item'>(ID:", $value["requestid"], ")<span class='name'>", $readuser["realname"], "</span><span class='label-requesting'>请求</span>";
                    if ($usertype == "system-admin") {
                        if ($value["type"] == "upgrade-to-admin") {
                            echo "升级权限至中级管理员";
                            print_accept_or_reject("upgrade-allow");
                        } elseif ($value["type"] == "change-manage") {
                            echo "更改权限至：管理房间" . implode("、", $params["change-to-rooms"]);
                            print_accept_or_reject("change-manage-allow");
                        }
                    }
                    if ($value["type"] == "register-allow") {
                        echo "注册成为", $params["request-to-be"] == "staff" ? "员工" : "中级管理员";
                        print_accept_or_reject("register-allow");
                    } elseif ($value["type"] == "check-in") {
                        echo "办理入住至<b>", $params["roomnumber"], "</b>从", $params["start-time"], "至", $params["end-time"];
                        print_accept_or_reject("check-in-allow");
                    }
                    echo "<a href=\"mailto:", $readuser["mail"], "\">", $readuser["mail"], "</a>";
                    echo "<small class='request-time'>", $value["time"], "</small>";
                    echo "</li>";
                }
                echo "<small><i>没有更多了~</i></small>";
                ?>
            </ul>
        </div>
    </main>
</body>

</html>