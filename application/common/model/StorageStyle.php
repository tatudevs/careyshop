<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源样式模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/5/31
 */

namespace app\common\model;

use think\Cache;

class StorageStyle extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'storage_style_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
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
     * @param  array $data 外部数据
     * @return bool
     */
    public function uniqueStorageStyleCode($data)
    {
        if (!$this->validateData($data, 'StorageStyle.unique')) {
            return false;
        }

        $map['code'] = ['eq', $data['code']];
        !isset($data['exclude_id']) ?: $map['storage_style_id'] = ['neq', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('资源样式编码已存在');
        }

        return true;
    }

    /**
     * 处理缩放规格
     * @access private
     * @param  array $data    外部请求数据
     * @param  array $oldData 原始缩放数据
     * @return void
     */
    private function setScale(&$data, $oldData = null)
    {
        // 构建初始数据结构
        if ($oldData) {
            $structure = $oldData;
        } else {
            $structure = [
                'pc'     => ['size' => [], 'crop' => []],
                'mobile' => ['size' => [], 'crop' => []],
            ];
        }

        // 将提交的数据实际写入结构中
        if (isset($data['scale'])) {
            foreach (['pc', 'mobile'] as $value) {
                if (!empty($data['scale'][$value]['size'])) {
                    $structure[$value]['size'] = $data['scale'][$value]['size'];
                }

                if (!empty($data['scale'][$value]['crop'])) {
                    $structure[$value]['crop'] = $data['scale'][$value]['crop'];
                }
            }
        }

        $data['scale'] = $structure;
    }

    /**
     * 添加一个资源样式
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addStorageStyleItem($data)
    {
        if (!$this->validateData($data, 'StorageStyle')) {
            return false;
        }

        // 处理缩放规格,并且避免无关字段
        $this->setScale($data);
        unset($data['storage_style_id']);

        if (false !== $this->allowField(true)->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个资源样式
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setStorageStyleItem($data)
    {
        if (!$this->validateSetData($data, 'StorageStyle.set')) {
            return false;
        }

        // 验证编码是否重复
        if (!empty($data['code'])) {
            $map['storage_style_id'] = ['neq', $data['storage_style_id']];
            $map['code'] = ['eq', $data['code']];

            if (self::checkUnique($map)) {
                return $this->setError('资源样式编码已存在');
            }
        }

        $result = self::get($data['storage_style_id']);
        if (!$result) {
            return is_null($result) ? $this->setError('数据不存在') : false;
        }

        // 处理新旧缩放规格
        $this->setScale($data, $result->getAttr('scale'));
        unset($data['storage_style_id']);

        if (false !== $result->allowField(true)->save($data)) {
            Cache::clear('StorageStyle');
            return $result->toArray();
        }

        return $this->setError($result->getError());
    }

    /**
     * 获取一个资源样式
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageStyleItem($data)
    {
        if (!$this->validateData($data, 'StorageStyle.item')) {
            return false;
        }

        $result = self::get($data['storage_style_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 根据编码获取资源样式
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageStyleCode($data)
    {
        if (!$this->validateData($data, 'StorageStyle.code')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $map['code'] = ['eq', $data['code']];
            $map['status'] = ['eq', 1];
            !isset($data['platform']) ?: $map['platform'] = ['eq', $data['platform']];

            $query
                ->cache(true, null, 'StorageStyle')
                ->field('scale,quality,suffix,style')
                ->where($map);
        });

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取资源样式列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getStorageStyleList($data)
    {
        if (!$this->validateData($data, 'StorageStyle.list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map['name'] = ['like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map['code'] = ['eq', $data['code']];
        is_empty_parm($data['platform']) ?: $map['platform'] = ['eq', $data['platform']];
        is_empty_parm($data['status']) ?: $map['status'] = ['eq', $data['status']];

        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $map) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'storage_style_id';

            $query
                ->where($map)
                ->order([$orderField => $orderType])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 批量删除资源样式
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function delStorageStyleList($data)
    {
        if (!$this->validateData($data, 'StorageStyle.del')) {
            return false;
        }

        self::destroy($data['storage_style_id']);
        Cache::clear('StorageStyle');

        return true;
    }

    /**
     * 批量设置资源样式状态
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setStorageStyleStatus($data)
    {
        if (!$this->validateData($data, 'StorageStyle.status')) {
            return false;
        }

        $map['storage_style_id'] = ['eq', $data['storage_style_id']];
        if (false !== $this->save(['status' => $data['status']], $map)) {
            Cache::clear('StorageStyle');
            return true;
        }

        return false;
    }
}
