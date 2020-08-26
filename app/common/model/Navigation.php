<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    导航模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\common\model;

use think\facade\Cache;

class Navigation extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'navigation_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'navigation_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'navigation_id' => 'integer',
        'sort'          => 'integer',
        'status'        => 'integer',
    ];

    /**
     * 添加一个导航
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addNavigationItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['navigation_id']);

        if ($this->save($data)) {
            Cache::tag('Navigation')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个导航
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setNavigationItem($data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 搜索条件
        $map[] = ['navigation_id', '=', $data['navigation_id']];

        $result = self::update($data, $map);
        Cache::tag('Navigation')->clear();

        return $result->toArray();
    }

    /**
     * 批量删除导航
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delNavigationList($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['navigation_id']);
        Cache::tag('Navigation')->clear();

        return true;
    }

    /**
     * 获取一个导航
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getNavigationItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['navigation_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量设置是否新开窗口
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNavigationTarget($data)
    {
        if (!$this->validateData($data, 'target')) {
            return false;
        }

        $map[] = ['navigation_id', 'in', $data['navigation_id']];
        self::update(['target' => $data['target']], $map);
        Cache::tag('Navigation')->clear();

        return true;
    }

    /**
     * 批量设置是否启用
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNavigationStatus($data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['navigation_id', 'in', $data['navigation_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('Navigation')->clear();

        return true;
    }

    /**
     * 获取导航列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getNavigationList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];

        // 后台管理搜索
        if (is_client_admin()) {
            empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
        } else {
            $map[] = ['status', '=', 1];
        }

        return $this->setDefaultOrder(['navigation_id' => 'asc'], ['sort' => 'asc'])
            ->cache(true, null, 'Navigation')
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 设置导航排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNavigationSort($data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['navigation_id', '=', $data['navigation_id']];
        self::update(['sort' => $data['sort']], $map);
        Cache::tag('Navigation')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param  $data
     * @return bool
     * @throws \Exception
     */
    public function setNavigationIndex($data)
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        $list = [];
        foreach ($data['navigation_id'] as $key => $value) {
            $list[] = ['navigation_id' => $value, 'sort' => $key + 1];
        }

        $this->saveAll($list);
        Cache::tag('Navigation')->clear();

        return true;
    }
}
