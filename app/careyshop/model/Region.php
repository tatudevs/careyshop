<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    区域验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

use think\facade\Cache;
use think\facade\Config;

class Region extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'region_id';

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'region_id',
        'parent_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'region_id' => 'integer',
        'parent_id' => 'integer',
        'sort'      => 'integer',
        'is_delete' => 'integer',
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
     * 获取区域缓存列表
     * @access public
     * @return array
     */
    public static function getRegionCacheList(): array
    {
        return self::withoutGlobalScope()
            ->cache('DeliveryArea')
            ->order(['sort', 'region_id'])
            ->column('region_id,parent_id,region_name,sort,is_delete', 'region_id');
    }

    /**
     * 添加一个区域
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addRegionItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        if ($this->allowField(['parent_id', 'region_name', 'sort'])->save($data)) {
            Cache::delete('DeliveryArea');
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个区域
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setRegionItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 搜索条件
        $map[] = ['region_id', '=', $data['region_id']];

        $result = self::update($data, $map);
        Cache::delete('DeliveryArea');

        return $result->toArray();
    }

    /**
     * 批量删除区域
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delRegionList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 搜索条件
        $map[] = ['region_id', 'in', $data['region_id']];

        self::update(['is_delete' => 1], $map);
        Cache::delete('DeliveryArea');

        return true;
    }

    /**
     * 获取指定区域
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getRegionItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 是否提取已删除区域
        $scope = (bool)$data['region_all'] ?? false;

        return self::withoutGlobalScope($scope ? ['delete'] : [])
            ->findOrEmpty($data['region_id'])
            ->toArray();
    }

    /**
     * 获取指定Id下的子节点
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getRegionList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 是否提取已删除区域
        $scope = (bool)$data['region_all'] ?? false;
        $map[] = ['parent_id', '=', $data['region_id'] ?? 0];

        return self::withoutGlobalScope($scope ? ['delete'] : [])
            ->where($map)
            ->order(['sort', 'region_id'])
            ->select()
            ->toArray();
    }

    /**
     * 获取指定Id下的所有子节点
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getRegionSonList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $data['region_id'] ??= 0;
        $isDelete = (bool)$data['region_all'] ?? false; // 是否提取已删除区域
        $regionList = self::getRegionCacheList();

        static $result = [];
        self::getRegionChildrenList($data['region_id'], $result, $regionList, $isDelete);

        return $result;
    }

    /**
     * 过滤和排序所有区域
     * @access private
     * @param int    $id       上级区域Id
     * @param array &$tree     树结构
     * @param array &$list     原始数据结构
     * @param bool   $isDelete 是否提取已删除区域
     */
    private static function getRegionChildrenList(int $id, array &$tree, array &$list, bool $isDelete)
    {
        static $keyList = null;
        if (is_null($keyList)) {
            $keyList = array_column($list, 'parent_id', 'parent_id');
        }

        foreach ($list as $value) {
            if ($value['parent_id'] != $id) {
                continue;
            }

            if (!$isDelete && $value['is_delete'] == 1) {
                continue;
            }

            if (!$isDelete) {
                unset($value['is_delete']);
            }

            $tree[] = $value;
            if ($value['region_id'] != 0 && isset($keyList[$value['region_id']])) {
                self::getRegionChildrenList($value['region_id'], $tree, $list, $isDelete);
            }
        }
    }

    /**
     * 设置区域排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setRegionSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        // 搜索条件
        $map[] = ['region_id', '=', $data['region_id']];

        self::update(['sort' => $data['sort']], $map);
        Cache::delete('DeliveryArea');

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setRegionIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['region_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['region_id' => $value]);
        }

        Cache::delete('DeliveryArea');
        return true;
    }

    /**
     * 根据区域编号获取区域名称
     * @access public
     * @param array $data 外部数据
     * @return string
     */
    public function getRegionName(array $data): string
    {
        if (!$this->validateData($data, 'name')) {
            return '';
        }

        $map[] = ['region_id', 'in', $data['region_id']];
        $result = self::withoutGlobalScope()->where($map)->column('region_name', 'region_id');

        // 根据用户输入的顺序返回
        $name = [];
        foreach ($data['region_id'] as $value) {
            !isset($result[$value]) ?: $name[] = $result[$value];
        }

        return implode(Config::get('careyshop.system_shopping.spacer', ''), $name);
    }
}
