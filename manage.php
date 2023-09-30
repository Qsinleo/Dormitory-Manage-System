<?php
/*

loginas:以……登录（null=访客，failed=登录失败，inactived=登录但未激活）
loginid:登录id（null=访客）

*/
require_once "header.php";
include_once "navpage.php";
if (is_null($_SESSION["loginid"])) {
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" href="css/manage.css">
    <title>管理</title>
</head>
<script src="js/dialogueOpenAndClose.js"></script>

<body>
    <h1>管理</h1>
    <div id="hide-screen">&nbsp;</div>
    <div id="dialogue">
        <button class="close" onclick="hideDialog()">×</button>
        <button class="collapse" onclick="smallDialog()">-</button>
        <form action="proceed.php" method="post" enctype="multipart/form-data" id="upload-header">
            <header>上传头像</header>
            <input type="hidden" name="type" value="change-header">
            <div>仅支持.png、.jpg和.jpeg的不超过1MB的图片文件！建议您上传正方形尺寸照片。</div>
            <input type="file" id="image" accept="image/png,image/jpg,image/jpeg" name="header" />
            <div id="drop">拖到此处以上传</div>
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-realname">
            <header>更改真实名称</header>
            <input type="hidden" name="type" value="change-realname">
            <input type="text" name="realname" maxlength="8" value="<?php echo $userinfo["realname"]; ?>" required />
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-access">
            <header>更改权限</header>
            <?php if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'upgrade-to-admin'")) == 0) { ?>
                <input type="hidden" name="type" value="change-access">
                <input type="hidden" name="toaccess" value="<?php echo $userinfo["accessment"] == "staff" ? "admin" : "staff" ?>">
                <div>确定要将权限修改为“<?php echo $userinfo["accessment"] == "staff" ? "中级管理员" : "员工" ?>”吗？<?php echo $userinfo["accessment"] == "staff" ? "这将需要系统管理员批准。" : "你将失去管理员权限！" ?></div>
                <input type="submit" value="Go(/≧▽≦)/">
            <?php
            } else { ?>
                <div>您已经发送过修改权限申请，请等待管理员同意。</div>
            <?php
            } ?>
        </form>
        <form action="proceed.php" method="post" id="change-depart">
            <header>修改部门</header>
            <input type="hidden" name="type" value="change-depart">
            <input type="text" name="depart" value="<?php echo $userinfo["department"]; ?>">
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-workid">
            <header>修改工号</header>
            <input type="hidden" name="type" value="change-workid">
            <input type="number" name="workid" value="<?php echo $userinfo["workid"]; ?>">
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-password">
            <header>修改密码</header>
            <input type="hidden" name="type" value="change-password">
            <table>
                <tr>
                    <td>请输入旧密码：</td>
                    <td><input type="password" id="old-password" required /></td>
                </tr>
                <tr>
                    <td>请输入新密码：</td>
                    <td><input type="password" name="new-password" required /></td>
                    <td id="password-info3"></td>
                </tr>
                <tr>
                    <td>请再次输入新密码：</td>
                    <td><input type="password" id="new-password-retype" required /></td>
                    <td id="password-info2"></td>
                </tr>
            </table>
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/">
        </form>
        <form action="proceed.php" method="post" id="change-email">
            <header>修改邮箱</header>
            <input type="hidden" name="type" value="change-email" />
            <label>请输入新的邮箱
                <input type="email" name="email" value="<?php echo $userinfo["mail"] ?>" required />
            </label>
            <span id="is-existed"></span>
            <input type="reset" />
            <input type="submit" value="Go(/≧▽≦)/" id="submit">
        </form>
        <form action="proceed.php" method="post" id="change-manage">
            <header>修改管理区域</header>
            <?php
            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'change-manage'")) == 0) { ?>
                <fieldset>
                    <input type="hidden" name="type" value="change-manage" />
                    <input type="hidden" name="room-data" id="room-data" />
                    当前管理房间号分别为：
                    <ul id="manage-parts">
                        <?php
                        if (!is_null($userinfo["managepartid"])) {
                            foreach (explode(',', $userinfo["managepartid"]) as $value) {
                                echo "<li>", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` WHERE `id` = " . $value))["number"],
                                "<button onclick='delRoom(this);' type='button'>删除</button>",
                                "</li>";
                            }
                        }
                        ?>
                    </ul>
                </fieldset>
                <fieldset>
                    <label>请输入想添加的房号：
                        <input type="number" id="room-number-add" />
                        <span id="room-info">请键入房号以开始检查</span>
                        <button type="button" onclick="addRoom()" id="add-room" disabled>添加</button>
                    </label>
                </fieldset>
                <input type="submit" value="Go(/≧▽≦)/">
            <?php } else { ?>
                <div>您的更改权限申请已经提交，在系统管理员未回复以前无法再次提交。</div>
            <?php } ?>
        </form>
    </div>
    <div>
        <h2>我的账号</h2>
        <?php
        if ($usertype == "inactived") {
        ?>
            <div class="non-actived"><?php echo $userinfo["realname"] ?>（ID：<?php echo $userinfo["id"] ?>），您的帐户尚未被批准。请检查邮箱，以便获取是否被通过（注册时已经自动发送了申请）。</div>
            <?php
            if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM `requests` WHERE requestid = " . $_SESSION["loginid"] . " AND `type` = 'register-allow'")) == 0) {
            ?>
                <form action="proceed.php" method="post">
                    <input type="hidden" name="type" value="request-register-allow">
                    <input type="hidden" name="request-to-be" value="<?php echo $userinfo["accessment"] ?>">
                    <input type="submit" value="申请批准" />
                </form>
            <?php
            } else {
                echo "<div>您已经发送过注册申请，请等待管理员同意。</div>";
            }
        } else {
            if (is_null($userinfo["header"])) {
                echo "<img src='img/stuff.webp' />";
            } else {
                echo "<img src='" . data_uri($userinfo["header"], "image/png") . "' />";
            }
            ?>
            <button onclick="openDialog('upload-header')">更改</button>
            <table>
                <tr>
                    <td><?php
                        echo $userinfo["mail"];
                        ?>
                        <button onclick="openDialog('change-email')">更改</button>
                    </td>
                    <td><?php echo $userinfo["realname"] ?>(ID:<?php echo $userinfo["id"] ?>)<button onclick="openDialog('change-realname')">更改</button></td>
                </tr>
                <tr>
                    <td><?php
                        if ($usertype == "staff") {
                            echo "员工";
                        } elseif ($usertype == "admin") {
                            echo "中级管理员";
                        } else {
                            echo "系统管理员";
                        }
                        if (!($usertype == "system-admin")) {
                        ?><button onclick="openDialog('change-access')">更改</button>
                        <?php } ?>
                    </td>
                    <td>工号：<?php echo $userinfo["workid"]; ?><button onclick="openDialog('change-workid')">更改</button></td>
                </tr>
                <tr>
                    <td>部门：<?php echo $userinfo["department"]; ?><button onclick="openDialog('change-depart')">更改</button></td>
                    <td>住所：<?php echo mysqli_num_rows(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $_SESSION["loginid"])) == 0 ? "无" : mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` WHERE id = " . mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `checkios` WHERE requestid = " . $userinfo["id"]))["roomid"]))["number"] ?></td>

                </tr>
                <tr>
                    <td>管理区域：
                        <?php
                        if ($usertype == "system-admin")
                            echo "全部房间";
                        else if (is_null($userinfo["managepartid"]))
                            echo "无";
                        else {
                            foreach (explode(",", $userinfo["managepartid"]) as $from_info) {
                                echo "<span style='margin:5px;background-color:blue;color:white'>", mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `rooms` where id=" . $from_info))["number"], "</span>";
                            }
                        }
                        if ($usertype == "admin") {
                            echo "<button onclick=\"openDialog('change-manage')\">更改</button>";
                        }
                        ?>
                    </td>
                    <td>最近登录时间：
                        <?php echo $userinfo["logintime"]; ?>
                    </td>
                </tr>
            </table>
        <?php
        }
        if ($usertype == "admin" || $usertype == "system-admin") {
        ?>
            <div><a href="accept.php">批准</a></div>
            <div><a href="userlist.php">用户列表</a></div>
            <div><a href="roomlist.php">房间列表</a></div>
        <?php } ?>
        <button onclick="openDialog('change-password')">更改密码</button>
        <button onclick="location.href = 'logout.php'">退出登录</button>
        <?php if ($usertype != "system-admin") { ?>
            <form action="proceed.php" method="post">
                <input type="submit" name="delete-account" value="删除账户" />
            </form>
        <?php } else { ?>
            <button disabled title="系统管理员无法删除账号">删除账号</button>
        <?php } ?>
    </div>
</body>
<script src="js/sha1.js"></script>
<script src="js/dropToUpload.js"></script>
<script src="js/verifyPassword.js"></script>
<script src="js/isExistedEmail.js"></script>
<script src="js/manageChange.js"></script>
<script>
    document.getElementById("image").onchange = () => {
        let sizeOfFile = 1024 * 1024 * 1;
        const item = document.getElementById("image");
        if (item.files[0] && item.files) {
            if (item.files[0].size > sizeOfFile) {
                alert("文件大小不能超出1MB!");
                document.getElementById("upload-header").reset();
            } else if (item.files[0].type != "image/png" && item.files[0].type != "image/jpg" && item.files[0].type != "image/jpeg") {
                alert("文件格式错误，当前：" + item.files[0].type);
                document.getElementById("upload-header").reset();
            }
        }

    }

    hideDialog();
</script>

</html>