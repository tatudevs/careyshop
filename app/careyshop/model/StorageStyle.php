<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源样式模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/2
 */

namespace app\careyshop\model;

use think\facade\Cache;

class StorageStyle extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'storage_style_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'storage_style_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'storage_style_id' => 'integer',
        'platform'         => 'integer',
        'scale'            => 'array',
        'quality'          => 'integer',
        'status'           => 'integer',
    ];

    /**
     * 验证资源样式编码是否唯一
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueStorageStyleCode(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        !isset($data['exclude_id']) ?: $map[] = ['storage_style_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('资源样式编码已存在');
        }

        return true;
    }

    /**
     * 添加一个资源样式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addStorageStyleItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['storage_style_id']);

        if ($this->save($data)) {
            Cache::tag('StorageStyle')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个资源样式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setStorageStyleItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 验证编码是否重复
        if (!empty($data['code'])) {
            $map[] = ['storage_style_id', '<>', $data['storage_style_id']];
            $map[] = ['code', '=', $data['code']];

            if (self::checkUnique($map)) {
                return $this->setError('资源样式编码已存在');
            }
        }

        $map = [['storage_style_id', '=', $data['storage_style_id']]];
        $result = self::update($data, $map);
        Cache::tag('StorageStyle')->clear();

        return $result->toArray();
    }

    /**
     * 获取一个资源样式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageStyleItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['storage_style_id'])->toArray();
    }

    /**
     * 根据编码获取资源样式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getStorageStyleCode(array $data)
    {
        if (!$this->validateData($data, 'code')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        $map[] = ['status', '=', 1];
        !isset($data['platform']) ?: $map[] = ['platform', '=', $data['platform']];

        return $this->cache(true, null, 'StorageStyle')
            ->field('scale,resize,quality,suffix,style')
            ->where($map)
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * 获取资源样式列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageStyleList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        is_empty_parm($data['platform']) ?: $map[] = ['platform', '=', $data['platform']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['storage_style_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除资源样式
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delStorageStyleList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['storage_style_id']);
        Cache::tag('StorageStyle')->clear();

        return true;
    }

    /**
     * 批量设置资源样式状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setStorageStyleStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['storage_style_id', 'in', $data['storage_style_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('StorageStyle')->clear();

        return true;
    }
}
