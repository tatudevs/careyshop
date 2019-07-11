<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/10
 */

namespace app\common\model;

class Spec extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'spec_id',
        //'goods_type_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'spec_id'       => 'integer',
        'goods_type_id' => 'integer',
        'spec_index'    => 'integer',
        'sort'          => 'integer',
    ];

    /**
     * hasMany cs_spec_item
     * @access public
     * @return mixed
     */
    public function hasSpecItem()
    {
        return $this->hasMany('SpecItem', 'spec_id');
    }

    /**
     * hasOne cs_goods_type
     * @access public
     * @return mixed
     */
    public function getGoodsType()
    {
        return $this
            ->hasOne('GoodsType', 'goods_type_id', 'goods_type_id', [], 'left')
            ->setEagerlyType(0);
    }

    /**
     * 添加一个商品规格
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addSpecItem($data)
    {
        if (!$this->validateData($data, 'Spec')) {
            return false;
        }

        // 避免无关字段
        unset($data['spec_id']);

        // 整理商品规格项数据
        $itemData = [];
        $data['spec_item'] = array_unique($data['spec_item']);

        foreach ($data['spec_item'] as $value) {
            $itemData[] = ['item_name' => $value];
        }

        // 开启事务
        self::startTrans();

        try {
            // 添加主表
            if (false === $this->allowField(true)->save($data)) {
                throw new \Exception($this->getError());
            }

            if (!$this->hasSpecItem()->saveAll($itemData)) {
                throw new \Exception($this->getError());
            }

            self::commit();
            return $this->toArray();
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个商品规格
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setSpecItem($data)
    {
        if (!$this->validateSetData($data, 'Spec.set')) {
            return false;
        }

        // 开启事务
        self::startTrans();

        try {
            if (false === $this->allowField(true)->save($data, ['spec_id' => ['eq', $data['spec_id']]])) {
                throw new \Exception($this->getError());
            }

            if (!empty($data['spec_item'])) {
                if (!SpecItem::updataItem($data['spec_id'], $data['spec_item'])) {
                    throw new \Exception();
                }
            }

            self::commit();
            return $this->toArray();
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 替换商品规格项
     * @access private
     * @param  array $data 待修改数据
     * @return void
     */
    private function replaceSpecItem(&$data)
    {
        if (!isset($data['has_spec_item'])) {
            return;
        }

        foreach ($data['has_spec_item'] as $value) {
            $data['spec_item'][] = $value['item_name'];
        }

        unset($data['has_spec_item']);
    }

    /**
     * 获取一条商品规格
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecItem($data)
    {
        if (!$this->validateData($data, 'Spec.item')) {
            return false;
        }

        $result = self::get($data['spec_id'], 'hasSpecItem');
        if (false !== $result) {
            if (is_null($result)) {
                return null;
            }

            $result = $result->toArray();
            $this->replaceSpecItem($result);

            return $result;
        }

        return false;
    }

    /**
     * 获取商品规格列表(可翻页)
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecPage($data)
    {
        if (!$this->validateData($data, 'Spec.page')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['goods_type_id']) ?: $map['goods_type_id'] = ['eq', $data['goods_type_id']];

        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'spec_id';

            // 排序处理
            $order['sort'] = 'asc';
            $order[$orderField] = $orderType;

            if (!empty($data['order_field'])) {
                $order = array_reverse($order);
            }

            // 搜索条件
            $map = [];
            empty($data['goods_type_id']) ?: $map['getGoodsType.goods_type_id'] = ['eq', $data['goods_type_id']];

            $query
                ->with('hasSpecItem,getGoodsType')
                ->where($map)
                ->order($order)
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            $result = $result->toArray();
            foreach ($result as $key => $value) {
                $this->replaceSpecItem($result[$key]);
            }

            return ['items' => $result, 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 获取商品规格列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecList($data)
    {
        if (!$this->validateData($data, 'Spec.list')) {
            return false;
        }

        $result = self::all(function ($query) use ($data) {
            $map['goods_type_id'] = ['eq', $data['goods_type_id']];

            $order['sort'] = 'asc';
            $order['spec_id'] = 'asc';

            $query->with('hasSpecItem')->where($map)->order($order);
        });

        if (false !== $result) {
            $result = $result->toArray();
            foreach ($result as $key => $value) {
                $this->replaceSpecItem($result[$key]);
            }

            return $result;
        }

        return false;
    }

    /**
     * 批量删除商品规格
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function delSpecList($data)
    {
        if (!$this->validateData($data, 'Spec.del')) {
            return false;
        }

        // 开启事务
        self::startTrans();

        try {
            self::destroy($data['spec_id']);

            foreach ($data['spec_id'] as $value) {
                SpecItem::updataItem($value, []);
            }

            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量设置商品规格检索
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     */
    public function setSpecKey($data)
    {
        if (!$this->validateData($data, 'Spec.key')) {
            return false;
        }

        $map['spec_id'] = ['in', $data['spec_id']];
        if (false !== $this->save(['spec_index' => $data['spec_index']], $map)) {
            return true;
        }

        return false;
    }

    /**
     * 设置商品规格排序
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setSpecSort($data)
    {
        if (!$this->validateData($data, 'Spec.sort')) {
            return false;
        }

        $map['spec_id'] = ['eq', $data['spec_id']];
        if (false !== $this->save(['sort' => $data['sort']], $map)) {
            return true;
        }

        return false;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param  $data
     * @return bool
     * @throws \Exception
     */
    public function setSpecIndex($data)
    {
        if (!$this->validateData($data, 'Spec.index')) {
            return false;
        }

        $list = [];
        foreach ($data['spec_id'] as $key => $value) {
            $list[] = ['spec_id' => $value, 'sort' => $key + 1];
        }

        if (false !== $this->isUpdate()->saveAll($list)) {
            return true;
        }

        return false;
    }
}
