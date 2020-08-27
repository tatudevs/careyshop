<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现账号模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/12
 */

namespace app\common\model;

class WithdrawUser extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'withdraw_user_id';

    /**
     * 最大添加数量
     * @var int
     */
    const WITHDRAWUSER_COUNT_MAX = 10;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'user_id',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'withdraw_user_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'withdraw_user_id' => 'integer',
        'user_id'          => 'integer',
        'is_delete'        => 'integer',
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
     * @param WithdrawUser $query 模型
     */
    public function scopeDelete($query)
    {
        $query->where('is_delete', '=', 0);
    }

    /**
     * 添加一个提现账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addWithdrawUserItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段并初始化部分数据
        unset($data['withdraw_user_id'], $data['is_delete']);
        empty($data['client_id']) ?: $data['client_id'] = (int)$data['client_id'];
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个提现账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setWithdrawUserItem($data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 避免无关字段并初始化部分数据
        unset($data['is_delete']);
        empty($data['client_id']) ?: $data['client_id'] = (int)$data['client_id'];

        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $map[] = ['user_id', '=', $userId];
        $map[] = ['withdraw_user_id', '=', $data['withdraw_user_id']];

        $result = self::update($data, $map);
        return $result->toArray();
    }

    /**
     * 批量删除提现账号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delWithdrawUserList($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['withdraw_user_id', 'in', $data['withdraw_user_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 获取指定账号的一个提现账号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getWithdrawUserItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['withdraw_user_id', '=', $data['withdraw_user_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取指定账号的提现账号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getWithdrawUserList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        return $this->where($map)->select()->toArray();
    }

    /**
     * 检测是否超出最大添加数量
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function isWithdrawUserMaximum($data)
    {
        if (!$this->validateData($data, 'maximum')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $result = $this->where($map)->count();

        if ($result >= self::WITHDRAWUSER_COUNT_MAX || !is_numeric($result)) {
            return $this->setError('已到达' . self::WITHDRAWUSER_COUNT_MAX . '个提现账号');
        }

        return true;
    }
}
