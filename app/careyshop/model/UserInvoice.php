<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号发票信息模型
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\careyshop\model;

class UserInvoice extends CareyShop
{
    /**
     * 最大添加数量
     * @var int
     */
    const INVOICE_COUNT_MAX = 5;

    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'user_invoice_id';

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'user_id',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'user_invoice_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'user_invoice_id' => 'integer',
        'user_id'         => 'integer',
        'type'            => 'integer',
        'content'         => 'integer',
        'is_delete'       => 'integer',
    ];

    /**
     * 定义全局的查询范围
     * @var string[]
     */
    protected $globalScope = [
        'delete',
    ];

    /**
     * 全局是否删除查询条件
     * @access public
     * @param object $query 模型
     */
    public function scopeDelete(object $query)
    {
        $query->where('is_delete', '=', 0);
    }

    /**
     * 获取一个发票信息
     * @access public
     * @param array $data
     * @return array|false
     */
    public function getUserInvoiceItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['user_invoice_id', '=', $data['user_invoice_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        return $this->where($map)->findOrEmpty()->toArray();
    }

    /**
     * 获取发票信息列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getUserInvoiceList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        return $this->where($map)->select()->toArray();
    }

    /**
     * 添加一个发票信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addUserInvoiceItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 处理部分数据
        unset($data['user_invoice_id']);
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 批量删除发票信息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delUserInvoiceList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['user_invoice_id', 'in', $data['user_invoice_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 检测是否超出最大添加数量
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function checkUserInvoiceMaximum(array $data): bool
    {
        if (!$this->validateData($data, 'maximum')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $result = $this->where($map)->count();

        if (!is_numeric($result) || $result >= self::INVOICE_COUNT_MAX) {
            return $this->setError('最多只能添加' . self::INVOICE_COUNT_MAX . '个发票信息');
        }

        return true;
    }
}
