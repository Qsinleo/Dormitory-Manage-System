<?php
require_once "header.php";
require_once "navpage.php";
if (is_null($usertype)) {
    header("Location: index.php");
}

$sql = "SELECT * FROM `rooms`";
if (key_exists("id", $_REQUEST)) {
    $sql = "SELECT * FROM `rooms` WHERE id = '" . $_REQUEST["id"] . "'";
}
if (key_exists("number", $_REQUEST)) {
    $sql = "SELECT * FROM `rooms` WHERE `number` = '" . $_REQUEST["number"] . "'";
}
if (key_exists("view", $_REQUEST)) {
    $sql = "SELECT * FROM `rooms` WHERE `status` = '" . $_REQUEST["view"] . "'";
}
if ($usertype == "admin") {
    $manageparts = explode(",", mysqli_fetch_assoc(mysqli_query($con, "SELECT `managepartid` FROM `users` WHERE id = " . $_SESSION["loginid"]))["managepartid"]);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/list.css">
    <title>列表</title>
</head>

<body>
    <main>
        <h1>房间列表</h1>
        <details>
            <summary>查询条件</summary>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <label>只看：
                    <select name="view">
                        <option value="normal">正常</option>
                        <option value="cleaning">正在打扫</option>
                        <option value="repairing">正在修复</option>
                        <option value="stop">停用</option>
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
                <label>指定房号：
                    <input type="number" name="number" required />
                </label>
                <input type="submit" value="查询" />
            </form>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                <input type="submit" value="查询全部" />
            </form>
        </details>
        <div class="room-list">
            <ul>
                <?php
                foreach (mysqli_fetch_all(mysqli_query($con, $sql), MYSQLI_ASSOC) as $value) {
                    echo "<li>",
                    "<span class='room-number'>", $value["number"], "</span>",
                    "<span class='room-status'>";
                    switch ($value["status"]) {
                        case 'normal':
                            echo "正常";
                            break;
                        case 'cleaning':
                            echo "正在打扫";
                            break;
                        case 'repairing':
                            echo "正在修复";
                            break;
                        case 'stop':
                        default:
                            echo "停用";
                            break;
                    }
                    echo "</span>";
                    if ($value["status"] == "normal") {
                        echo "<span class='live-count'>", mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE roomid = " . $value["id"])), "人居住</span>";
                    }
                    echo "<span class='identify'>ID:", $value["id"], "</span>";
                    if (
                        mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")) == 0 &&
                        $value["status"] == "normal"
                    ) {
                        echo '<button onclick="setRoom(', $value["number"], ');">设为入住房间→</button>';
                    }
                    if ($usertype == "admin") {
                        if (in_array($value["id"], $manageparts)) {
                            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE roomid = " . $value["id"])) == 0) {
                                echo '<form action="proceed.php" method="post" class="inline">
                                <input type="hidden" name="type" value="delete-room" />
                                <input type="hidden" name="id" value="' . $value["id"] . '" />
                                <input type="submit" value="删除房间" />
                                </form>
                                <form action="proceed.php" method="post" class="inline">
                                <input type="hidden" name="type" value="change-room-status" />
                                <input type="hidden" name="id" value="' . $value["id"] . '" />
                                <label>将房间状态更改为：
                                <select name="change-to-status">
                                    <option value="normal">正常(有人/无人)</option>
                                    <option value="cleaning">打扫中</option>
                                    <option value="repairing">修理中</option>
                                    <option value="stop">停用</option>
                                </select>
                                </label>
                                </form>
                            ';
                            } else {
                                echo "<button title='房间里还有成员正在居住！' disabled>删除房间</button>";
                            }
                        } else {
                            echo "<span class='no-access'>您没有对此房间管理的权限。</span>";
                        }
                    } elseif ($usertype == "system-admin") {
                        echo '<form action="proceed.php" method="post" class="inline">
                                <input type="hidden" name="type" value="delete-room" />
                                <input type="hidden" name="id" value="' . $value["id"] . '" />
                                <input type="submit" value="删除房间" class="delete"/>
                            </form>
                            <form action="proceed.php" method="post" class="inline">
                            <input type="hidden" name="type" value="change-room-status" />
                            <input type="hidden" name="id" value="' . $value["id"] . '" />
                            <label>将房间状态更改为
                            <select name="change-to-status">
                                <option value="normal">正常(有人/无人)</option>
                                <option value="cleaning">打扫中</option>
                                <option value="repairing">修理中</option>
                                <option value="stop">停用</option>
                            </select>
                            </label>
                            <input type="submit" value="确定"/>
                            </form>';
                    }
                    echo "</li>";
                }
                ?>
                <i><small>没有更多了~</small></i>
            </ul>
        </div>
        <div class="sidebar-inner roomlist">
            <?php if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"])) != 0) { ?>
                <div>
                    <form action="proceed.php" method="post">
                        <header>提前办理签出</header>
                        <div>您的既定预约时间是：<b>
                                <?php
                                echo mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"]))["fromdate"];
                                ?> →
                                <?php
                                echo mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"]))["todate"];
                                ?>
                            </b></div>
                        <div>房间号是：<b>
                                <?php
                                echo mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` WHERE id = " . mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"]))["roomid"]))["number"];
                                ?>
                            </b></div>
                        <input type="hidden" name="type" value="check-out" />
                        <input type="submit" id="submit-room" value="确认(提前)签出" />
                    </form>
                </div>
            <?php } else if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")) == 0) { ?>
                <div>
                    <form action="proceed.php" method="post">
                        <header>办理入住</header>
                        <input type="hidden" name="type" value="request-check-in" />
                        <input type="hidden" name="room-num" />
                        <div class="operation-info">
                            <div><small>ROOM</small><span id="roomnum">-</span></div>
                            <div>
                                <input type="date" name="start-time" min="<?php echo date("Y-m-d") ?>" value="<?php echo date("Y-m-d") ?>" required />
                                →
                                <input type="date" name="end-time" min="<?php echo date("Y-m-d") ?>" value="<?php echo date("Y-m-d") ?>" required />
                            </div>
                        </div>
                        <textarea name="reason" maxlength="100" placeholder="住房原因。不超过100个字符。" class="reason"></textarea>
                        <input type="reset" value="重置" />
                        <input type="submit" id="submit-room" value="提交申请" disabled />
                    </form>
                </div>
            <?php } else {  ?>
                <div>
                    <header>您的提交正在被审核……</header>
                </div>
            <?php }
            if ($usertype == "admin" || $usertype == "system-admin") { ?>
                <details>
                    <summary>高级设置</summary>
                    <header>添加房间</header>
                    <form action="proceed.php" method="post">
                        <input type="hidden" name="type" value="add-room">
                        <label>请输入房号：<input type="number" name="room-number"></label>
                        <input type="reset" value="重置" />
                        <input type="submit" value="添加">
                    </form>
                </details>
            <?php } ?>
        </div>
    </main>
</body>
<script src="js/roomStatus.js"></script>

</html>