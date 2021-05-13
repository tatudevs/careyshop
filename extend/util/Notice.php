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

use aliyun\SendSmsRequest;
use aliyun\core\{DefaultAcsClient, profile\DefaultProfile};
use PHPMailer\PHPMailer\{PHPMailer, SMTP};
use think\facade\{Config, Log};

class Notice
{
    /**
     * 发送短信
     * @access public
     * @param string $mobile 手机号码
     * @param string $body   发送正文
     * @param string $code   模板编号
     * @param mixed  $sign   短信签名
     * @throws
     */
    public static function sendSms(string $mobile, string $body, string $code, $sign = null)
    {
        // 获取配置信息
        $setting = json_decode(Config::get('careyshop.notice.sms'), true);
        if ($setting['status']['value'] <= 0) {
            return;
        }

        // 加载区域结点配置
        \aliyun\core\Config::load();

        // 设置SMS
        $product = 'Dysmsapi';
        $domain = 'dysmsapi.aliyuncs.com';
        $region = 'cn-hangzhou';
        $endPointName = 'cn-hangzhou';
        $keyId = $setting['key_id']['value'];
        $keySecret = $setting['key_secret']['value'];

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $keyId, $keySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 设置请求参数
        $request = new SendSmsRequest();
        $request->setPhoneNumbers($mobile);
        $request->setSignName($sign ?? $setting['sign']['value']);
        $request->setTemplateCode($code);
        $request->setTemplateParam($body);

        // 正式请求
        $client = new DefaultAcsClient($profile);
        $result = $client->getAcsResponse($request);
        'OK' != $result['Code'] && Log::error($result['Message']);
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
        if ($setting['status']['value'] <= 0) {
            return;
        }

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
            if (is_string($file) && is_file($file)) {
                $mail->addAttachment($file);
                continue;
            }

            if (is_array($file)) {
                ['name' => $name, 'source' => $source] = $file;
                is_file($source) && $mail->addAttachment($source, $name);
            }
        }

        // 正式发送
        if (!$mail->send()) {
            Log::error($mail->ErrorInfo);
        }
    }
}
