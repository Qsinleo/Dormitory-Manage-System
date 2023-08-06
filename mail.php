<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './mail/Exception.php';
require './mail/PHPMailer.php';
require './mail/SMTP.php';

function send_mail($mainbody, $to, $title, $altbody = ""): void
{
    $mail = new PHPMailer(true);                        // Passing `true` enables exceptions
    try {
        //服务器配置
        $mail->CharSet = "UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'smtp.163.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = 'lylsnrc';                // SMTP 用户名  即邮箱的用户名
        $mail->Password = 'FBNBSABUHGFJRDDL';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom('lylsnrc@163.com', '系统');  //发件人
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
        $mail->Body    = "<div style='width:50%;box-shadow:grey 5px 7px 4px;margin:auto;'>" . $mainbody . date('Y-m-d H:i:s') . "</div>";
        $mail->AltBody = $altbody;
        $mail->send();
    } catch (Exception $e) {
        echo '邮件发送失败: ', $mail->ErrorInfo;
    }
}
