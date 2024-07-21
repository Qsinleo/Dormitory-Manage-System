<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/pages/manage.css">
    <title>管理</title>
</head>


<body>
    <?php
    $vis_limit = 1;
    require_once "embed/sidenav.php";
    include_once "embed/invalid_visit.php";
    include_once "embed/loading_bg.php";
    ?>
    <div id="popup-bg">
        <div id="popup-border">
            <div id="popup-controller">
                <div onclick="closePopup()">×</div>
                <header id="popup-title">上传头像</header>
            </div>
            <div id="popup-content">
                <div data-type="更改头像">
                    <form action="process.php" method="post" enctype="multipart/form-data" id="change-header-form">
                        <input type="hidden" name="type" value="change-header">
                        <p>仅支持.png、.jpg和. jpeg的不超过1MB的图片文件！建议您上传正方形尺寸照片。</p>
                        <input type="file" id="upload-image-input" accept="image/png,image/jpg,image/jpeg" name="header" required />
                        <span id="uploaded-image-status-label">等待上传……</span>
                        <div id="drop-image-box">拖到此处以上传</div>

                        <div class="form-controller">
                        </div>
                    </form>
                    <form action="process.php" method="post">
                        <input type="hidden" name="type" value="change-header">
                        <input type="submit" value="删除已有头像">
                    </form>
                </div>

                <form action="process.php" method="post" data-type="更改真名">
                    <input type="hidden" name="type" value="change-realname">
                    <input type="text" name="realname" maxlength="8" value="<?php echo $userinfo["realname"]; ?>" required />
                    <div class="form-controller">
                    </div>
                </form>
                <form action="process.php" method="post" data-type="更改权限">
                    <?php if ($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'upgrade-to-admin'")->num_rows == 0) { ?>
                        <input type="hidden" name="type" value="change-access">
                        <input type="hidden" name="toaccess" value="<?php echo $userinfo["accessment"] == "staff" ? "admin" : "staff" ?>">
                        <div>确定要将权限修改为<b><?php echo $userinfo["accessment"] == "staff" ? "中级管理员" : "员工" ?></b>吗？<?php echo $userinfo["accessment"] == "staff" ? "这将需要系统管理员批准。" : "你将失去管理员权限！" ?></div>
                        <div class="form-controller" data-needreset="false">
                        </div>
                    <?php
                    } else { ?>
                        <div>您已经发送过修改权限申请，请等待管理员同意。</div>
                    <?php
                    } ?>
                </form>
                <form action="process.php" method="post" data-type="更改部门">
                    <input type="hidden" name="type" value="change-depart">
                    <input type="text" name="depart" value="<?php echo $userinfo["department"]; ?>" required>
                    <div class="form-controller">
                    </div>
                </form>
                <form action="process.php" method="post" data-type="更改工号">
                    <input type="hidden" name="type" value="change-workid">
                    <input type="text" name="workid" value="<?php echo $userinfo["workid"]; ?>">
                    <div class="form-controller">
                    </div>
                </form>
                <form action="process.php" method="post" data-type="更改密码" id="change-password-form">
                    <input type="hidden" name="type" value="change-password">
                    <div><input type="password" required placeholder="旧密码" /></div>
                    <div><input type="password" name="new-password" id="new-password-input" required placeholder="新密码" /></div>
                    <div><input type="password" id="new-password-retype" required placeholder="再次输入新密码" /></div>
                    <div id="new-password-info"></div>
                    <div class="form-controller">
                    </div>
                </form>
                <form action="process.php" method="post" data-type="更改邮箱" id="change-email-form">
                    <input type="hidden" name="type" value="change-email" />
                    <input type="email" name="email" value="<?php echo $userinfo["email"] ?>" required id="email-input" />
                    <div id="email-is-existed"></div>
                    <div class="form-controller">
                    </div>
                </form>
                <form action="process.php" method="post" data-type="更改管理">
                    <?php
                    if ($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'change-manage'")->num_rows == 0) { ?>
                        <input type="hidden" name="type" value="request-manage-change" />
                        <input type="hidden" id="change-id-meta" value="<?php echo $_SESSION["loginid"]; ?>">
                        <select id="area-name-select" name="area-name"></select>
                        <div>
                            <button id="reload-user-manage-change" type="button">刷新管理</button>
                            <input type="submit" value="提交申请 ➔" disabled id="submit-user-manage-change" />
                        </div>
                    <?php } else { ?>
                        <div>您的更改管理区域至<code>
                                <?php
                                echo json_decode($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'change-manage'")->fetch_assoc()["param"], true)["area"];
                                ?></code>申请已经提交，无法再次提交。
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
    <main>
        <h1>管理</h1>
        <div class="profile-container">
            <h2>基本信息</h2>
            <div class="header-image-label">
                <?php
                if (is_null($userinfo["header"])) {
                    echo "<img src='img/stuff.webp'/>";
                } else {
                    echo "<img src='" . data_uri($userinfo["header"], "image/png") . "'/>";
                }
                ?>
                <button onclick="openPopup('更改头像')">更改</button>
            </div>
            <div class="flex">
                <div>
                    <span class="attribute-label">名称</span>
                    <span><?php echo $userinfo["realname"] ?><button onclick="openPopup('更改真名')">更改</button></span>
                </div>
                <div>
                    <span class="attribute-label">邮箱</span>
                    <span><?php echo $userinfo["email"] ?><button onclick="openPopup('更改邮箱')">更改</button></span>
                </div>
            </div>
            <div class="flex">
                <div>
                    <span class="attribute-label">ID</span>
                    <span><?php echo $userinfo["id"] ?></span>
                </div>
                <div>
                    <span class="attribute-label">身份</span>
                    <span><?php
                            if ($userinfo["accessment"] == "staff") {
                                echo "员工";
                            } elseif ($userinfo["accessment"] == "admin") {
                                echo "中级管理员";
                            } elseif ($userinfo["accessment"] == "system-admin") {
                                echo "系统管理员";
                            } ?>
                    </span>
                    <?php if ($usertype == "staff" || $usertype == "admin") { ?>
                        <button onclick="openPopup('更改权限')">更改</button>
                    <?php } ?>
                </div>
                <div>
                    <span class="attribute-label">已激活</span>
                    <span><?php echo $userinfo["actived"] ? "是" : "否"; ?></span>
                </div>
            </div>
            <h2>部门信息</h2>
            <?php
            if ($usertype == "inactived") {
            ?>
                <div>
                    <div>您的帐户尚未被批准。请检查邮箱，以便获取是否被通过（注册时已经自动发送了申请）。</div>
                </div>
                <?php
                if ($con->query("SELECT * FROM `requests` WHERE `requestid` = " . $_SESSION["loginid"] . " AND `type` = 'register-allow'")->num_rows == 0) {
                ?>
                    <form action="process.php" method="post">
                        <input type="hidden" name="type" value="request-register">
                        <input type="hidden" name="request-to-be" value="<?php echo $userinfo["accessment"] ?>">
                        <input type="submit" value="申请批准" />
                    </form>
                <?php
                } else {
                    echo "<div>您已经发送过注册申请，请等待管理员同意。</div>";
                }
            } else { ?>
                <div class="flex">
                    <div>
                        <span>工号</span>
                        <span><?php echo $userinfo["workid"]; ?><button onclick="openPopup('更改工号')">更改</button></span>
                    </div>
                    <div>
                        <span>部门</span>
                        <span><?php echo $userinfo["department"]; ?><button onclick="openPopup('更改部门')">更改</button></span>
                    </div>
                    <div>
                        <span>住所</span>
                        <span><?php echo $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $_SESSION["loginid"])->num_rows == 0 ? "无" : $con->query("SELECT * FROM `rooms` WHERE `id` = " . $con->query("SELECT * FROM `checkios` WHERE `requestid` = " . $userinfo["id"])->fetch_assoc()["room"])->fetch_assoc()["number"] ?></span>
                    </div>
                </div>
                <div class="flex">
                    <div>
                        <span>管理区域</span>
                        <?php
                        if ($usertype == "system-admin")
                            echo "<code>全部房间</code>";
                        else if (is_null($userinfo["managepart"]))
                            echo "<code>无</code>";
                        else {
                            echo $userinfo["managepart"];
                        }
                        if ($usertype == "admin") {
                            echo "<button onclick=\"getUserManage(" . $_SESSION["loginid"] . ")\">更改</button>";
                        }
                        ?>
                    </div>
                    <div>
                        <span>最近登录时间</span>
                        <span><?php echo $userinfo["logintime"]; ?></span>
                    </div>
                </div>
            <?php
            }
            ?>
            <h2>账号操作</h2>
            <div>
                <button onclick="openPopup('更改密码')">更改密码</button>
                <button onclick="confirmLogout()">退出登录</button>
                <?php if ($usertype != "system-admin") { ?>
                    <form action="process.php" method="post" class="inline" onsubmit="return secondConfirm()">
                        <input type="hidden" name="type" value="delete-account">
                        <input type="submit" class="dangerous" value="删除账户" />
                    </form>
                <?php } else { ?>
                    <button disabled title="系统管理员无法删除账号">删除账号</button>
                <?php } ?>
            </div>

        </div>
    </main>
</body>
<script src="js/xhr.js"></script>
<script src="js/popup.js"></script>
<script src="js/manage.js"></script>

</html>