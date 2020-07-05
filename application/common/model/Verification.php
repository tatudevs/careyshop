<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    验证码模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/20
 */

namespace app\common\model;

class Verification extends CareyShop
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'verification_id' => 'integer',
        'status'          => 'integer',
    ];

    /**
     * 发送验证码
     * @access public
     * @param  string $code   通知编码 sms或email
     * @param  string $number 手机号或邮箱地址
     * @return bool
     * @throws
     */
    private function sendNotice($code, $number)
    {
        $result = self::get(function ($query) use ($number) {
            $query->where(['number' => ['eq', $number]])->order(['verification_id' => 'desc']);
        });

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

        if (false === $this->isUpdate(false)->save($data)) {
            return false;
        }

        return true;
    }

    /**
     * 使用验证码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function useVerificationItem($data)
    {
        if (!$this->validateData($data, 'Verification.use')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $map['number'] = ['eq', $data['number']];
            $map['status'] = ['eq', 1];

            $query->order(['verification_id' => 'desc'])->where($map);
        });

        if (!$result) {
            return is_null($result) ? $this->setError('验证码已无效') : false;
        }

        // 开启事务
        self::startTrans();

        try {
            // 完成主业务数据
            if (false === $result->save(['status' => 0])) {
                throw new \Exception($this->getError());
            }

            // 变更账户验证状态
            if (!empty($data['is_check'])) {
                $userDb = new User();
                $type = $result->getAttr('type');

                $userData = [$type == 'sms' ? 'is_mobile' : 'is_email' => 1];
                $userMap = [
                    $type == 'sms' ? 'mobile' : 'email' => ['eq', $data['number']],
                    'is_delete'                         => ['eq', 0],
                ];

                if (false === $userDb->update($userData, $userMap)) {
                    throw new \Exception($userDb->getError());
                }
            }

            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 发送短信验证码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function sendVerificationSms($data)
    {
        if (!$this->validateData($data, 'Verification.sms')) {
            return false;
        }

        return $this->sendNotice('sms', $data['mobile']);
    }

    /**
     * 发送邮件验证码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function sendVerificationEmail($data)
    {
        if (!$this->validateData($data, 'Verification.email')) {
            return false;
        }

        return $this->sendNotice('email', $data['email']);
    }

    /**
     * 验证验证码
     * @access public
     * @param  string $number 手机号或邮箱地址
     * @param  string $code   通知编码 sms或email
     * @return bool
     * @throws
     */
    public function verVerification($number, $code)
    {
        $map['number'] = ['eq', $number];
        $map['code'] = ['eq', $code];

        $result = self::get(function ($query) use ($map) {
            $query->where($map)->order(['verification_id' => 'desc']);
        });

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
     * @param  array $data 外部数据
     * @return bool
     */
    public function verVerificationSms($data)
    {
        if (!$this->validateData($data, 'Verification.ver_sms')) {
            return false;
        }

        return $this->verVerification($data['mobile'], $data['code']);
    }

    /**
     * 验证邮件验证码
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function verVerificationEmail($data)
    {
        if (!$this->validateData($data, 'Verification.ver_email')) {
            return false;
        }

        return $this->verVerification($data['email'], $data['code']);
    }
}
