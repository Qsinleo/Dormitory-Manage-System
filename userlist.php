<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="css/pages/list.css">
    <title>用户列表</title>
</head>

<body>
    <?php
    $vis_limit = 4;
    require_once "embed/sidenav.php";
    include_once "embed/invalid_visit.php";
    include_once "embed/loading_bg.php";
    ?>
    <main>
        <h1>用户列表</h1>
        <details>
            <summary>筛选</summary>
            <div class="restrict-table">
                <div>
                    <label>ID<input type="number" id="id-restrict-input" min="1" placeholder="包含" /></label>
                    <label>真名<input type="text" id="realname-restrict-input" placeholder="包含" /></label>
                    <label>邮箱<input type="email" id="email-restrict-input" placeholder="包含" /></label>
                </div>
                <div>
                    <label>工号<input type="text" id="work-id-restrict-input" placeholder="包含" /></label>
                    <label>权限<select id="access-restrict-select">
                            <option value="no-restrict">无限制</option>
                            <option value="员工">员工</option>
                            <option value="中级管理员">管理员</option>
                            <option value="系统管理员">系统管理员(我)</option>
                        </select>
                    </label>
                    <label>激活状态
                        <select id="actived-restrict-select">
                            <option value="no-restrict">无限制</option>
                            <option value="已激活">仅已激活</option>
                            <option value="未激活">仅未激活</option>
                        </select>
                    </label>
                </div>
                <div>
                    <div>
                        最近登录时间
                        <span class="lighter smaller">从至今</span>
                        <input type="number" id="last-login-restrict-range-start" min="1" placeholder="天数">
                        <span class="lighter smaller">天以前的</span>
                        <input type="number" min="1" id="last-login-restrict-range-length" placeholder="默认1">
                        <span class="lighter smaller">天</span>
                    </div>
                    <div>
                        注册时间
                        <span class="lighter smaller">从至今</span>
                        <input type="number" id="register-restrict-range-start" min="1" placeholder="天数">
                        <span class="lighter smaller">天以前的</span>
                        <input type="number" min="1" id="register-restrict-range-length" placeholder="默认1">
                        <span class="lighter smaller">天</span>
                    </div>
                </div>
            </div>
        </details>
        <div>
            共有<span id="total-result-label"><?php echo $con->query("SELECT COUNT(*) AS `ct` FROM `users`")->fetch_assoc()["ct"]; ?></span>条记录
            <button id="clear-restrict-button">清除筛选</button>
        </div>
        <div class="data-list">
            <table>
                <?php
                foreach ($con->query("SELECT * FROM `users`")->fetch_all(MYSQLI_ASSOC) as $value) {
                    echo "<tr>",
                    "<td><span class='id-label'>", $value["id"], "</span></td>",
                    "<td><img src='" . (is_null($value["header"]) ? "img/stuff.webp" : data_uri($value["header"], "image/jpg")) . "' class='header-img-small'/></td>",
                    "<td><span class='realname-label'>", $value["realname"], "</span></td>",
                    "<td><a href='mailto:", $value["email"], "' class='email-label'>", $value["email"], "</a></td>",
                    "<td><div class='access-label'>", ($value["accessment"] == "staff" ? "员工" : ($value["accessment"] == "admin" ? "中级管理员" : "系统管理员")), "</div>
                    <div class='actived-label smaller lighter'>", ($value["actived"] == 0 ? "未激活" : "已激活"), "</div></td>",
                    "<td class='smaller'><div class='lighter'>工号</div><span class='work-id-label'>", $value["workid"], "</span></td>",
                    "<td class='smaller'><div class='lighter'>住所</div><span class='live-in-label'>";
                    $live_in = $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $value["id"])->fetch_assoc();
                    if ($live_in) {
                        echo $live_in["room"];
                    } else {
                        echo "无";
                    }
                    echo "</span></td>",
                    "<td class='smaller'><div class='lighter'>权限</div><span class='manage-part-label'>";
                    if ($value["accessment"] == "system-admin") {
                        echo "全部房间";
                    } else {
                        echo is_null($value["managepart"]) ? "(无)" : $value["managepart"];
                    }
                    echo "</span></td>",
                    "<td class='smaller'><div class='lighter'>最近登录</div><span class='last-login-label'>", $value["logintime"], "</span></td>",
                    "<td class='smaller'><div class='lighter'>注册时间</div><span class='register-label'>", $value["registertime"], "</span></td>";
                    if ($usertype == "system-admin") {
                        echo "<td>";
                        if ($value["accessment"] != "system-admin") {
                            echo "<form action='process.php' method='post' class='inline' onsubmit='return secondConfirm()'>
                                        <input type='hidden' name='type' value='delete-user' />
                                        <input type='hidden' name='id' value='" . $value["id"] . "' />
                                        <input type='submit' value='删除' class='dangerous'/>
                                    </form>";
                        } else {
                            echo "<span class='lighter'>不可操作</span>";
                        }
                        if ($value["accessment"] == "admin") {
                            echo "<button onclick=\"getUserManage(", $value["id"], ")\">更改权限</button>";
                        }
                        echo "</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
            <?php include_once "embed/no_matched.php"; ?>
        </div>
        <div class="light-bg">
            <header>修改管理区域</header>
            <form action="process.php" method="post" id="change-manage-form">
                <input type="hidden" name="type" value="change-user-manage" />
                <input type="hidden" name="id" id="change-id-meta" />
                <table>
                    <tr>
                        <td>选择的管理人员</td>
                        <td><code id="user-change-label">未选择</code></td>
                    </tr>
                    <tr>
                        <td>管理区域名称</td>
                        <td><select id="area-name-select" name="area-name">
                                <option selected>请先选择</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div>
                    <button id="reset-user-manage-change" type="button">取消更改</button>
                    <button id="reload-user-manage-change" type="button">刷新管理</button>
                    <input type="submit" value="提交更改 ➔" disabled id="submit-user-manage-change" />
                </div>
            </form>
        </div>
    </main>
</body>
<script src="js/xhr.js"></script>
<script src="js/userlist.js"></script>
<script src="js/validInput.js"></script>

</html>