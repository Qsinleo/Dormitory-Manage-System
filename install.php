<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Database Setup</title>
</head>

<body>
    <h1>Database Setup</h1>

    <?php
    // 检查是否提交了表单
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // 获取表单提交的数据
        $db_host = $_POST["db_host"];
        $db_username = $_POST["db_username"];
        $db_password = $_POST["db_password"];
        $db_name = $_POST["db_name"];

        // 连接到MySQL服务器
        $conn = new mysqli($db_host, $db_username, $db_password);

        // 检查连接是否成功
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // 检查数据库是否存在
        $result = $conn->query("SHOW DATABASES LIKE '$db_name'");

        if ($result->num_rows == 0) {
            // 如果数据库不存在，创建数据库
            $create_db_query = "CREATE DATABASE $db_name";

            if ($conn->query($create_db_query) === TRUE) {
                echo "Database created successfully.<br>";
            } else {
                echo "Error creating database: " . $conn->error . "<br>";
            }
        } else {
            echo "Database already exists.<br>";
        }

        // 选择数据库
        $conn->select_db($db_name);

        // 读取SQL文件
        $sql_file = "dms-data.sql";
        $sql = file_get_contents($sql_file);

        // 执行SQL语句
        if ($conn->multi_query($sql) === TRUE) {
            echo "SQL file executed successfully.<br>";

            // 创建和更改config.json文件
            $config_data = array(
                "db_host" => $db_host,
                "db_username" => $db_username,
                "db_password" => $db_password,
                "db_name" => $db_name
            );
            $config_json = json_encode($config_data, JSON_PRETTY_PRINT);

            // 写入config.json文件
            file_put_contents('config.json', $config_json);
            echo "config.json file created and updated successfully.";
        } else {
            echo "Error executing SQL file: " . $conn->error;
        }

        // 关闭数据库连接
        $conn->close();
    }
    ?>

    <form method="post">
        <label for="db_host">Database Host:</label><br>
        <input type="text" id="db_host" name="db_host"><br><br>

        <label for="db_username">Database Username:</label><br>
        <input type="text" id="db_username" name="db_username"><br><br>

        <label for="db_password">Database Password:</label><br>
        <input type="password" id="db_password" name="db_password"><br><br>

        <label for="db_name">Database Name:</label><br>
        <input type="text" id="db_name" name="db_name"><br><br>

        <input type="submit" value="Setup Database">
    </form>
</body>

</html>