<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\common\model;

class Spec extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'spec_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'spec_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'spec_id'       => 'integer',
        'goods_type_id' => 'integer',
        'spec_index'    => 'integer',
        'spec_type'     => 'integer',
        'sort'          => 'integer',
    ];

    /**
     * hasMany cs_spec_item
     * @access public
     * @return mixed
     */
    public function specItem()
    {
        return $this->hasMany(SpecItem::class, 'spec_id');
    }

    /**
     * hasOne cs_goods_type
     * @access public
     * @return mixed
     */
    public function getGoodsType()
    {
        return $this
            ->hasOne(GoodsType::class, 'goods_type_id', 'goods_type_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param mixed $value
     * @return mixed|\stdClass
     */
    public function getGetGoodsTypeAttr($value)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 添加一个商品规格
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addSpecItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['spec_id']);

        // 整理商品规格项数据(去重)
        $itemData = [];
        $data['spec_item'] = array_unique($data['spec_item']);

        foreach ($data['spec_item'] as $key => $value) {
            $itemData[] = [
                'item_name'  => $value,
                'is_contact' => 1,
                'sort'       => $key,
            ];
        }

        // 开启事务
        $this->startTrans();

        try {
            // 添加规格主表
            $this->save($data);

            // 添加规格项表
            $this->specItem()->saveAll($itemData);

            $this->commit();
            return $this->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个商品规格
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setSpecItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改规格主表
            $map[] = ['spec_id', '=', $data['spec_id']];
            $result = self::update($data, $map);

            if (!empty($data['spec_item'])) {
                if (!SpecItem::updateItem($data['spec_id'], $data['spec_item'])) {
                    throw new \Exception();
                }
            }

            $this->commit();
            return $result->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取一条商品规格
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getSpecItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 搜索与关联
        $map[] = ['spec_id', '=', $data['spec_id']];
        $with['spec_item'] = function ($query) {
            $query->where('is_contact', '=', 1)->order(['sort' => 'asc']);
        };

        $result = $this->with($with)->where($map)->find();
        if (is_null($result)) {
            return null;
        }

        $specData = $result->toArray();
        if (empty($data['is_detail'])) {
            $specData['spec_item'] = array_column($specData['spec_item'], 'item_name');
        }

        return $specData;
    }

    /**
     * 获取商品规格列表(可翻页)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecPage(array $data)
    {
        if (!$this->validateData($data, 'page')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_type_id', '<>', 0];
        empty($data['goods_type_id']) ?: $map[] = ['goods_type_id', '=', $data['goods_type_id']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 重置搜索条件
        $map = [['spec.goods_type_id', '<>', 0]];
        empty($data['goods_type_id']) ?: $map[] = ['getGoodsType.goods_type_id', '=', $data['goods_type_id']];

        // 关联查询
        $with['spec_item'] = function ($query) {
            $query->where('is_contact', '=', 1)->order(['sort' => 'asc']);
        };

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['spec_id' => 'asc'], ['sort' => 'asc'], true)
            ->withJoin('getGoodsType')
            ->with($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        if (empty($data['is_detail'])) {
            foreach ($result['items'] as $key => $value) {
                $result['items'][$key]['spec_item'] = array_column($value['spec_item'], 'item_name');
            }
        }

        self::keyToSnake(['getGoodsType'], $result['items']);
        return $result;
    }

    /**
     * 获取商品规格列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_type_id', '=', $data['goods_type_id']];

        // 关联查询
        $with['spec_item'] = function ($query) {
            $query->where('is_contact', '=', 1)->order(['sort' => 'asc']);
        };

        $result = $this->setDefaultOrder(['spec_id' => 'asc'], ['sort' => 'asc'])
            ->with($with)
            ->where($map)
            ->withSearch(['order'])
            ->select()
            ->toArray();

        foreach ($result as &$value) {
            $value['check_list'] = [];
            foreach ($value['spec_item'] as &$item) {
                $item['image'] = [];
                $item['color'] = '';
            }
        }

        unset($value);
        return [
            'spec_config' => $result,
            'spec_key'    => array_column($result, 'spec_id'),
        ];
    }

    /**
     * 获取所有商品规格及规格项
     * @access public
     * @return array
     * @throws
     */
    public function getSpecAll(): array
    {
        // 搜索条件
        $map[] = ['spec.goods_type_id', '<>', 0];

        // 关联查询
        $with['spec_item'] = function ($query) {
            $query->where('is_contact', '=', 1)->order(['sort' => 'asc']);
        };

        $resultData = [];
        $result = $this->setDefaultOrder(['spec_id' => 'asc'], ['sort' => 'asc'])
            ->withJoin('getGoodsType')
            ->with($with)
            ->where($map)
            ->withSearch(['order'])
            ->select()
            ->toArray();

        foreach ($result as $value) {
            if (!array_key_exists($value['goods_type_id'], $resultData)) {
                $resultData[$value['goods_type_id']] = [
                    'name'          => $value['getGoodsType']['type_name'],
                    'goods_type_id' => $value['goods_type_id'],
                ];
            }

            foreach ($value['spec_item'] as &$item) {
                $item['image'] = [];
                $item['color'] = '';
            }

            unset($item);
            unset($value['getGoodsType']);
            $resultData[$value['goods_type_id']]['item'][] = $value;
        }

        return array_values($resultData);
    }

    /**
     * 批量删除商品规格
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delSpecList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 开启事务
        $this->startTrans();

        try {
            // 修改规格主表
            $map[] = ['spec_id', 'in', $data['spec_id']];
            self::update(['goods_type_id' => 0, 'spec_index' => 0], $map);

            // 断开模型字段
            $map[] = ['is_contact', '<>', 0];
            SpecItem::update(['is_contact' => 0], $map);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量设置商品规格检索
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setSpecKey(array $data): bool
    {
        if (!$this->validateData($data, 'key')) {
            return false;
        }

        $map[] = ['spec_id', 'in', $data['spec_id']];
        self::update(['spec_index' => $data['spec_index']], $map);

        return true;
    }

    /**
     * 设置商品规格排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setSpecSort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['spec_id', '=', $data['spec_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setSpecIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['spec_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['spec_id' => $value]);
        }

        return true;
    }
}
