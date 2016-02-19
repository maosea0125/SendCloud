<?php

require 'vendor/autoload.php';

use SendCloud\Mailer;

$mailer = new Mailer('maosea0125_test_NjiWJn', 'c9PlzndOB2cmlL7m');
$mailer->setFrom('maosea0125@163.com');
$mailer->setFromName('john.mao');
$mailer->setTo('john.mao@expacta.com.cn');
$mailer->setSubject('从SendCloud发送了一封测试邮件');
$mailer->setHtml('你太棒了！你已成功的从SendCloud发送了一封测试邮件，接下来快登录前台去完善账户信息吧！');
// $mailer->setCc('maosea0125@163.com');
// $mailer->setBcc('maosea0125@gmail.com');
$mailer->setReplyTo('maosea0125@163.com');

$mailer->setAttachments(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test.php',
    )
);

$mailer->setTemplateInvokeName('citizen_app_material_request');
$mailer->setTemplateVars(array(
    '%username%' => array('john.mao'),
));

$result = $mailer->send();

if(!$result){
    var_dump($mailer->getResponseStatusCode(), $mailer->getResponseMessage(), $mailer->getResponseInfo());
}else{
    var_dump($mailer->getEmailIdList());
}