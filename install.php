<?php
define("ENV_FILE_PATH", "embed/env_config.php");
require_once 'embed/mail.php';
session_start();
if (file_exists(ENV_FILE_PATH)) {
    $avaliable = true;
    try {
        // 是否合法
        include_once ENV_FILE_PATH;
    } catch (\Throwable $th) {
        $avaliable = false;
    }
    if ($avaliable) {
        header("Location: index.php");
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>数据库安装 / Database Setup</title>
    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="css/pages/install.css">
</head>

<body>
    <h1>Ethroom 数据库安装</h1>
    <div class="container">
        <div>
            <h2>安装提示</h2>
            <h3>提醒</h3>
            <ul>
                <li>
                    <b>安装前请先确保你的MySQL服务处于运行状态！</b>
                </li>
                <li>
                    <b>如数据库内已有数据，将会被重置！</b>
                </li>
                <li>
                    <b>授权码不一定是邮箱账号的密码！</b>若你使用的是QQ邮箱等其他运营商，请先查看你的SMTP服务是否开启，然后获取授权码！
                </li>
                <li>
                    若你不知道数据库或邮件服务相关信息，可向服务提供商咨询。
                </li>
                <li>
                    邮件服务配置完成后，在配置的“发出人”邮箱中会收到一条邮件。
                </li>
                <li>
                    如显示<code>Access denied for user ... (USING PASSWORD ...)</code>，代表数据库用户名/密码错误，请检查后重新填写。
                </li>
                <li>
                    如显示<code>SMTP Error: Could not connect to SMTP host.</code>，代表邮箱服务主机无法连接，请检查后重新填写。
                </li>
                <li>
                    如显示<code>SMTP Error: Could not authenticate.</code>，代表邮箱服务账户/授权码填写错误，请检查后重新填写。
                </li>
            </ul>
        </div>
        <div>
            <h2>信息输入</h2>
            <form method="post">
                <div class="form-container">
                    <div><label for="db_host">数据库地址</label>
                        <input type="text" id="db_host" name="db_host" required>
                    </div>
                    <div><label for="db_username">数据库用户名</label>
                        <input type="text" id="db_username" name="db_username" required>
                    </div>
                    <div><label for="db_password">数据库密码(可为空)</label>
                        <input type="password" id="db_password" name="db_password">
                    </div>
                    <div><label for="db_databasename">数据库名称</label>
                        <input type="text" id="db_databasename" name="db_databasename" required>
                    </div>
                    <div><label for="root_name">超级管理员邮箱</label>
                        <input type="email" id="root_email" name="root_email" required>
                    </div>
                    <div><label for="root_password">超级管理员密码(默认123456)</label>
                        <input type="password" id="root_password" name="root_password" required minlength="6" maxlength="18" value="123456">
                    </div>
                    <div><label for="email_serverhost">邮件服务提供商地址(SMTP)</label>
                        <input type="text" id="email_serverhost" name="email_serverhost" required>
                    </div>
                    <div><label for="email_serverport">邮件服务提供商端口</label>
                        <input type="number" id="email_serverport" name="email_serverport" min="0" value="465" required>
                    </div>
                    <div><label for="email_username">邮件服务用户名</label>
                        <input type="text" id="email_username" name="email_username" required>
                    </div>
                    <div><label for="email_password">邮件服务授权码</label>
                        <input type="password" id="email_password" name="email_password" required>
                    </div>
                    <div><label for="email_senderadd">系统邮件发出人地址</label>
                        <input type="email" id="email_senderadd" name="email_senderadd" required>
                    </div>
                    <div><label for="service_title">系统名称</label>
                        <input type="text" id="service_title" name="service_title" required value="Ethroom - " placeholder="起一个个性化的名字吧！" maxlength="25">
                    </div>
                </div>
                <input type="submit" value="开始设置Ethroom">
            </form>
        </div>
        <div>
            <h2>安装信息</h2>
            <h3>运行日志</h3>
            <div class="run-logs">
                <?php
                // 检查是否提交了表单
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // 获取表单提交的数据
                    $recv = $_POST;

                    // 清空表单
                    $_POST = array();

                    function generateDefinesToFile($array, $targetFile)
                    {
                        $defines = '';
                        foreach ($array as $key => $value) {
                            $defines .= "define('" . addslashes((string)$key) . "', '" . addslashes((string)$value) . "');" . PHP_EOL;
                        }

                        // 将生成的define代码写入目标文件
                        file_put_contents($targetFile, '<?php ' . PHP_EOL . $defines . '?>');
                        if (file_exists($targetFile)) {
                            echo "<div class='success'>配置文件生成成功</div>";
                        } else {
                            quitRunning("配置文件生成失败");
                        }
                    }

                    function quitRunning($mes)
                    {
                        echo "<div class='error'>$mes</div>";
                        echo "</div><div><div class='error setup-result'>安装失败</div><p>试着重新输入信息吧……</p></div>";
                        exit;
                    }

                    // 连接到MySQL服务器
                    try {
                        $conn = new mysqli($recv["db_host"], $recv["db_username"], $recv["db_password"]);
                    } catch (\Throwable $th) {
                        quitRunning("连接<code>" . $recv["db_host"] . "</code>错误：" . $th->getMessage());
                    }
                    // 检查数据库是否存在
                    $result = $conn->query("SHOW DATABASES LIKE '" . $conn->real_escape_string($recv["db_name"]) . "'");

                    if ($result->num_rows == 0) {
                        // 如果数据库不存在，创建数据库
                        $create_db_query = "CREATE DATABASE `" . $conn->real_escape_string($recv["db_name"]) . "`";

                        if ($conn->query($create_db_query) === TRUE) {
                            echo "<div class='success'>成功创建数据库</div>";
                        } else {
                            quitRunning("创建数据库时错误: " . $conn->error);
                        }
                    } else {
                        echo "<div class='success'>数据库已存在</div>";
                    }

                    // 选择数据库
                    $conn->select_db($recv["db_name"]);

                    // 读取SQL文件
                    $sql_file = "embed/dms-data.sql";
                    $sql = file_get_contents($sql_file);
                    // 执行SQL语句
                    $conn->multi_query($sql);
                    while ($conn->more_results()) {
                        // 查看是否有错误
                        try {
                            $conn->next_result();
                        } catch (\Throwable $err) {
                            quitRunning("执行SQL时错误: " . $conn->error);
                        }
                    }
                    echo "<div class='success'>数据库结构已创建</div>";

                    if ($conn->query("INSERT INTO `users` VALUES (NULL,'" . $recv["root_email"] . "', '超级管理员',SHA1('" . $recv["root_password"] . "') , 1145, 'SYSTEM-ADMIN', 'system-admin', NULL, 1, NULL, CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP())") === true) {
                        echo "<div class='success'>超级用户已创建</div>";
                    } else {
                        quitRunning("超级用户创建失败：" . $conn->error);
                    }

                    // 创建和更改env_config文件
                    generateDefinesToFile($recv, ENV_FILE_PATH);
                    try {
                        send_mail("<h1>Ethroom邮件配置已成功</h1>当你看到这封邮件，即表明Ethroom邮件配置已成功。如果你未曾配置过这类东西，请忽略。", $recv["email_senderadd"], "邮件配置成功", "", $recv["email_serverhost"], $recv["email_serverport"], $recv["email_username"], $recv["email_password"], $recv["email_senderadd"]);
                    } catch (\Throwable $th) {
                        quitRunning("邮件配置错误：" . $th->getMessage());
                    }
                    echo "<div class='success'>邮件配置成功</div>";
                    echo "</div>
                    <div>
                        <div class='success setup-result'>安装成功！</div>
                            <div>
                                <b>超级管理员</b><br>账号：<code>" . $recv["root_email"] . "</code><br>密码：<code>" . $recv["root_password"] . "</code>
                            </div>
                        <button class='link-button' onclick='location.href=\"index.php\"'>前往主页</button>
                    </div>
                    ";
                    // 关闭数据库连接
                    $conn->close();
                } else {
                    echo "</div><div class='setup-result not-started'>🕒尚未开始</div>";
                }
                ?>
            </div>

</body>

</html>