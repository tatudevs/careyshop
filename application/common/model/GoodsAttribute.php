<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/7
 */

namespace app\common\model;

class GoodsAttribute extends CareyShop
{
    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'goods_attribute_id',
        //'parent_id',
        //'goods_type_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'goods_attribute_id' => 'integer',
        'parent_id'          => 'integer',
        'goods_type_id'      => 'integer',
        'attr_index'         => 'integer',
        'attr_input_type'    => 'integer',
        'sort'               => 'integer',
        'is_important'       => 'integer',
        'attr_values'        => 'array',
    ];

    /**
     * 查询范围
     * @access protected
     * @param  object $query 模型
     * @return void
     */
    protected function scopeDelete($query)
    {
        $query->where(['is_delete' => ['eq', 0]]);
    }

    /**
     * hasMany cs_goods_attribute
     * @access public
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->hasMany('GoodsAttribute', 'parent_id');
    }

    /**
     * 添加一个商品属性主体
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAttributeBodyItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.body')) {
            return false;
        }

        $field = ['attr_name', 'description', 'icon', 'goods_type_id', 'sort'];
        $hidden = ['attr_index', 'attr_input_type', 'attr_values', 'is_important'];

        if (false !== $this->allowField($field)->save($data)) {
            return $this->hidden($hidden)->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品属性主体
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAttributeBodyItem($data)
    {
        if (!$this->validateSetData($data, 'GoodsAttribute.bodyset')) {
            return false;
        }

        $map['goods_attribute_id'] = ['eq', $data['goods_attribute_id']];
        $map['parent_id'] = ['eq', 0];
        $map['is_delete'] = ['eq', 0];

        $field = ['goods_type_id', 'attr_name', 'description', 'icon', 'sort'];
        if (false !== $this->allowField($field)->save($data, $map)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个商品属性主体
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeBodyItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.item')) {
            return false;
        }

        $map['goods_attribute_id'] = ['eq', $data['goods_attribute_id']];
        $map['parent_id'] = ['eq', 0];

        $field = 'goods_attribute_id,attr_name,description,icon,goods_type_id,sort';
        $result = self::scope('delete')->field($field)->where($map)->find();

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取商品属性主体列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeBodyList($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.list')) {
            return false;
        }

        $map['goods_type_id'] = ['eq', $data['goods_type_id']];
        $map['parent_id'] = ['eq', 0];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = ['eq', 0];

        $order['sort'] = 'asc';
        $order['goods_attribute_id'] = 'asc';

        $result = $this
            ->field('goods_attribute_id,attr_name,description,icon,goods_type_id,sort')
            ->where($map)
            ->order($order)
            ->select();

        if (false !== $result) {
            return $result->toArray();
        }

        return false;
    }

    /**
     * 添加一个商品属性
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAttributeItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute')) {
            return false;
        }

        // 避免无关字段
        unset($data['goods_attribute_id'], $data['is_delete']);

        // 当attr_input_type为手工填写(值=0)时需要清除attr_values
//        if (0 == $data['attr_input_type']) {
//            $data['attr_values'] = [];
//        }

        // 当attr_input_type为手工填写(值=0)时自动设为不检索
        if (0 == $data['attr_input_type']) {
            $data['attr_index'] = 0;
        }

        if (false !== $this->allowField(true)->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品属性
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAttributeItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.set')) {
            return false;
        }

        // 避免无关字段
        unset($data['is_delete']);

        // 当attr_input_type为手工填写(值=0)时需要清除attr_values
//        if (isset($data['attr_input_type']) && 0 == $data['attr_input_type']) {
//            $data['attr_values'] = [];
//        }

        // 当attr_input_type为手工填写(值=0)时自动设为不检索
        if (isset($data['attr_input_type']) && 0 == $data['attr_input_type']) {
            $data['attr_index'] = 0;
        }

        $map['goods_attribute_id'] = ['eq', $data['goods_attribute_id']];
        $map['parent_id'] = ['neq', 0];
        $map['is_delete'] = ['eq', 0];

        if (false !== $this->allowField(true)->save($data, $map)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一个商品属性
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.item')) {
            return false;
        }

        $map['goods_attribute_id'] = ['eq', $data['goods_attribute_id']];
        $map['parent_id'] = ['neq', 0];

        $result = self::scope('delete')->where($map)->find();
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取商品属性列表(可翻页)
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributePage($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.page')) {
            return false;
        }

        // 搜索条件
        $map = [];
        $map['parent_id'] = ['eq', 0];
        empty($data['goods_type_id']) ?: $map['goods_type_id'] = ['eq', $data['goods_type_id']];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = ['eq', 0];

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
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'goods_attribute_id';

            // 排序处理
            $order['sort'] = 'asc';
            $order[$orderField] = $orderType;

            if (!empty($data['order_field'])) {
                $order = array_reverse($order);
            }

            $with = ['getAttribute' => function ($query) use ($order, $map) {
                $withMap = [];
                !isset($map['is_delete']) ?: $withMap['is_delete'] = $map['is_delete'];

                $query->where($withMap)->order($order);
            }];

            $query
                ->field('goods_attribute_id,attr_name,description,icon,goods_type_id,sort')
                ->with($with)
                ->where($map)
                ->order($order)
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 获取商品属性列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeList($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.list')) {
            return false;
        }

        $result = self::all(function ($query) use ($data) {
            $map['goods_type_id'] = ['eq', $data['goods_type_id']];
            $map['parent_id'] = ['eq', 0];
            isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = ['eq', 0];

            $order['sort'] = 'asc';
            $order['goods_attribute_id'] = 'asc';

            $with = ['getAttribute' => function ($query) use ($order, $map) {
                $withMap['is_delete'] = isset($map['is_delete']) ? $map['is_delete'] : [];
                $query->where($withMap)->order($order);
            }];

            $query
                ->field('goods_attribute_id,attr_name,description,icon,goods_type_id,sort')
                ->with($with)
                ->where($map)
                ->order($order);
        });

        if (false !== $result) {
            $attrData = $result->toArray();
            foreach ($attrData as $value) {
                foreach ($value['get_attribute'] as &$item) {
                    $item['result'] = '';
                }
            }

            return [
                'attr_config' => $attrData,
                'attr_key'    => array_column($attrData, 'goods_attribute_id'),
            ];
        }

        return false;
    }

    /**
     * 批量设置商品属性检索
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setAttributeKey($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.key')) {
            return false;
        }

        $map['goods_attribute_id'] = ['in', $data['goods_attribute_id']];
        $map['parent_id'] = ['neq', 0];

        if (false !== $this->save(['attr_index' => $data['attr_index']], $map)) {
            return true;
        }

        return false;
    }

    /**
     * 批量设置商品属性是否核心
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setAttributeImportant($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.important')) {
            return false;
        }

        $map['goods_attribute_id'] = ['in', $data['goods_attribute_id']];
        $map['parent_id'] = ['neq', 0];

        if (false !== $this->save(['is_important' => $data['is_important']], $map)) {
            return true;
        }

        return false;
    }

    /**
     * 设置主体或属性的排序值
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function setAttributeSort($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.sort')) {
            return false;
        }

        $map['goods_attribute_id'] = ['eq', $data['goods_attribute_id']];
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
    public function setAttributeIndex($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.index')) {
            return false;
        }

        $list = [];
        foreach ($data['goods_attribute_id'] as $key => $value) {
            $list[] = ['goods_attribute_id' => $value, 'sort' => $key + 1];
        }

        if (false !== $this->isUpdate()->saveAll($list)) {
            return true;
        }

        return false;
    }

    /**
     * 批量删除商品主体或属性
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAttributeList($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.del')) {
            return false;
        }

        $result = self::all($data['goods_attribute_id']);
        if (false === $result) {
            return false;
        }

        foreach ($result as $value) {
            // 获取当前商品属性Id
            $attributeId = $value->getAttr('goods_attribute_id');

            if ($value->getAttr('parent_id') === 0) {
                $this->update(['is_delete' => 1], ['parent_id' => ['eq', $attributeId]]);
            }

            $value->save(['is_delete' => 1], ['goods_attribute_id' => ['eq', $attributeId]]);
        }

        return true;
    }

    /**
     * 获取基础数据索引列表
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     */
    public function getAttributeData($data)
    {
        if (!$this->validateData($data, 'GoodsAttribute.list')) {
            return false;
        }

        $map['goods_type_id'] = ['eq', $data['goods_type_id']];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = ['eq', 0];

        $result = $this->where($map)->column('goods_attribute_id,attr_name,description,icon');
        if (false !== $result) {
            return $result;
        }

        return false;
    }
}
