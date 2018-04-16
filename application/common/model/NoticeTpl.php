<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统模板模型
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2017/7/18
 */

namespace app\common\model;

use app\common\service\Notice;
use PHPMailer\PHPMailer\PHPMailer;
use think\Cache;
use think\Config;
use aliyun\SendSmsRequest;
use aliyun\core\Config as AliyunConfig;
use aliyun\core\profile\DefaultProfile;
use aliyun\core\DefaultAcsClient;
use aliyun\core\exception\ClientException;
use aliyun\core\exception\ServerException;

class NoticeTpl extends CareyShop
{
    /**
     * 主配置参数
     * @var array
     */
    private $setting;

    /**
     * 当前通知类型短信模板参数
     * @var array
     */
    private $smsSetting;

    /**
     * 当前通知类型邮箱模板参数
     * @var array
     */
    private $emailSetting;

    /**
     * 可用变量
     * @var array
     */
    private $noticeItem;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'notice_tpl_id',
        'name',
        'code',
        'type',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'notice_tpl_id' => 'integer',
        'type'          => 'integer',
        'status'        => 'integer',
    ];

    /**
     * hasMany cs_notice_item
     * @access public
     * @return $this
     */
    public function getNoticeItem()
    {
        return $this->hasMany('NoticeItem', 'type', 'type');
    }

    /**
     * 获取通知系统模板列表(不包括关联数据,不对外,模型内部调用)
     * @access public
     * @param  int    $type 通知类型
     * @param  string $code 通知编码
     * @return array/false
     */
    public function getNoticeTplData($type, $code = null)
    {
        $map['type'] = ['eq', $type];
        !isset($code) ?: $map['code'] = ['eq', $code];
        $result = $this->cache(true, null, 'NoticeTpl')->where($map)->select();

        if (false !== $result) {
            return $result->toArray();
        }

        return false;
    }

    /**
     * 获取一个通知系统模板
     * @access public
     * @param  array $data 外部数据
     * @return array/false
     */
    public function getNoticeTplItem($data)
    {
        if (!$this->validateData($data, 'NoticeTpl.item')) {
            return false;
        }

        // 搜索条件
        $map['notice_tpl_id'] = ['eq', $data['notice_tpl_id']];
        $map['code'] = ['eq', $data['code']];

        // 获取数据
        $result = self::get(function ($query) use ($map) {
            $with['getNoticeItem'] = function ($query) {
                $query->cache(true, null, 'NoticeTpl');
            };

            $query->cache(true, null, 'NoticeTpl')->with($with)->where($map);
        });

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取通知系统模板列表
     * @access public
     * @param  array $data 外部数据
     * @return array/false
     */
    public function getNoticeTplList($data)
    {
        if (!$this->validateData($data, 'NoticeTpl.list')) {
            return false;
        }

        // 获取数据
        $result = self::all(function ($query) use ($data) {
            $query->with('getNoticeItem')->where(['code' => ['eq', $data['code']]]);
        });

        if (false !== $result) {
            return $result->toArray();
        }

        return false;
    }

    /**
     * 编辑一个通知系统模板
     * @access public
     * @param  array $data 外部数据
     * @return array/false
     */
    public function setNoticeTplItem($data)
    {
        if (!$this->validateData($data, 'NoticeTpl.item')) {
            return false;
        }

        if (!$this->validateSetData($data, 'NoticeTpl.set_' . $data['code'])) {
            return false;
        }

        $map['notice_tpl_id'] = ['eq', $data['notice_tpl_id']];
        $map['code'] = ['eq', $data['code']];

        if (false !== $this->allowField(true)->save($data, $map)) {
            Cache::clear('NoticeTpl');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 批量设置通知系统模板是否启用
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setNoticeTplStatus($data)
    {
        if (!$this->validateData($data, 'NoticeTpl.status')) {
            return false;
        }

        $map['notice_tpl_id'] = ['in', $data['notice_tpl_id']];
        $map['code'] = ['eq', $data['code']];

        if (false !== $this->save(['status' => $data['status']], $map)) {
            Cache::clear('NoticeTpl');
            return true;
        }

        return false;
    }

    /**
     * 发送通知
     * @access public
     * @param  string $mobile 手机号
     * @param  string $email  邮箱地址
     * @param  int    $type   通知类型
     * @param  string $code   通知编码 sms或email(为空则根据设置判断,否则为指定发送)
     * @param  array  $data   发送数据(如订单号则需要从外部传入,而验证码就不需要)
     * @return bool
     */
    public function sendNotice($mobile = '', $email = '', $type, $code = null, $data = [])
    {
        if (empty($mobile) && empty($email)) {
            return $this->setError('手机号或邮箱地址不能为空');
        }

        // 获取主配置参数
        $isSmsClose = $isEmailClose = false;
        $this->setting = Notice::getNoticeList();

        if (empty($this->setting['sms']['value']['status']['value'])) {
            $isSmsClose = true;
        }

        if (empty($this->setting['email']['value']['status']['value'])) {
            $isEmailClose = true;
        }

        // 判断主配置是否启用
        if ($isSmsClose && $isEmailClose) {
            return $this->setError('通知系统已全部禁用');
        }

        if (!is_null($code)) {
            if ($isSmsClose && 'sms' == $code) {
                return $this->setError('通知系统短信已禁用');
            }

            if ($isEmailClose && 'email' == $code) {
                return $this->setError('通知系统邮箱已禁用');
            }
        }

        // 获取模板配置参数
        $tplResult = $this->getNoticeTplData($type, $code);
        if (false === $tplResult) {
            return false;
        }

        foreach ($tplResult as $value) {
            if ('sms' == $value['code'] && !$isSmsClose) {
                $this->smsSetting = $value;
                continue;
            }

            if ('email' == $value['code'] && !$isEmailClose) {
                $this->emailSetting = $value;
                continue;
            }
        }

        // 指定编码时判断模板是否存在
        if (!is_null($code)) {
            if ('sms' == $code && !isset($this->smsSetting)) {
                return $this->setError('通知系统短信模板配置不存在');
            }

            if ('email' == $code && !isset($this->emailSetting)) {
                return $this->setError('通知系统邮箱模板配置不存在');
            }
        }

        // 指定通知编码时判断模板是否启用
        if (!is_null($code) && isset($this->smsSetting) && 0 == $this->smsSetting['status']) {
            return $this->setError('通知系统短信(' . $this->smsSetting['name'] . ')模板已禁用');
        }

        if (!is_null($code) && isset($this->emailSetting) && 0 == $this->emailSetting['status']) {
            return $this->setError('通知系统邮箱(' . $this->emailSetting['name'] . ')模板已禁用');
        }

        // 根据通知类型获取可用变量(缓存)
        $this->noticeItem = NoticeItem::cache()
            ->where(['type' => ['eq', $type]])
            ->column('item_name,replace_name', 'item_name');

        $error = '';
        if (!empty($mobile) && !$isSmsClose && isset($this->smsSetting) && 1 == $this->smsSetting['status']) {
            if (!$this->snedNoticeSms($mobile, $data)) {
                $error .= $this->getError();
            }
        }

        if (!empty($email) && !$isEmailClose && isset($this->emailSetting) && 1 == $this->emailSetting['status']) {
            if (!$this->snedNoticeEmail($email, $this->emailSetting['title'], $data)) {
                $error .= $this->getError();
            }
        }

        return empty($error) ? true : $this->setError($error);
    }

    /**
     * 模板转实际发送数据
     * @access private
     * @param  string $code  通知编码
     * @param  array  &$data 内部提交数据
     * @return mixed
     */
    private function templateToSendContent($code, &$data)
    {
        $realValue = function ($item_name) use ($data) {
            $value = '';
            switch ($item_name) {
                case '{验证码}':
                    //$value = rand_number(6);
                    !isset($data['number']) ?: $value = $data['number'];
                    break;

                case '{商城名称}':
                    $value = Config::get('shop_name.value', 'system_info');
                    break;

                case '{账号}':
                    !isset($data['user_name']) ?: $value = auto_hid_substr($data['user_name']);
                    break;

                case '{充值金额}':
                    !isset($data['recharge_money']) ?: $value = $data['recharge_money'];
                    break;

                case '{主订单号}':
                    !isset($data['order_no']) ?: $value = $data['order_no'];
                    break;

                case '{订单金额}':
                    !isset($data['order_money']) ?: $value = $data['order_money'];
                    break;

                case '{商品金额}':
                    !isset($data['goods_money']) ?: $value = $data['goods_money'];
                    break;

                case '{商品名称}':
                    !isset($data['goods_name']) ?: $value = $data['goods_name'];
                    break;

                case '{商品规格}':
                    !isset($data['goods_spec']) ?: $value = $data['goods_spec'];
                    break;

                case '{物流公司}':
                    !isset($data['delivery_name']) ?: $value = $data['delivery_name'];
                    break;

                case '{快递单号}':
                    !isset($data['logistic_code']) ?: $value = $data['logistic_code'];
                    break;

                default:
                    $value = '';
            }

            return $value;
        };

        // 填充可用变量实际内容
        $noticeItem = [];
        foreach ($this->noticeItem as $key => $value) {
            $noticeItem[$value] = $realValue($key);
        }

        // 从模板中获取需要替换的变量
        $replaceItem = [];
        $template = 'sms' == $code ? $this->smsSetting['template'] : $this->emailSetting['template'];

        if (false === preg_match_all('/\{([^\}]+)\}/', $template, $replaceItem)) {
            return false;
        }

        $result = '';
        if (!empty($replaceItem[0])) {
            if ('sms' == $code) {
                $smsData = [];
                foreach ($replaceItem[0] as $value) {
                    if (isset($this->noticeItem[$value])) {
                        $smsData[$this->noticeItem[$value]] = $noticeItem[$this->noticeItem[$value]];
                    }
                }

                $result = json_encode($smsData);
            }

            if ('email' == $code) {
                $emailData = $template;
                foreach ($replaceItem[0] as $value) {
                    if (isset($this->noticeItem[$value])) {
                        $emailData = str_replace($value, $noticeItem[$this->noticeItem[$value]], $emailData);
                    }
                }

                $result = $emailData;
            }
        }

        return $result;
    }

    /**
     * 发送手机短信
     * @access private
     * @param  string $mobile 手机号
     * @param  array  &$data  发送数据
     * @return bool
     */
    private function snedNoticeSms($mobile, &$data)
    {
        // 加载区域结点配置
        AliyunConfig::load();

        // 短信API产品名
        $product = 'Dysmsapi';

        // 短信API产品域名
        $domain = 'dysmsapi.aliyuncs.com';

        // 暂时不支持多Region
        $region = 'cn-hangzhou';

        // 服务结点
        $endPointName = 'cn-hangzhou';

        // AccessKeyId
        $keyId = $this->setting['sms']['value']['key_id']['value'];

        // AccessKeySecret
        $keySecret = $this->setting['sms']['value']['key_secret']['value'];

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $keyId, $keySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 设置签名名称
        $request->setSignName($this->setting['sms']['value']['sms_sign']['value']);

        // 设置模板CODE
        $request->setTemplateCode($this->smsSetting['sms_code']);

        // 设置模板参数
        if (!empty($templateData = $this->templateToSendContent('sms', $data))) {
            $request->setTemplateParam($templateData);
        }

        // 发起访问请求
        try {
            $client = new DefaultAcsClient($profile);
            $client->getAcsResponse($request);
        } catch (ServerException $e) {
            return $this->setError($e->getErrorMessage());
        } catch (ClientException $e) {
            return $this->setError($e->getErrorMessage());
        }

        return true;
    }

    /**
     * 发送邮件
     * @access private
     * @param  string $email      邮箱号码
     * @param  string $subject    邮件主题
     * @param  array  &$data      发送数据
     * @param  string $attachment 附件列表
     * @return bool
     */
    private function snedNoticeEmail($email, $subject, &$data, $attachment = null)
    {
        // 实例化PHPMailer对象
        $mail = new PHPMailer();

        // 设置邮件编码
        $mail->CharSet = 'UTF-8';

        // 设置使用SMTP服务
        $mail->IsSMTP();

        // SMTP调试功能 0=关闭 1=错误和消息 2=消息
        $mail->SMTPDebug = 0;

        // 启用 SMTP 验证功能
        $mail->SMTPAuth = true;

        // 使用安全协议
        $mail->SMTPSecure = 'ssl';

        // SMTP服务器
        $mail->Host = $this->setting['email']['value']['email_host']['value'];

        // SMTP服务器的端口号
        $mail->Port = $this->setting['email']['value']['email_port']['value'];

        // SMTP服务器用户名
        $mail->Username = $this->setting['email']['value']['email_id']['value'];

        // SMTP服务器密码
        $mail->Password = $this->setting['email']['value']['email_pass']['value'];

        $name = Config::get('shop_name.value', 'system_info');
        $mail->SetFrom($this->setting['email']['value']['email_addr']['value'], $name);
        $mail->AddReplyTo($this->setting['email']['value']['email_addr']['value'], $name);

        // 设置邮件主题
        $mail->Subject = $subject;

        // 设置邮件内容
        if (!empty($templateData = $this->templateToSendContent('email', $data))) {
            $mail->MsgHTML($templateData);
        }

        // 设置收件人
        $mail->AddAddress($email);

        // 添加附件
        if (is_array($attachment)) {
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }

        try {
            if ($mail->Send()) {
                return true;
            }
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }

        return $this->setError($mail->ErrorInfo);
    }
}