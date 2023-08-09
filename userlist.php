<?php
require_once "header.php";
if (!($usertype == "admin" || $usertype == "system-admin")) {
    header("Location: manage.php");
} elseif (is_null($usertype)) {
    header("Location: index.php");
}

$sql = "SELECT * FROM `users`";
if (key_exists("id", $_REQUEST)) {
    $sql = "SELECT * FROM `users` WHERE id = '" . $_REQUEST["id"] . "'";
}
if (key_exists("email", $_REQUEST)) {
    $sql = "SELECT * FROM `users` WHERE mail = '" . $_REQUEST["email"] . "'";
}
if (key_exists("view", $_REQUEST)) {
    $sql = "SELECT * FROM `users` WHERE accessment = '" . $_REQUEST["view"] . "'";
}
if (key_exists("realname", $_REQUEST)) {
    $sql = "SELECT * FROM `users` WHERE realname = '" . $_REQUEST["realname"] . "'";
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="css/list.css">
    <title>列表</title>
</head>

<body>
    <h1>用户列表</h1>
    <div>登录为：<?php echo $usertype == "admin" ? "中级管理员" : "系统管理员" ?></div>
    <details>
        <summary>查询条件</summary>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label>只看：
                <select name="view">
                    <option value="system-admin">系统管理员</option>
                    <option value="admin">中级管理员</option>
                    <option value="staff">员工</option>
                </select>
            </label>
            <input type="submit" value="查询" />
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label>指定ID：
                <input type="number" name="id" required />
            </label>
            <input type="submit" value="查询" />
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label>指定邮箱：
                <input type="email" name="email" required />
            </label>
            <input type="submit" value="查询" />
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label>指定真名：
                <input type="text" name="realname" maxlength="8" required />
            </label>
            <input type="submit" value="查询" />
        </form>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <input type="submit" value="查询全部" />
        </form>
    </details>
    <div class="user-list">
        <ul>
            <?php foreach (mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC) as $value) {
                echo "<li>",
                "<span class='real-name'>", $value["realname"], "</span><a href='mailto:", $value["mail"], "'>", $value["mail"], "</a>",
                "<span class='access'>", ($value["accessment"] == "staff" ? "员工" : ($value["accessment"] == "admin" ? "中级管理员" : "系统管理员")),
                "<span class='identify'>ID:", $value["id"], " 上次登录时间：", $value["logintime"], "</span>";
                if ($usertype == "system-admin") {
                    echo "<form action='proceed.php' method='post' class='inline'>
                    <input type='hidden' name='type' value='delete-user' />
                    <input type='hidden' name='id' value='" . $value["id"] . "' />
                    <input type='submit' value='删除用户' />
                    </form>";
                } else {
                    echo "<button title='中级管理员无权限' disabled>删除用户</button>";
                }
                echo "</li>";
            }
            ?>
            <i><small>没有更多了~</small></i>
        </ul>
    </div>
</body>

</html>