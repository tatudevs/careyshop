<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    收货地址管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class UserAddress extends CareyShop
{
    /**
     * 最大添加数量
     * @var int
     */
    const ADDRESS_COUNT_MAX = 20;

    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'user_address_id';

    /**
     * 隐藏属性
     * @var mixed|string[]
     */
    protected $hidden = [
        'user_id',
        'is_delete',
    ];

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'user_address_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'user_address_id' => 'integer',
        'country'         => 'integer',
        'region_list'     => 'array',
        'is_delete'       => 'integer',
    ];

    /**
     * 定义全局的查询范围
     * @var mixed|string[]
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
     * 获取指定账号的收货地址列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAddressList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        return $this->where($map)->select()->toArray();
    }

    /**
     * 获取指定账号的一个收货地址
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getAddressItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['user_address_id', '=', $data['user_address_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        return $this->where($map)->findOrEmpty()->toArray();
    }

    /**
     * 获取指定账号的默认收货地址信息
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getAddressDefault(array $data)
    {
        if (!$this->validateData($data, 'get_default')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $userId = User::where($map)->value('user_address_id', 0);

        if (!$userId) {
            return $this->setError('尚未指定默认收货地址或尚未存在');
        }

        return $this->findOrEmpty($userId)->toArray();
    }

    /**
     * 添加一个收货地址
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAddressItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 处理部分数据
        $region = $data['region_list'];
        unset($data['user_address_id'], $data['is_delete']);
        !isset($data['is_default']) ?: $data['is_default'] = (int)$data['is_default'];
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();

        // 根据区域编号查询所在区域
        if (!empty($data['country'])) {
            array_unshift($region, $data['country']);
        }

        $regionDb = new Region();
        $data['region'] = $regionDb->getRegionName(['region_id' => $region]);

        if ($this->save($data)) {
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $this->setUserAddressDefault($this->getAttr('user_id'), $this->getAttr('user_address_id'));
            }

            return $this->hidden(['client_id'])->toArray();
        }

        return false;
    }

    /**
     * 编辑一个收货地址
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAddressItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 避免无关字段,并且处理部分字段
        unset($data['is_delete']);
        !isset($data['is_default']) ?: $data['is_default'] = (int)$data['is_default'];

        $userId = is_client_admin() ? $data['client_id'] : get_client_id();
        $map[] = ['user_id', '=', $userId];
        $map[] = ['user_address_id', '=', $data['user_address_id']];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 处理区域
        $country = !isset($data['country']) ? $result->getAttr('country') : $data['country'];
        $region = empty($data['region_list']) ? $result->getAttr('region_list') : $data['region_list'];

        if (!empty($country)) {
            array_unshift($region, $country);
        }

        $regionDb = new Region();
        $data['region'] = $regionDb->getRegionName(['region_id' => $region]);

        if ($result->save($data)) {
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $this->setUserAddressDefault($userId, $data['user_address_id']);
            }

            return $result->hidden(['client_id'])->toArray();
        }

        return false;
    }

    /**
     * 批量删除收货地址
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAddressList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['user_address_id', 'in', $data['user_address_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 设置账号默认收货地址
     * @access public
     * @param int $clientId  账号Id
     * @param int $addressId 收货地址Id
     */
    private function setUserAddressDefault(int $clientId, int $addressId)
    {
        User::update(['user_address_id' => $addressId], ['user_id' => $clientId]);
    }

    /**
     * 设置一个收货地址为默认
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setAddressDefault(array $data): bool
    {
        if (!$this->validateData($data, 'default')) {
            return false;
        }

        $result = $this->find($data['user_address_id']);
        if (is_null($result)) {
            return $this->setError('收货地址不存在');
        }

        if (!is_client_admin() && $result->getAttr('user_id') != get_client_id()) {
            return false;
        }

        $this->setUserAddressDefault($result->getAttr('user_id'), $result->getAttr('user_address_id'));
        return true;
    }

    /**
     * 检测是否超出最大添加数量
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function isAddressMaximum(array $data): bool
    {
        if (!$this->validateData($data, 'maximum')) {
            return false;
        }

        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $result = $this->where($map)->count();

        if (!is_numeric($result) || $result >= self::ADDRESS_COUNT_MAX) {
            return $this->setError('已到达' . self::ADDRESS_COUNT_MAX . '个收货地址');
        }

        return true;
    }
}
