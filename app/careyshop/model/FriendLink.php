<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    友情链接模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/12
 */

namespace app\careyshop\model;

class FriendLink extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'friend_link_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'friend_link_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'friend_link_id' => 'integer',
        'sort'           => 'integer',
        'status'         => 'integer',
    ];

    /**
     * 添加一个友情链接
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addFriendLinkItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['friend_link_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个友情链接
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setFriendLinkItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['friend_link_id', '=', $data['friend_link_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除友情链接
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delFriendLinkList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['friend_link_id']);
        return true;
    }

    /**
     * 获取一个友情链接
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getFriendLinkItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['friend_link_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取友情链接列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getFriendLinkList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        if (is_client_admin()) {
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
            empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        } else {
            $map[] = ['status', '=', 1];
        }

        return $this->setDefaultOrder(['friend_link_id' => 'desc'], ['sort' => 'asc'], true)
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 批量设置友情链接状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setFriendLinkStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['friend_link_id', 'in', $data['friend_link_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 设置友情链接排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setFriendLinkSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['friend_link_id', '=', $data['friend_link_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setFriendLinkIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['friend_link_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['friend_link_id' => $value]);
        }

        return true;
    }
}
