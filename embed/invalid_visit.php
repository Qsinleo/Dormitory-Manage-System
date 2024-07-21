<?php if (
    ($vis_limit == 1 && is_null($usertype)) ||
    ($vis_limit == 2 && (is_null($usertype) || $usertype == "inactived")) ||
    ($vis_limit == 3 && (is_null($usertype) || $usertype == "inactived" || $usertype == "staff")) ||
    ($vis_limit == 4 && $usertype != "system-admin")
) { ?>

    <main>
        <style>
            details#invalid-access-more-info {
                margin: 10px;
                background-color: var(--light-bg);
                padding: 5px;
                border-radius: 5px;
                transition-duration: .1s;
            }

            details#invalid-access-more-info:hover {
                background-color: var(--input-hover-color);
            }

            details#invalid-access-more-info:hover {
                background-color: var(--input-active-color);
            }

            #access-show-table {
                background-color: var(--light-bg);
                padding: 5px;
                border-radius: 5px;
            }

            #access-show-table tr {
                margin: 5px;
                border-bottom: 1px solid var(--body-color);
            }

            #access-show-table td {
                text-align: center;
                padding: 5px;
                height: 20px;
                width: 100px;
                border-radius: 5px;
            }

            #access-show-table td.color-block {
                border: 1px solid var(--body-color);
                border-collapse: collapse;
            }

            #access-show-table td.lighted {
                background-color: var(--second-body-color);
            }
        </style>
        <h1>＞﹏＜非法访问！</h1>
        <p>你似乎来到了你不应该出现的领域！(⊙x⊙;)</p>
        <p>
        <table id="access-show-table">
            <tr>
                <th></th>
                <th>登录</th>
                <th>已激活</th>
                <th>中级管理员</th>
                <th>系统管理员</th>
            </tr>
            <tr>
                <td>你的权限</td>
                <td class="color-block <?php if (!is_null($usertype)) echo 'lighted'; ?>"></td>
                <td class="color-block <?php if (!is_null($usertype) && $usertype != "inactived") echo 'lighted'; ?>"></td>
                <td class="color-block <?php if ($usertype == "admin" && $usertype == "system-admin") echo 'lighted'; ?>"></td>
                <td class="color-block <?php if ($usertype == "system-admin") echo 'lighted'; ?>"></td>
            </tr>
            <tr>
                <td>要求权限</td>
                <td class="color-block <?php if ($vis_limit >= 1) echo 'lighted'; ?>"></td>
                <td class="color-block <?php if ($vis_limit >= 2) echo 'lighted'; ?>"></td>
                <td class="color-block <?php if ($vis_limit >= 3) echo 'lighted'; ?>"></td>
                <td class="color-block <?php if ($vis_limit == 4) echo 'lighted'; ?>"></td>
            </tr>
        </table>
        </p>
        <p>
            <button onclick="history.back();">返回上页</button>
            <button onclick="location.href = 'index.php';">返回首页</button>
            <button onclick="location.href = 'access.php';">登录/注册</button>
        </p>
    </main>
<?php
    exit;
} ?>