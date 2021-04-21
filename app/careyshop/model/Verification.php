<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    验证码模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/2
 */

namespace app\careyshop\model;

use think\facade\Config;

class Verification extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'verification_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var false|string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'verification_id',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'verification_id' => 'integer',
        'status'          => 'integer',
    ];

    /**
     * 发送短信验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function sendVerificationSms(array $data): bool
    {
        if (!$this->validateData($data, 'sms')) {
            return false;
        }

        return $this->sendNotice($data['mobile'], 'sms');
    }

    /**
     * 发送邮件验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function sendVerificationEmail(array $data): bool
    {
        if (!$this->validateData($data, 'email')) {
            return false;
        }

        return $this->sendNotice($data['email'], 'email');
    }

    /**
     * 发送验证码
     * @access private
     * @param string $number 号码
     * @param string $type   短信或邮件
     * @return bool
     * @throws
     */
    private function sendNotice(string $number, string $type): bool
    {
        // 获取配置
        $code = rand_number(6);
        $setting = json_decode(Config::get('careyshop.notice.' . $type), true);

        if (1 != $setting['status']['value']) {
            return $this->setError('通知系统' . ('sms' === $type ? '短信' : '邮件') . '已禁用');
        }

        // 发送频率检测
        $result = $this
            ->where('number', '=', $number)
            ->order(['verification_id' => 'desc'])
            ->find();

        if ($result) {
            $nowTime = time();
            $createTime = $result->getData('create_time');

            if (($nowTime - $createTime) < 60) {
                return $this->setError(sprintf('操作过于频繁，请%d秒后重试', 60 - ($nowTime - $createTime)));
            }
        }

        // 实际发送
        if ('sms' === $type) {
            $body = json_encode(['number' => $code], JSON_UNESCAPED_UNICODE);
            \util\Notice::sendSms($number, $body, $setting['code']['value']);
        } else {
            $body = str_replace('${number}', $code, $setting['template']['value']);
            \util\Notice::sendEmail($number, $setting['subject']['value'], $body);
        }

        // 写入数据
        self::create(['number' => $number, 'code' => $code]);
        return true;
    }

    /**
     * 使用验证码
     * @access public
     * @param string $number 手机号码或邮箱地址
     * @param string $code   验证码
     * @return bool
     * @throws
     */
    public static function useVerificationItem(string $number, string $code): bool
    {
        if (empty($number)) {
            throw new \Exception('手机号码或邮箱地址不能为空');
        }

        if (empty($code)) {
            throw new \Exception('验证码不能为空');
        }

        $map[] = ['number', '=', $number];
        $map[] = ['code', '=', $code];

        $result = self::where($map)->order(['verification_id' => 'desc'])->find();
        if (is_null($result)) {
            throw new \Exception('验证码错误');
        }

        if ($result->getAttr('status') !== 1) {
            throw new \Exception('验证码已失效');
        }

        if (time() - $result->getData('create_time') > 60 * 5) {
            throw new \Exception('验证码已失效');
        }

        // 使用验证码
        return $result->save(['status' => 0]);
    }
}
