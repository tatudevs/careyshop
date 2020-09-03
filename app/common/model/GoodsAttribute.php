<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/27
 */

namespace app\common\model;

class GoodsAttribute extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'goods_attribute_id';

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
     * 全局是否删除查询条件
     * @access public
     * @param object $query 模型
     */
    public function scopeDelete($query)
    {
        $query->where('is_delete', '=', 0);
    }

    /**
     * hasMany cs_goods_attribute
     * @access public
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->hasMany(GoodsAttribute::class, 'parent_id');
    }

    /**
     * 添加一个商品属性主体
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAttributeBodyItem(array $data)
    {
        if (!$this->validateData($data, 'body')) {
            return false;
        }

        $field = ['attr_name', 'description', 'icon', 'goods_type_id', 'sort'];
        $hidden = ['attr_index', 'attr_input_type', 'attr_values', 'is_important'];

        if ($this->allowField($field)->save($data)) {
            return $this->hidden($hidden)->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品属性主体
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAttributeBodyItem(array $data)
    {
        if (!$this->validateData($data, 'bodyset', true)) {
            return false;
        }

        $map[] = ['goods_attribute_id', '=', $data['goods_attribute_id']];
        $map[] = ['parent_id', '=', 0];
        $map[] = ['is_delete', '=', 0];

        $field = ['goods_type_id', 'attr_name', 'description', 'icon', 'sort'];
        $result = self::update($data, $map, $field);

        return $result->toArray();
    }

    /**
     * 获取一个商品属性主体
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeBodyItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['goods_attribute_id', '=', $data['goods_attribute_id']];
        $map[] = ['parent_id', '=', 0];

        $field = 'goods_attribute_id,attr_name,description,icon,goods_type_id,sort';
        $result = $this->scope('delete')->field($field)->where($map)->find();

        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取商品属性主体列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeBodyList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $map[] = ['goods_type_id', '=', $data['goods_type_id']];
        $map[] = ['parent_id', '=', 0];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map[] = ['is_delete', '=', 0];

        return $this->setDefaultOrder(['goods_attribute_id' => 'asc'], ['sort' => 'asc'])
            ->field('goods_attribute_id,attr_name,description,icon,goods_type_id,sort')
            ->where($map)
            ->withSearch(['order'])
            ->select()
            ->toArray();
    }

    /**
     * 添加一个商品属性
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addAttributeItem(array $data)
    {
        if (!$this->validateData($data)) {
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

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品属性
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setAttributeItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
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

        $map[] = ['goods_attribute_id', '=', $data['goods_attribute_id']];
        $map[] = ['parent_id', '<>', 0];
        $map[] = ['is_delete', '=', 0];

        $result = self::update($data, $map);
        return $result->toArray();
    }

    /**
     * 获取一个商品属性
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['goods_attribute_id', '=', $data['goods_attribute_id']];
        $map[] = ['parent_id', '<>', 0];

        $result = $this->scope('delete')->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取商品属性列表(可翻页)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributePage(array $data)
    {
        if (!$this->validateData($data, 'page')) {
            return false;
        }

        // 搜索条件
        $map['parent_id'] = 0;
        empty($data['goods_type_id']) ?: $map['goods_type_id'] = $data['goods_type_id'];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = 0;

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 关联查询
        $with = ['get_attribute' => function ($query) use ($data, $map) {
            $withMap = [];
            !isset($map['is_delete']) ?: $withMap['is_delete'] = $map['is_delete'];

            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'asc';
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'goods_attribute_id';

            $order['sort'] = 'asc';
            $order[$orderField] = $orderType;

            if (!empty($data['order_field'])) {
                $order = array_reverse($order);
            }

            $query->where($withMap)->order($order);
        }];

        // 返回字段
        $field = 'goods_attribute_id,attr_name,description,icon,goods_type_id,sort';

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['goods_attribute_id' => 'asc'], ['sort' => 'asc'], true)
            ->field($field)
            ->with($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取商品属性列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttributeList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map['goods_type_id'] = $data['goods_type_id'];
        $map['parent_id'] = 0;
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map['is_delete'] = 0;

        // 排序方式
        $order['sort'] = 'asc';
        $order['goods_attribute_id'] = 'asc';

        // 关联查询
        $with = ['get_attribute' => function ($query) use ($order, $map) {
            $withMap['is_delete'] = isset($map['is_delete']) ? $map['is_delete'] : [];
            $query->where($withMap)->order($order);
        }];

        // 返回字段
        $field = 'goods_attribute_id,attr_name,description,icon,goods_type_id,sort';

        $result = $this->field($field)
            ->with($with)
            ->where($map)
            ->order($order)
            ->select()
            ->toArray();

        foreach ($result as $value) {
            foreach ($value['get_attribute'] as &$item) {
                $item['result'] = '';
            }
        }

        return [
            'attr_config' => $result,
            'attr_key'    => array_column($result, 'goods_attribute_id'),
        ];
    }

    /**
     * 批量设置商品属性检索
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAttributeKey(array $data)
    {
        if (!$this->validateData($data, 'key')) {
            return false;
        }

        $map[] = ['goods_attribute_id', 'in', $data['goods_attribute_id']];
        $map[] = ['parent_id', '<>', 0];

        self::update(['attr_index' => $data['attr_index']], $map);
        return true;
    }

    /**
     * 批量设置商品属性是否核心
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAttributeImportant(array $data)
    {
        if (!$this->validateData($data, 'important')) {
            return false;
        }

        $map[] = ['goods_attribute_id', 'in', $data['goods_attribute_id']];
        $map[] = ['parent_id', '<>', 0];

        self::update(['is_important' => $data['is_important']], $map);
        return true;
    }

    /**
     * 设置主体或属性的排序值
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAttributeSort(array $data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['goods_attribute_id', '=', $data['goods_attribute_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function setAttributeIndex(array $data)
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        $list = [];
        foreach ($data['goods_attribute_id'] as $key => $value) {
            $list[] = ['goods_attribute_id' => $value, 'sort' => $key + 1];
        }

        $this->saveAll($list);
        return true;
    }

    /**
     * 批量删除商品主体或属性
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delAttributeList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $result = $this->select($data['goods_attribute_id']);
        foreach ($result as $value) {
            // 获取当前商品属性Id
            $attributeId = $value->getAttr('goods_attribute_id');

            if ($value->getAttr('parent_id') === 0) {
                self::update(['is_delete' => 1], ['parent_id' => $attributeId]);
            }

            $value::update(['is_delete' => 1], ['goods_attribute_id' => $attributeId]);
        }

        return true;
    }

    /**
     * 获取基础数据索引列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getAttributeData(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        $map[] = ['goods_type_id', '=', $data['goods_type_id']];
        isset($data['attribute_all']) && $data['attribute_all'] == 1 ?: $map[] = ['is_delete', '=', 0];

        $field = 'goods_attribute_id,parent_id,attr_name,description,icon,is_important';
        return $this->where($map)->column($field, 'goods_attribute_id');
    }
}
