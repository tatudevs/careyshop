<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知扩展库
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/13
 */

namespace util;

use AlibabaCloud\Client\AlibabaCloud;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use think\facade\Config;

class Notice
{
    /**
     * 发送短信
     * @access public
     * @param string $mobile 手机号码
     * @param string $body   发送正文
     * @param string $code   模板编号
     * @throws
     */
    public static function sendSms(string $mobile, string $body, string $code)
    {
        // 获取配置信息
        $setting = json_decode(Config::get('careyshop.notice.sms'), true);

        // 待发送数据
        $data['query'] = [
            'PhoneNumbers'  => $mobile,
            'SignName'      => $setting['sign']['value'],
            'TemplateCode'  => $code,
            'TemplateParam' => $body,
        ];

        AlibabaCloud::accessKeyClient($setting['key_id']['value'], $setting['key_secret']['value'])
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options($data)
            ->request();
    }

    /**
     * @param string $to          收件人
     * @param string $subject     标题
     * @param string $body        正文
     * @param array  $attachments 附件
     * @throws
     */
    public static function sendEmail(string $to, string $subject, string $body, array $attachments = [])
    {
        // 获取配置信息
        $setting = json_decode(Config::get('careyshop.notice.email'), true);

        // 设置PHPMailer
        $mail = new PHPMailer();
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $setting['secure']['value'];
        $mail->Host = $setting['host']['value'];
        $mail->Port = $setting['port']['value'];
        $mail->Username = $setting['username']['value'];
        $mail->Password = $setting['password']['value'];
        $mail->setLanguage('zh_cn');
        $mail->isSMTP();

        // 设置接收者
        $mail->setFrom($setting['from']['value'], $setting['nickname']['value']);
        $mail->addReplyTo($setting['from']['value'], $setting['nickname']['value']);
        $mail->addAddress($to);

        // 设置标题与正文
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // 添加附件
        foreach ($attachments as $file) {
            is_file($file) && $mail->addAttachment($file);
        }

        // 正式发送
        $mail->send();
    }
}
