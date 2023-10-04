<?php
require_once "header.php";
include_once "navpage.php";
if (!($usertype == "admin" || $usertype == "system-admin")) {
    header("Location: manage.php");
} elseif (is_null($usertype)) {
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
    <h1>房间列表</h1>
    <div>登录为：<?php echo $usertype == "admin" ? "中级管理员" : "系统管理员" ?></div>
    <details>
        <summary>查询条件</summary>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
            <label>只看：
                <select name="view">
                    <option value="occupied">有人</option>
                    <option value="empty">无人</option>
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
                    case 'occupied':
                        echo "有人";
                        break;
                    case 'empty':
                        echo "无人";
                        break;
                    case 'cleaning':
                        echo "正在打扫";
                        break;
                    case 'repairing':
                        echo "正在修复";
                        break;
                    case 'stop':
                    default:
                        echo "无人";
                        break;
                }
                echo "</span><span class='live-count'>";
                if ($value["status"] == "occupied") {
                    echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE roomid = " . $value["id"]));
                } else {
                    echo "无";
                }
                echo "人居住</span><span class='identify'>ID:", $value["id"], "</span>";
                if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")) == 0) {
                    echo '<button onclick="setRoom(', $value["number"], ');">设为操作房间→</button>
                    ';
                } else {
                    echo '<button disabled>设为操作房间→</button>
                    ';
                }
                if ($usertype == "admin") {
                    if (in_array($value["id"], $manageparts)) {
                        if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE roomid = " . $value["id"])) == 0) {
                            echo '<form action="proceed.php" method="post" class="inline">
                                <input type="hidden" name="type" value="delete-room" />
                                <input type="hidden" name="id" value="' . $value["id"] . '" />
                                <input type="submit" value="删除房间" />
                            </form>';
                        } else {
                            echo "<button title='房间里还有成员正在居住！' disabled>删除房间</button>";
                        }
                    } else {
                        echo "<button title='你没有权限，请在管理页面申请权限！' disabled>删除房间</button>";
                    }
                } else {
                    echo '<form action="proceed.php" method="post" class="inline">
                                <input type="hidden" name="type" value="delete-room" />
                                <input type="hidden" name="id" value="' . $value["id"] . '" />
                                <input type="submit" value="删除房间" />
                            </form>';
                }
                echo "</li>";
            }
            ?>
            <i><small>没有更多了~</small></i>
        </ul>
    </div>
    <div class="sidebar roomlist">
        <?php if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"])) != 0) { ?>
            <div>
                <form action="proceed.php" method="post">
                    <header>提前办理签出</header>
                    <div>您的既定预约时间是：<b>
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
                    <input type="submit" id="submit-room" value="确认提前签出" />
                </form>
            </div>
        <?php } else if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")) == 0) { ?>
            <div>
                <form action="proceed.php" method="post">
                    <header>办理入住</header>
                    <input type="hidden" name="type" value="request-check-in" />
                    <input type="hidden" name="room-num" />
                    <div>入住房间号：<span id="roomnum">-</span></div>
                    <div><label>开始居住时间<input type="date" name="start-time" min="<?php echo date("Y-m-d") ?>" value="<?php echo date("Y-m-d") ?>" /></label></div>
                    <div><label>结束居住时间<input type="date" name="end-time" value="<?php echo date("Y-m-d") ?>" min="<?php echo date("Y-m-d") ?>" /></label></div>
                    <textarea name="reason" maxlength="100" placeholder="住房原因。不超过100个字符。"></textarea>
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
            <div>
                <header>添加房间</header>
                <form action="proceed.php" method="post">
                    <input type="hidden" name="type" value="add-room">
                    <label>请输入房号：<input type="number" name="room-number"></label>
                    <input type="reset" value="重置" />
                    <input type="submit" value="添加">
                </form>
            </div>
        <?php } ?>
    </div>
</body>
<script src="js/roomStatus.js"></script>

</html>