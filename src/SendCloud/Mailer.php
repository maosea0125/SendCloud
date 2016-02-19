<?php

namespace SendCloud;

use \SendCloud\SendCloud;

/**
 * SendCloud（https://sendcloud.sohu.com）
 */

class Mailer extends SendCloud
{
    protected $module = 'mail';

    // 发件人地址. 举例: support@ifaxin.com, 爱发信支持<support@ifaxin.com>
    protected $from;

    // 发件人名称. 显示如: ifaxin客服支持<support@ifaxin.com>
    protected $fromName;

    // 收件人地址. 多个地址使用';'分隔, 如 ben@ifaxin.com;joe@ifaxin.com
    protected $to = array();

    // 标题. 不能为空
    protected $subject;

    // 邮件的内容. 邮件格式为text/html
    protected $html;

    // 邮件的内容. 邮件格式为 text/plain
    protected $plain;

    // 抄送地址. 多个地址使用';'分隔
    protected $cc = array();

    // 密送地址. 多个地址使用';'分隔
    protected $bcc = array();

    // 设置用户默认的回复邮件地址. 如果 replyTo 没有或者为空, 则默认的回复邮件地址为 from
    protected $replyTo;

    // 本次发送所使用的标签ID. 此标签需要事先创建
    protected $labelId;

    // 邮件头部信息. JSON 格式, 比如:{"header1": "value1", "header2": "value2"}
    protected $headers = array();

    // 邮件附件. 发送附件时, 必须使用 multipart/form-data 进行 post 提交 (表单提交)
    protected $attachments = array();

    // 默认值: true. 是否返回 emailId. 有多个收件人时, 会返回 emailId 的列表
    protected $respEmailId = true;

    // 默认值: false. 是否使用回执
    protected $useNotification = false;

    // 默认值: false. 是否使用地址列表发送. 比如: to=group1@maillist.sendcloud.org;group2@maillist.sendcloud.org
    protected $useAddressList = false;

    protected $isUseTemplate = false;

    // 邮件模板调用名称
    protected $templateInvokeName;

    // 邮件模板中的参数, 如果多个收件人, 则与收件人一一对应
    protected $templateVars = array();

    public function setFrom($from){
        $this->from = (string) $from;
    }

    public function setFromName($fromName){
        $this->fromName = (string) $fromName;
    }

    public function setTo($to){
        if(!is_array($to)){
            $to = array($to);
        }
        $this->to = $to;
    }

    public function setSubject($subject){
        $this->subject = (string) $subject;
    }

    public function setHtml($html){
        $this->html = (string) $html;
    }

    public function setPlain($plain){
        $this->plain = (string) $plain;
    }

    public function setCc($cc){
        if( !is_array($cc) ){
            $cc = array($cc);
        }
        $this->cc = $cc;
    }

    public function setBcc($bcc){
        if( !is_array($bcc) ){
            $bcc = array($bcc);
        }
        $this->bcc = $bcc;
    }

    public function setReplyTo($replyTo){
        $this->replyTo = (string) $replyTo;
    }

    public function setLabelId($labelId){
        $this->labelId = intval($labelId);
    }

    public function setHeaders($headers){
        if( !is_array($headers) ){
            $headers = array($headers);
        }

        $this->headers = $headers;
    }

    public function setAttachments($attachments){
        if( !is_array($attachments) ){
            $attachments = array($attachments);
        }

        $this->attachments = $attachments;
    }

    public function setRespEmailId($respEmailId){
        $this->respEmailId = (boolean) $respEmailId;
    }

    public function setUseNotification($useNotification){
        $this->useNotification = (boolean) $useNotification;
    }

    public function setUseAddressList($useAddressList){
        $this->useAddressList = (boolean) $useAddressList;
    }

    public function setTemplateInvokeName($templateInvokeName){
        $this->isUseTemplate = true;
        $this->templateInvokeName = (string) $templateInvokeName;
    }

    public function setTemplateVars($templateVars){
        $this->templateVars = $templateVars;
    }

    /**
     * 发送邮件
     */
    public function send(){
        $action = $this->isUseTemplate ? 'sendtemplate' : 'send';

        $parameters['apiUser'] = $this->apiUser;
        $parameters['apiKey']  = $this->apiKey;
        if($this->from) $parameters['from'] = $this->from;
        if($this->fromName) $parameters['fromName'] = $this->fromName;
        if( !empty($this->to) ) $parameters['to'] = implode(';', $this->to);
        if($this->subject) $parameters['subject'] = $this->subject;
        if($this->html) $parameters['html'] = $this->html;
        if($this->plain) $parameters['plain'] = $this->plain;
        if( !empty($this->cc) ) $parameters['cc'] = implode(';', $this->cc);
        if( !empty($this->bcc) ) $parameters['bcc'] = implode(';', $this->bcc);
        if($this->replyTo) $parameters['replyTo'] = $this->replyTo;
        if($this->labelId) $parameters['labelId'] = $this->labelId;
        if( !empty($this->headers) ) $parameters['headers'] = json_encode($this->headers);
        if($this->respEmailId){
            $parameters['respEmailId'] = 'true';
        }else{
            $parameters['respEmailId'] = 'false';
        }
        if($this->useNotification){
            $parameters['useNotification'] = 'true';
        }
        if($this->useAddressList){
            $parameters['useAddressList'] = 'true';
        }

        // 附件
        if( !empty($this->attachments) ){
            $attachments = array();
            foreach ($this->attachments as $key => $attachment) {
                $parameters['attachments['.$key.']'] = '@' . $attachment;
            }
        }

        // 模板发送
        if($this->templateInvokeName) $parameters['templateInvokeName'] = $this->templateInvokeName;

        // 设置xsmtpapi信息
        if($this->isUseTemplate){
            $parameters['xsmtpapi']['to'] = $this->to;
            if( !empty($this->templateVars) ) $parameters['xsmtpapi']['sub'] = $this->templateVars;
        }

        if( isset($parameters['xsmtpapi']) ){
            $parameters['xsmtpapi'] = json_encode($parameters['xsmtpapi']);
        }

        return $this->sendRequest($this->module, $action, $parameters);
    }

    public function getEmailIdList(){
        $responseInfo = $this->getResponseInfo();
        return isset($responseInfo['emailIdList']) ? $responseInfo['emailIdList'] : array();
    }
}
