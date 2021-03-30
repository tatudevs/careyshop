<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    客服模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\careyshop\model;

class Support extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'support_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'support_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'support_id' => 'integer',
        'sort'       => 'integer',
        'status'     => 'integer',
    ];

    /**
     * 添加一名客服
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addSupportItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['support_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一名客服
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setSupportItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['support_id', '=', $data['support_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除客服
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delSupportList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['support_id']);
        return true;
    }

    /**
     * 获取一名客服
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getSupportItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['support_id', '=', $data['support_id']];
        is_client_admin() ?: $map[] = ['status', '=', 1];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取客服列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSupportList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        if (is_client_admin()) {
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
            empty($data['type_name']) ?: $map[] = ['type_name', 'like', '%' . $data['type_name'] . '%'];
            empty($data['nick_name']) ?: $map[] = ['nick_name', 'like', '%' . $data['nick_name'] . '%'];
        } else {
            $map[] = ['status', '=', 1];
        }

        // 实际查询
        return $this->setDefaultOrder(['support_id' => 'asc'], ['sort' => 'asc'], true)
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 批量设置客服状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setSupportStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['support_id', 'in', $data['support_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 设置客服排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setSupportSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['support_id', '=', $data['support_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setSupportIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['support_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['support_id' => $value]);
        }

        return true;
    }
}
