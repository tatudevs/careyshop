<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品模型模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\common\model;

class GoodsType extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'goods_type_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'goods_type_id',
    ];

    /**
     * 添加一个商品模型
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addTypeItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['goods_type_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品模型
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setTypeItem($data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['type_name'])) {
            $map = [
                ['goods_type_id', '<>', $data['goods_type_id']],
                ['type_name', '=', $data['type_name']],
            ];

            if (self::checkUnique($map)) {
                return $this->setError('商品模型名称已存在');
            }
        }

        $map = [['goods_type_id', '=', $data['goods_type_id']]];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 查询商品模型名称是否已存在
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueTypeName($data)
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['type_name', '=', $data['type_name']];
        !isset($data['exclude_id']) ?: $map[] = ['goods_type_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('商品模型名称已存在');
        }

        return true;
    }

    /**
     * 获取一个商品模型
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getTypeItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['goods_type_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取商品模型选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getTypeSelect($data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        return $this->setDefaultOrder(['goods_type_id' => 'desc'])
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 获取商品模型列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getTypeList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['type_name']) ?: $map[] = ['type_name', 'like', '%' . $data['type_name'] . '%'];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['goods_type_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除商品模型
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delTypeList($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 检测商品模型是否存在关联,存在则不允许删除
        $attribute = GoodsAttribute::field('goods_type_id, count(*) num')
            ->group('goods_type_id')
            ->where('is_delete', '=', 0)
            ->buildSql();

        $spec = Spec::field('goods_type_id, count(*) num')
            ->group('goods_type_id')
            ->where('goods_type_id', '<>', 0)
            ->buildSql();

        $result = $this->alias('t')
            ->field('t.*, ifnull(a.num, 0) attribute_total, ifnull(s.num, 0) spec_total')
            ->join([$attribute => 'a'], 'a.goods_type_id = t.goods_type_id', 'left')
            ->join([$spec => 's'], 's.goods_type_id = t.goods_type_id', 'left')
            ->whereIn('t.goods_type_id', $data['goods_type_id'])
            ->select();

        if ($result->isEmpty()) {
            return true;
        }

        foreach ($result as $value) {
            $typeId = $value->getAttr('goods_type_id');
            $typeName = $value->getAttr('type_name');

            if ($value->getAttr('attribute_total') > 0) {
                return $this->setError('Id:' . $typeId . ' 模型名称"' . $typeName . '"存在商品属性');
            }

            if ($value->getAttr('spec_total') > 0) {
                return $this->setError('Id:' . $typeId . ' 模型名称"' . $typeName . '"存在商品规格');
            }
        }

        self::destroy($data['goods_type_id']);
        return true;
    }
}
