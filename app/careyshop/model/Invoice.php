<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    票据管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\careyshop\model;

use think\facade\Config;
use think\facade\Event;
use think\facade\Request;

class Invoice extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'invoice_id';

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
        'invoice_id',
        'order_no',
        'user_id',
        'user_invoice_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'invoice_id'      => 'integer',
        'user_id'         => 'integer',
        'user_invoice_id' => 'integer',
        'premium'         => 'float',
        'order_amount'    => 'float',
        'invoice_amount'  => 'float',
        'attachment'      => 'array',
        'status'          => 'integer',
    ];

    /**
     * belongsTo cs_user_invoice
     * @access public
     * @return object
     */
    public function getInvoice(): object
    {
        return $this
            ->belongsTo(UserInvoice::class, 'user_invoice_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param null $value
     * @return object
     */
    public function getGetInvoiceAttr($value = null)
    {
        return $value ?? new \stdClass;
    }

    /**
     * 添加一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addInvoiceItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 处理部分数据
        $data['attachment'] = [];
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();
        unset($data['invoice_id'], $data['status'], $data['client_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 重置一个票据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function resetInvoiceItem(array $data)
    {
        if (!$this->validateData($data, 'reset')) {
            return false;
        }

        $data['number'] = '';
        $data['attachment'] = [];
        $data['status'] = 0;

        $map[] = ['invoice_id', '=', $data['invoice_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 编辑一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setInvoiceItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['invoice_id', '=', $data['invoice_id']];
        $db = $this->where($map)->find();

        if (is_null($db)) {
            return $this->setError('数据不存在');
        }

        if (0 !== $db->getAttr('status')) {
            return $this->setError('此发票状态已不允许修改');
        }

        1 === $data['status'] ?: $data['attachment'] = [];
        $field = ['number', 'remark', 'attachment', 'status'];

        if (1 === $data['send_email'] && 1 === $data['status']) {
            if (!$this->sendEmail($db->toArray(), $data['attachment'])) {
                return false;
            }
        }

        if (!$db->allowField($field)->save($data)) {
            return false;
        }

        $result = $db->toArray();
        $isSubscribe = isset($data['status']) && $data['status'] != $db->getAttr('status');

        if ($isSubscribe) {
            switch ($result['status']) {
                case 1:
                    Event::trigger('CompleteInvoice', $result);
                    break;

                case 2:
                    Event::trigger('RefuseInvoice', $result);
                    break;
            }
        }

        return $result;
    }

    /**
     * 通过电子邮箱发送电子发票
     * @access private
     * @param array $data       开票数据
     * @param mixed $attachment 电子发票资源
     * @return bool
     */
    private function sendEmail(array $data, $attachment = null): bool
    {
        if (empty($attachment)) {
            return $this->setError('缺少开票资源');
        }

        $email = UserInvoice::withoutGlobalScope()
            ->where('user_invoice_id', '=', $data['user_invoice_id'])->value('email');

        if (!$email) {
            return $this->setError('收票邮箱不能为空');
        }

        $subject = '收到来自' . Config::get('careyshop.system_info.name') . '的电子发票';
        $body = "您好！来自订单号：{$data['order_no']}开具的{$data['invoice_amount']}元电子发票。";

        $pattern = '/^((http|https)?:\/\/)/i';
        foreach ($attachment as $value) {
            if (!preg_match($pattern, $value['source'])) {
                $value['source'] = (Request::isSsl() ? 'https' : 'http') . '://' . $value['source'];
            }

            ['name' => $name, 'source' => $file] = $value;
            $body .= sprintf('<br/><a href="%s" target="_blank" rel="noopener">%s(链接另存为保存)</a>', $file, $name);
        }

        \util\Notice::sendEmail($email, $subject, $body);
        return true;
    }

    /**
     * 根据订单号获取一条票据
     * @access public
     * @param array $data 外部数据
     * @return array|false|mixed
     */
    public function getInvoiceOrder(array $data)
    {
        if (!$this->validateData($data, 'order')) {
            return false;
        }

        // 搜索条件
        $map[] = ['invoice.order_no', '=', $data['order_no']];
        is_client_admin() ?: $map[] = ['invoice.user_id', '=', get_client_id()];

        $result[] = $this->withJoin('getInvoice')
            ->where($map)
            ->findOrEmpty()
            ->toArray();

        self::keyToSnake(['getInvoice'], $result);
        return $result[0];
    }

    /**
     * 获取票据列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getInvoiceList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['order_no']) ?: $map[] = ['invoice.order_no', '=', $data['order_no']];
        is_client_admin() ?: $map[] = ['invoice.user_id', '=', get_client_id()];
        is_empty_parm($data['status']) ?: $map[] = ['invoice.status', '=', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['invoice.create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        $result['total_result'] = $this->alias('invoice')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setAliasOrder('invoice')
            ->setDefaultOrder(['invoice_id' => 'desc'])
            ->withJoin('getInvoice')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getInvoice'], $result['items']);
        return $result;
    }
}
