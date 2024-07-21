<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';
require_once 'env_config.php';

date_default_timezone_set("PRC");

function send_mail($mainbody, $to, $title, $altbody = "", $host = null, $port = null, $username = null, $password = null, $sender = null): void
{
    function get_path()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
    }
    $mail = new PHPMailer(true);                        // Passing `true` enables exceptions
    //服务器配置
    $mail->CharSet = "UTF-8";                     //设定邮件编码
    $mail->SMTPDebug = 0;                        // 调试模式输出
    $mail->isSMTP();                             // 使用SMTP
    $mail->Host = is_null($host) ? email_serverhost : $host;                // SMTP服务器
    $mail->SMTPAuth = true;                      // 允许 SMTP 认证
    $mail->Username = is_null($username) ? email_username : $username;                // SMTP 用户名  即邮箱的用户名
    $mail->Password = is_null($password) ? email_password : $password;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
    $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
    $mail->Port = is_null($port) ? email_serverport : $port;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

    $mail->setFrom(is_null($sender) ? email_senderadd : $sender, '[' . service_title . '](Ethroom)系统');  //发件人
    $mail->addAddress($to, '用户');  // 收件人
    //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
    //$mail->addCC('cc@example.com');                    //抄送
    //$mail->addBCC('bcc@example.com');                    //密送

    //发送附件
    // $mail->addAttachment('../xy.zip');         // 添加附件
    // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名

    //Content
    $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
    $mail->Subject = $title;
    $mail->Body    = "
        <style>
        h1{
            margin:10px;
            text-decoration:2px underline orange;
        }
        </style>
        <div style='width:fit-content;text-align:center;margin:auto;'>
        <div style='
        color:gray;
        background-color:aliceblue;
        padding:8px;
        border-top-left-radius:8px;
        border-top-right-radius:8px;
        '>[" . service_title . "]的系统消息</div>
        <div style='padding:10px;'>
        " .
        $mainbody .
        "</div>
        <footer style='
        font-size:small;
        margin-top:12px;
        padding:8px 100px;
        background-color:aliceblue;
        border-bottom-left-radius:8px;
        border-bottom-right-radius:8px;
        border-bottom:2px solid cyan;
        '>" . date('Y-m-d H:i:s') .
        " (UTC+8)
        <div style='color:gray;'><a href='" . get_path() . "index.php'>前往主页</a> | <a href='" . get_path() . "access.php'>前往登录</a> | Powered by Ethroom.</div>
        </footer></div>";
    $mail->AltBody = $altbody;
    $mail->send();
}
