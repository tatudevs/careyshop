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
     * 发送验证码
     * @access public
     * @param string $code   通知编码 sms或email
     * @param string $number 手机号或邮箱地址
     * @return bool
     * @throws
     */
    private function sendNotice(string $code, string $number): bool
    {
        $result = $this
            ->where('number', '=', $number)
            ->order(['verification_id' => 'desc'])
            ->find();

        if ($result) {
            // 现在时间与创建日期
            $nowTime = time();
            $createTime = $result->getData('create_time');

            if (($nowTime - $createTime) < 60) {
                return $this->setError(sprintf('操作过于频繁，请%d秒后重试', 60 - ($nowTime - $createTime)));
            }
        }

        $notice = new NoticeTpl();
        $data['number'] = rand_number(6);

        if (!$notice->sendNotice($number, $number, Notice::CAPTCHA, $code, $data)) {
            return $this->setError($notice->getError());
        }

        // 添加新的验证码
        $data = [
            'number' => $number,
            'code'   => $data['number'],
            'type'   => $code,
        ];

        self::create($data);
        return true;
    }

    /**
     * 使用验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function useVerificationItem(array $data): bool
    {
        if (!$this->validateData($data, 'use')) {
            return false;
        }

        $map[] = ['number', '=', $data['number']];
        $map[] = ['status', '=', 1];

        $result = $this->where($map)->order(['verification_id' => 'desc'])->find();
        if (is_null($result)) {
            return $this->setError('验证码已无效');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 完成主业务数据
            $result->save(['status' => 0]);

            // 变更账户验证状态
            if (!empty($data['is_check'])) {
                $userDb = new User();
                $type = $result->getAttr('type');

                $userData = [$type == 'sms' ? 'is_mobile' : 'is_email' => 1];
                $userMap = [
                    [$type == 'sms' ? 'mobile' : 'email', '=', $data['number']],
                    ['is_delete', '=', 0],
                ];

                $userDb->update($userData, $userMap);
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

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

        return $this->sendNotice('sms', $data['mobile']);
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

        return $this->sendNotice('email', $data['email']);
    }

    /**
     * 验证验证码
     * @access public
     * @param string $number 手机号或邮箱地址
     * @param string $code   通知编码 sms或email
     * @return bool
     * @throws
     */
    public function verVerification(string $number, string $code): bool
    {
        $map[] = ['number', '=', $number];
        $map[] = ['code', '=', $code];

        $result = $this->where($map)->order(['verification_id' => 'desc'])->find();
        if (is_null($result)) {
            return $this->setError('验证码错误');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('验证码已失效');
        }

        if (time() - $result->getData('create_time') > 60 * 5) {
            return $this->setError('验证码已失效');
        }

        return true;
    }

    /**
     * 验证短信验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function verVerificationSms(array $data): bool
    {
        if (!$this->validateData($data, 'ver_sms')) {
            return false;
        }

        return $this->verVerification($data['mobile'], $data['code']);
    }

    /**
     * 验证邮件验证码
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function verVerificationEmail(array $data): bool
    {
        if (!$this->validateData($data, 'ver_email')) {
            return false;
        }

        return $this->verVerification($data['email'], $data['code']);
    }
}
