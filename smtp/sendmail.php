<?php


    require_once 'email.class.php';
    //******************** 配置信息 ********************************
    $smtpserver = 'smtp.qq.com';//SMTP服务器
    $smtpserverport = 25;//SMTP服务器端口
    $smtpusermail = '77522325@qq.com';//SMTP服务器的用户邮箱
    //$smtpemailto = $_POST['toemail'];//发送给谁
    $smtpemailto = '77522325@qq.com';//发送给谁
    $smtpuser = '77522325';//SMTP服务器的用户帐号
    $smtppass = 'cat3830soft3830';//SMTP服务器的用户密码
    //$mailtitle = $_POST['title'];//邮件主题
    $mailtitle = 'This is a PHP mail test';//邮件主题
    //$mailcontent = '<h1>'.$_POST['content'].'</h1>';//邮件内容
    $mailcontent = '<h1>'.'This is a php mail test content'.'</h1>';//邮件内容
    $mailtype = 'HTML';//邮件格式（HTML/TXT）,TXT为文本邮件
    //************************ 配置信息 ****************************
    $smtp = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = false;//是否显示发送的调试信息
    $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);

    if ($state == '') {
        echo 'Send mail failed';
        exit();
    }
    echo 'Send mail successfully';
