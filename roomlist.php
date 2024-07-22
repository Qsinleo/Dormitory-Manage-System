<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/pages/list.css">
    <title>房间列表</title>
</head>

<body>
    <?php
    $vis_limit = 2;
    require_once "embed/sidenav.php";
    include_once "embed/invalid_visit.php";
    include_once "embed/loading_bg.php";
    ?>
    <div id="popup-bg">
        <div id="popup-border">
            <div id="popup-controller">
                <div onclick="closePopup()">×</div>
                <header id="popup-title"></header>
            </div>
            <div id="popup-content">
                <div data-type="管理区域设置">
                    <div class="flex">
                        <div>
                            <header>获取房间号</header>
                            <div>
                                <input type="text" placeholder="搜索房间……" maxlength="10" id="included-room-search-input" />
                                <div id="included-rooms-searchbox" class="light-bg">
                                    <div><i>无搜索结果</i></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <header>添加管理区域</header>

                            <div>
                                <div>
                                    <b>包含的管理房间</b>
                                    <button onclick="document.getElementById('adding-included-room-list').innerHTML = '';">清除</button>
                                    <span class="smaller lighter">双击房间号以删除</span>
                                </div>
                                <div id="adding-included-room-list" class="light-bg room-list">

                                </div>
                            </div>
                            <form action="process.php" method="post" onsubmit="return generateData(0);">
                                <input type="hidden" name="type" value="add-area">
                                <input type="text" name="name" maxlength="20" placeholder="名称" required />
                                <input type="hidden" name="includes" id="adding-area-includes-rooms-meta">
                                <input type="submit" value="添加" id="add-new-area-button">
                            </form>
                        </div>
                        <div>
                            <header>配置已有管理区域</header>
                            <div>
                                <b>已有区域列表</b>
                                <div id="exists-area-list" class="light-bg">
                                    <?php
                                    foreach ($con->query("SELECT * FROM `areas`") as $key => $value) {
                                        echo '<div>', $value["name"],
                                        '<button onclick="modifyArea(\'' . addslashes($value["name"]) . '\',this)">进行更改 ⬇</button>
                                        <form action="process.php" method="post" class="inline">
                                            <input type="hidden" name="type" value="delete-area" />
                                            <input type="hidden" name="name" value="', addslashes($value["name"]), '" />
                                            <input type="submit" value="删除" class="dangerous">
                                        </form>
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div>正在更改<code id="modifying-area-origin-name">-/-</code></div>

                            <div>
                                <div>
                                    <b>包含的管理房间</b>
                                    <button onclick="
                                document.getElementById('modifying-included-room-list').innerHTML = '';
                                ">清除</button>
                                    <span class="smaller lighter">双击房间号以删除</span>
                                </div>
                                <div id="modifying-included-room-list" class="light-bg room-list">

                                </div>
                            </div>
                            <form action="process.php" method="post" onsubmit="return generateData(1);">
                                <input type="hidden" name="type" value="modify-area">
                                <input type="hidden" name="old-name" id="origin-area-name-meta">
                                <input type="text" name="new-name" maxlength="20" placeholder="新名称" minlength="1" required id="new-area-name-input">
                                <input type="hidden" name="includes" id="modifying-area-includes-rooms-meta">
                                <button id="reset-modify-area-button" type="button" class="dangerous">重置</button>
                                <input type="submit" value="保存更改" disabled id="modify-area-button">
                            </form>
                        </div>
                    </div>
                </div>
                <div data-type="房间设置">
                    <button id="refresh-room-info-button" class="dangerous">重新获取所有设置</button>
                    <form action="process.php" method="post" class="inline" onsubmit="return secondConfirm()">
                        <input type="hidden" name="type" value="delete-room" />
                        <input type="hidden" name="number" class="modifying-room-number-meta" />
                        <input type="submit" value="删除房间" class="dangerous" id="delete-room-button" />
                    </form>
                    <form action="process.php" method="post" class="inline" onsubmit="return secondConfirm()">
                        <input type="hidden" name="type" value="remove-all-living-people" />
                        <input type="hidden" name="number" class="modifying-room-number-meta" />
                        <input type="submit" value="清空居住人" class="dangerous" />
                    </form>
                    <div class="flex">
                        <div>
                            <header>更改房间状态</header>
                            <form action="process.php" method="post">
                                <input type="hidden" name="type" value="change-room-status" />
                                <input type="hidden" name="number" class="modifying-room-number-meta" />
                                <select name="change-to-status" id="modifying-room-status-select">
                                    <option value="normal">正常</option>
                                    <option value="cleaning">打扫中</option>
                                    <option value="repairing">修理中</option>
                                    <option value="stop">停用</option>
                                </select>
                                </label>
                                <input type="submit" value="更改" />
                            </form>
                        </div>
                        <div>
                            <header>更改房间地址</header>
                            <form action="process.php" method="post">
                                <input type="hidden" name="type" value="change-room-location" />
                                <input type="hidden" name="number" class="modifying-room-number-meta" />
                                <input type="text" name="location" id="modifying-room-location-input" maxlength="30" />
                                <input type="submit" value="更改" />
                            </form>
                            <header>更改房间最大人数</header>
                            <form action="process.php" method="post">
                                <input type="hidden" name="type" value="change-room-max" />
                                <input type="hidden" name="number" class="modifying-room-number-meta" />
                                <div>
                                    <input type="number" max="50" min="1" placeholder="最多人数" name="max-ps" id="max-living-people-input" required />
                                    <input type="submit" value="更改" />
                                </div>
                                <div class="smaller">*请确保人数不低于当前人数，否则将失败。</div>

                            </form>
                        </div>
                        <div>
                            <header>居住人列表</header>
                            <div>人数<span id="current-room-living-label"></span>/<span id="max-room-living-label"></span></div>
                            <div id="room-living-people-list" class="light-bg">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main>
        <h1>房间列表</h1>
        <details>
            <summary>筛选</summary>
            <div class="restrict-table">
                <div>
                    <label>房号<input type="text" id="number-restrict-input" placeholder="包含" /></label>
                    <label>状态
                        <select id="status-restrict-select">
                            <option value="no-restrict">无限制</option>
                            <option>正常</option>
                            <option>正在打扫</option>
                            <option>正在维修</option>
                            <option>停用</option>
                        </select>
                    </label>
                </div>
                <div>
                    <div>
                        更新时间
                        <span class="lighter smaller">从至今</span>
                        <input type="number" id="last-update-restrict-range-start" min="1" placeholder="天数">
                        <span class="lighter smaller">天以前的</span>
                        <input type="number" min="1" id="last-update-restrict-range-length" placeholder="默认1">
                        <span class="lighter smaller">天</span>
                    </div>
                    <div>
                        添加时间
                        <span class="lighter smaller">从至今</span>
                        <input type="number" id="added-time-restrict-range-start" min="1" placeholder="天数">
                        <span class="lighter smaller">天以前的</span>
                        <input type="number" min="1" id="added-time-restrict-range-length" placeholder="默认1">
                        <span class="lighter smaller">天</span>
                    </div>
                </div>
            </div>
        </details>
        <div>
            共有<span id="total-result-label"><?php echo $con->query("SELECT COUNT(*) AS `ct` FROM `rooms`")->fetch_assoc()["ct"]; ?></span>条记录
            <button id="clear-restrict-button">清除筛选</button>
        </div>
        <div class="data-list">
            <table>
                <?php
                foreach ($con->query("SELECT * FROM `rooms`")->fetch_all(MYSQLI_ASSOC) as $value) {
                    echo "<tr>",
                    "<td class='number-label'>", $value["number"], "</td>",
                    "<td class='status-label'>";
                    switch ($value["status"]) {
                        case 'normal':
                            echo "正常";
                            break;
                        case 'cleaning':
                            echo "正在打扫";
                            break;
                        case 'repairing':
                            echo "正在维修";
                            break;
                        case 'stop':
                        default:
                            echo "停用";
                            break;
                    }
                    echo "</td>";
                    echo "<td>" . $con->query("SELECT COUNT(*) AS `ct` FROM `checkios` WHERE `room` = '" . $con->real_escape_string($value["number"]) . "'")->fetch_assoc()["ct"] . "/" . $value["max"] . "人居住";
                    if ($value["status"] == "normal" && $con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")->num_rows == 0 && $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->num_rows == 0 && $con->query("SELECT * FROM `checkios` WHERE `room` = '" . $con->real_escape_string($value["number"]) . "'")->num_rows < $value["max"]) {
                        echo '<button onclick="setRoomToLiveIn(\'', addslashes($value["number"]), '\');">办理入住</button>';
                    }
                    echo "</td>",
                    "<td class='smaller'><div class='lighter'>地址</div><span class='added-time-label'>", $value["location"], "</span></td>",
                    "<td class='smaller'><div class='lighter'>最近状态更新</div><span class='last-update-label'>", $value["updatetime"], "</span></td>",
                    "<td class='smaller'><div class='lighter'>添加时间</div><span class='added-time-label'>", $value["addtime"], "</span></td>";
                    if (
                        $usertype == "system-admin" ||
                        (!is_null($userinfo["managepart"]) &&
                            strlen($con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($userinfo["managepart"]) . "'")->fetch_assoc()["includes"]) > 0 &&
                            in_array($value["number"], explode(',', $con->query("SELECT * FROM `areas` WHERE `name` = '" . $con->real_escape_string($userinfo["managepart"]) . "'")->fetch_assoc()["includes"])))
                    ) {
                        echo '<td><button onclick="modifyRoom(\'' . addslashes($value["number"]) . '\')">房间设置</button></td>';
                    } else {
                        echo "<td class='lighter'>无权限</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
            <?php include_once "embed/no_matched.php"; ?>
        </div>
        <div class="flex">
            <div>
                <?php if ($con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->num_rows != 0) { ?>
                    <div>
                        <form action="process.php" method="post">
                            <header>办理签出</header>
                            <div>您的既定预约房间号是<code>
                                    <?php
                                    echo $con->query("SELECT * FROM `rooms` WHERE `number` = " . $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->fetch_assoc()["room"])->fetch_assoc()["number"];
                                    ?>
                                </code>
                            </div>
                            <div>
                                时间为<code>
                                    <?php
                                    echo $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->fetch_assoc()["fromdate"];
                                    ?>
                                </code>➔<code>
                                    <?php
                                    echo $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->fetch_assoc()["todate"];
                                    ?>
                                </code>
                            </div>
                            <input type="hidden" name="type" value="check-out" />
                            <input type="submit" id="submit-request-checkin" value="确认签出" />
                        </form>
                    </div>
                <?php } else if ($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'check-in'")->num_rows == 0) { ?>
                    <div>
                        <form action="process.php" method="post">
                            <header>办理入住</header>
                            <input type="hidden" name="type" value="request-check-in" />
                            <input type="hidden" name="room-number" id="room-number-input" />
                            <div class="operation-info">
                                <div id="chosen-room-number-label">-/-</div>
                                <div>
                                    <input type="date" name="start-time" min="<?php echo date("Y-m-d") ?>" value="<?php echo date("Y-m-d") ?>" required id="book-start-time-input" />
                                    ➔
                                    <code id="end-book-time-label"></code>
                                    <span class="lighter">持续</span>
                                    <input type="number" name="during-time" id="book-during-time-input" required min="1" max="3650" />
                                    <span class="lighter">天</span>
                                </div>
                            </div>
                            <textarea name="reason" maxlength="100" placeholder="住房原因填写，可省略，不超过100个字符" class="reason"></textarea>
                            <div class="light-bg">
                                <input type="reset" value="重置" />
                                <input type="submit" id="submit-request-checkin" value="提交申请 ➔" disabled />
                            </div>

                        </form>
                    </div>
                <?php } else {  ?>
                    <div>
                        <header class="checking-request-info">您的提交正在被审核……</header>
                    </div>
                <?php } ?>

            </div>
            <div>
                <?php if ($usertype == "system-admin") { ?>
                    <header>添加房间</header>
                    <form action="process.php" method="post">
                        <input type="hidden" name="type" value="add-room">
                        <div><input type="text" name="room-number" placeholder="房间号" maxlength="10" required /></div>
                        <div><input type="number" max="50" min="1" placeholder="最多人数" name="max-ps" required /></div>
                        <div><input type="text" maxlength="30" placeholder="地址" required name="location" /></div>
                        <input type="reset" value="重置" />
                        <input type="submit" value="添加" />
                    </form>
                    <div>
                        <button onclick="openPopup('管理区域设置')">管理区域设置</button>
                    </div>
                <?php } ?>
            </div>
        </div>

    </main>
</body>
<script src="js/xhr.js"></script>
<script src="js/popup.js"></script>
<script src="js/roomlist.js"></script>
<script src="js/validInput.js"></script>

</html>