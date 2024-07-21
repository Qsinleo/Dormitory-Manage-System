<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>批准</title>
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="css/pages/list.css">
</head>

<body>
    <?php
    $vis_limit = 3;
    require_once "embed/sidenav.php";
    include_once "embed/invalid_visit.php";
    ?>
    <main>
        <h1>批准</h1>
        <details>
            <summary>筛选</summary>
            <div class="restrict-table">
                <div>
                    <label>请求ID<input type="number" id="id-restrict-input" placeholder="包含" /></label>
                    <label>用户ID<input type="number" id="user-id-restrict-input" placeholder="包含" /></label>
                    <label>真名<input type="text" id="realname-restrict-input" placeholder="包含" /></label>
                </div>
                <div>
                    <div>
                        请求时间
                        <span class="lighter smaller">从至今</span>
                        <input type="number" id="request-time-restrict-range-start" min="1" placeholder="天数">
                        <span class="lighter smaller">天以前的</span>
                        <input type="number" min="1" id="request-time-restrict-range-length" placeholder="默认1">
                        <span class="lighter smaller">天</span>
                    </div>
                    <select id="type-restrict-select">
                        <option value="no-restrict">无限制</option>
                        <option value="升级权限">升级权限</option>
                        <option value="更改管理区域">更改管理区域</option>
                        <option value="注册成为">请求注册</option>
                        <option value="办理入住">请求入住</option>
                    </select>
                </div>
            </div>
        </details>
        <div>
            共有<span id="total-result-label"><?php echo $con->query("SELECT COUNT(*) AS `ct` FROM `requests`")->fetch_assoc()["ct"]; ?></span>条记录
            <button id="clear-restrict-button">清除筛选</button>
        </div>
        <div class="data-list">
            <table>
                <?php
                function print_accept_or_reject($method): void
                {
                    global $value;
                    echo '<form action="process.php" method="post" class="inline">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="req-id" value="', $value["id"], '">
                        <input type="submit" value="批准" class="accept-btn">
                    </form>
                    <form action="process.php" method="post" class="inline">
                        <input type="hidden" name="type" value="', $method, '">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="req-id" value="', $value["id"], '">
                        <input type="submit" value="驳回" class="reject-btn">
                    </form>';
                }
                foreach ($con->query("SELECT * FROM `requests`")->fetch_all(MYSQLI_ASSOC) as $value) {
                    $params = json_decode($value["param"], true);
                    $readuser = $con->query("SELECT * FROM `users` WHERE `id` = " . $value["requestid"])->fetch_assoc();
                    echo "<tr>";
                    echo "<td class='id-label'>", $value["id"], "</td>";
                    echo "<td class='realname-label'>", $readuser["realname"], "</td>";
                    echo "<td class='smaller'><div class='lighter'>用户ID</div><span class='user-id-label'>", $value["requestid"], "</span></td>";
                    echo "<td><a href=\"mailto:", $readuser["email"], "\">", $readuser["email"], "</a></td>";
                    echo "<td class='smaller'><div class='lighter'>请求时间</div><span class='request-time-label'>", $value["time"], "</span></td>";
                    echo "<td><span class='type-label'>";
                    if ($usertype == "system-admin") {
                        if ($value["type"] == "upgrade-to-admin") {
                            echo "升级权限</span>至中级管理员";
                            print_accept_or_reject("upgrade-allow");
                        } elseif ($value["type"] == "change-manage") {
                            echo "更改管理区域</span>至<code>", ($params["area"] == "[null]" ? "(无管理)" : $params["area"]), "</code>";
                            print_accept_or_reject("change-manage-allow");
                        } elseif ($value["type"] == "register-allow") {
                            echo "注册成为</span>", $params["request-to-be"] == "staff" ? "员工" : "中级管理员";
                            print_accept_or_reject("register-allow");
                        }
                    } else {
                        if ($value["type"] == "register-allow" && $params["request-to-be"] == "staff") {
                            echo "注册成为</span>员工";
                            print_accept_or_reject("register-allow");
                        }
                    }
                    if ($value["type"] == "check-in" && ($usertype == "system-admin" ||
                        (!is_null($readuser["managepart"]) &&
                            strlen($con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($readuser["managepart"]) . "'")->fetch_assoc()["includes"]) > 0 &&
                            in_array($params["room-number"], explode(',', $con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($readuser["managepart"]) . "'")->fetch_assoc()["includes"]))))) {
                        echo "办理入住</span>至<code>", $params["roomnumber"], "</code>（<code>", $params["start-time"], "</code>➔<code>", $params["end-time"] . "</code>）";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
            <?php include_once "embed/no_matched.php"; ?>
        </div>
    </main>
</body>
<script src="js/accept.js"></script>

</html>