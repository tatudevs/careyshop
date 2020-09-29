<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/3
 */

namespace app\common\model;

use think\facade\Cache;
use think\helper\Str;
use util\Http;

class Goods extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'goods_id';

    /**
     * 商品属性模型对象
     * @var GoodsAttr
     */
    private static $goodsAttr = null;

    /**
     * 商品规格模型对象
     * @var SpecGoods
     */
    private static $specGoods = null;

    /**
     * 商品规格图片模型对象
     * @var SpecImage
     */
    private static $specImage = null;

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'goods_id',
        'comment_sum',
        'sales_sum',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'goods_id'          => 'integer',
        'goods_category_id' => 'integer',
        'brand_id'          => 'integer',
        'store_qty'         => 'integer',
        'comment_sum'       => 'integer',
        'sales_sum'         => 'integer',
        'page_views'        => 'integer',
        'measure'           => 'float',
        'measure_type'      => 'integer',
        'is_postage'        => 'integer',
        'market_price'      => 'float',
        'shop_price'        => 'float',
        'integral_type'     => 'integer',
        'give_integral'     => 'float',
        'is_integral'       => 'integer',
        'least_sum'         => 'integer',
        'purchase_sum'      => 'integer',
        'attachment'        => 'array',
        'video'             => 'array',
        'is_recommend'      => 'integer',
        'is_new'            => 'integer',
        'is_hot'            => 'integer',
        'goods_type_id'     => 'integer',
        'sort'              => 'integer',
        'status'            => 'integer',
        'is_delete'         => 'integer',
    ];

    /**
     * hasMany cs_goods_attr
     * @access public
     * @return mixed
     */
    public function goodsAttrItem()
    {
        return $this->hasMany(GoodsAttr::class, 'goods_id');
    }

    /**
     * hasMany cs_spec_goods
     * @access public
     * @return mixed
     */
    public function goodsSpecItem()
    {
        return $this->hasMany(SpecGoods::class, 'goods_id');
    }

    /**
     * hasMany cs_spec_image
     * @access public
     * @return mixed
     */
    public function specImage()
    {
        return $this->hasMany(SpecImage::class, 'goods_id');
    }

    /**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
        !is_null(self::$goodsAttr) ?: self::$goodsAttr = new GoodsAttr();
        !is_null(self::$specGoods) ?: self::$specGoods = new SpecGoods();
        !is_null(self::$specImage) ?: self::$specImage = new SpecImage();
    }

    /**
     * 通用全局查询条件
     * @access public
     * @param object $query 模型
     */
    public function scopeGlobal($query)
    {
        $query->where([
            ['status', '=', 1],
            ['is_delete', '=', 0],
            ['store_qty', '>', 0],
        ]);
    }

    /**
     * 产生随机10位的商品货号
     * @access private
     * @return string
     */
    private function setGoodsCode()
    {
        do {
            $goodsCode = 'CS' . rand_number(8);
        } while (self::checkUnique(['goods_code' => $goodsCode]));

        return $goodsCode;
    }

    /**
     * 检测商品货号是否唯一
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueGoodsCode(array $data)
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['goods_code', '=', $data['goods_code']];
        !isset($data['exclude_id']) ?: $map[] = ['goods_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('商品货号已存在');
        }

        return true;
    }

    /**
     * 添加商品附加属性与规格
     * @access private
     * @param int    $goodsId 商品编号
     * @param array &$result  商品自身数据集
     * @param array  $data    外部数据
     * @return bool
     */
    private function addGoodSubjoin(int $goodsId, array &$result, array $data)
    {
        // 检测规格是否存在自定义,存在则更新,并且返回会附带规格图集合
        \app\common\service\SpecGoods::validateSpecMenu($data);

        // 插入商品属性列表
        if (!empty($data['attr_config'])) {
            $attrList = [];
            GoodsAttrConfig::updateAttrConfig($goodsId, $data['attr_config']);

            foreach ($data['attr_config'] as $value) {
                foreach ($value['get_attribute'] as $key => $item) {
                    if (!is_empty_parm($item['attr_value'])) {
                        $attr_value = is_array($item['attr_value'])
                            ? implode(' ', $item['attr_value'])
                            : $item['attr_value'];

                        $attrList[] = [
                            'goods_id'           => $goodsId,
                            'goods_attribute_id' => $item['goods_attribute_id'],
                            'parent_id'          => $item['parent_id'],
                            'is_important'       => $item['is_important'],
                            'attr_value'         => $attr_value,
                            'sort'               => $key,
                        ];
                    }
                }
            }

            if (!empty($attrList)) {
                if (false === self::$goodsAttr->addGoodsAttr($goodsId, $attrList)) {
                    return $this->setError(self::$goodsAttr->getError());
                }
            }
        }

        // 处理商品规格配置
        if (!empty($data['spec_config'])) {
            SpecConfig::updateSpecConfig($goodsId, $data['spec_config']);
        }

        // 插入商品规格组合列表
        if (!empty($data['spec_combo'])) {
            if (false === self::$specGoods->addGoodsSpec($goodsId, $data['spec_combo'])) {
                return $this->setError(self::$specGoods->getError());
            }

            // 计算实际商品库存并更新
            $result['store_qty'] = (int)array_sum(array_column($data['spec_combo'], 'store_qty'));
            $this->where('goods_id', '=', $goodsId)->save(['store_qty' => $result['store_qty']]);
        }

        // 插入商品规格图片
        if (!empty($data['spec_image'])) {
            if (false === self::$specImage->addSpecImage($goodsId, $data['spec_image'])) {
                return $this->setError(self::$specImage->getError());
            }
        }

        return true;
    }

    /**
     * 添加一个商品
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addGoodsItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 过滤无关字段及初始部分数据
        unset($data['goods_id'], $data['comment_sum'], $data['sales_sum'], $data['is_delete']);
        unset($data['create_time'], $data['update_time']);
        !empty($data['goods_code']) ?: $data['goods_code'] = $this->setGoodsCode();

        // 开启事务
        $this->startTrans();

        try {
            // 写入主表
            $this->save($data);
            $result = $this->toArray();

            // 写入属性与规格
            if (!$this->addGoodSubjoin($this->getAttr('goods_id'), $result, $data)) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            Cache::tag('GoodsCategory')->clear();

            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一个商品
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setGoodsItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (isset($data['goods_code'])) {
            $map[] = ['goods_id', '<>', $data['goods_id']];
            $map[] = ['goods_code', '=', $data['goods_code']];

            if (self::checkUnique($map)) {
                return $this->setError('商品货号已存在');
            }

            // 如果为空则产生一个随机货号
            !empty($data['goods_code']) ?: $data['goods_code'] = $this->setGoodsCode();
        }

        unset($map);
        $map[] = ['goods_id', '=', $data['goods_id']];

        // 开启事务
        $this->startTrans();

        try {
            // 更新主数据
            $goodsDB = self::update($data, $map);

            if (!empty($data['attr_config'])) {
                if (false === self::$goodsAttr->where($map)->delete()) {
                    throw new \Exception(self::$goodsAttr->getError());
                }
            }

            if (!empty($data['spec_config'])) {
                if (false === self::$specImage->where($map)->delete()) {
                    throw new \Exception(self::$specImage->getError());
                }
            }

            if (!empty($data['spec_combo'])) {
                if (false === self::$specGoods->where($map)->delete()) {
                    throw new \Exception(self::$specGoods->getError());
                }
            }

            $result = $goodsDB->toArray();
            if (!$this->addGoodSubjoin($data['goods_id'], $result, $data)) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取一个商品
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['goods_id']);
        if (!is_client_admin() && $result) {
            $result->inc('page_views')->update();
        }

        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量删除或恢复商品(回收站)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delGoodsList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['is_delete' => $data['is_delete']], $map);
        Cache::tag('GoodsCategory')->clear();

        return true;
    }

    /**
     * 获取指定编号商品的基础数据
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     */
    public function getGoodsSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        $field = 'goods_id,name,short_name,attachment,store_qty,sales_sum,status,is_delete';

        $order = [];
        $result = $this->where($map)->column($field, 'goods_id');

        // 根据传入顺序返回列表
        foreach ($data['goods_id'] as $value) {
            if (array_key_exists($value, $result)) {
                $order[] = $result[$value];
            }
        }

        return $order;
    }

    /**
     * 批量设置或关闭商品可积分抵扣
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setIntegralGoodsList(array $data)
    {
        if (!$this->validateData($data, 'integral')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['is_integral' => $data['is_integral']], $map);

        return true;
    }

    /**
     * 批量设置商品是否推荐
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setRecommendGoodsList(array $data)
    {
        if (!$this->validateData($data, 'recommend')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['is_recommend' => $data['is_recommend']], $map);

        return true;
    }

    /**
     * 批量设置商品是否为新品
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNewGoodsList(array $data)
    {
        if (!$this->validateData($data, 'new')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['is_new' => $data['is_new']], $map);

        return true;
    }

    /**
     * 批量设置商品是否为热卖
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setHotGoodsList(array $data)
    {
        if (!$this->validateData($data, 'hot')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['is_hot' => $data['is_hot']], $map);

        return true;
    }

    /**
     * 批量设置商品是否上下架
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setShelvesGoodsList(array $data)
    {
        if (!$this->validateData($data, 'shelves')) {
            return false;
        }

        $map[] = ['goods_id', 'in', $data['goods_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 获取指定商品的属性列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsAttrList(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return GoodsAttr::where('goods_id', '=', $data['goods_id'])->select()->toArray();
    }

    /**
     * 获取指定商品的规格组合列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getGoodsSpecList(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return SpecGoods::where('goods_id', '=', $data['goods_id'])->column('*', 'key_name');
    }

    /**
     * 获取指定商品的规格图
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsSpecImage(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return SpecImage::where('goods_id', '=', $data['goods_id'])->select()->toArray();
    }

    /**
     * 获取管理后台商品列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsAdminList(array $data)
    {
        if (!$this->validateData($data, 'admin_list')) {
            return false;
        }

        // 获取商品分类Id,包括子分类
        $catIdList = [];
        if (isset($data['goods_category_id'])) {
            if (0 == $data['goods_category_id'] || '' == $data['goods_category_id']) {
                $catIdList[] = 0;
            } else {
                $goodsCat = GoodsCategory::getCategoryList($data['goods_category_id'], false, true);
                $catIdList = array_column((array)$goodsCat, 'goods_category_id');
            }
        }

        // 搜索条件
        !isset($data['goods_id']) ?: $map[] = ['goods_id', 'in', $data['goods_id']];
        !isset($data['exclude_id']) ?: $map[] = ['goods_id', 'not in', $data['exclude_id']];
        empty($data['goods_code']) ?: $map[] = ['goods_code|goods_spu|goods_sku|bar_code', '=', $data['goods_code']];
        empty($data['brand_id']) ?: $map[] = ['brand_id', 'in', $data['brand_id']];
        empty($data['store_qty']) ?: $map[] = ['store_qty', 'between', $data['store_qty']];
        empty($catIdList) ?: $map[] = ['goods_category_id', 'in', $catIdList];
        is_empty_parm($data['is_postage']) ?: $map[] = ['is_postage', '=', $data['is_postage']];
        is_empty_parm($data['is_integral']) ?: $map[] = ['is_integral', '>', 0];
        is_empty_parm($data['is_recommend']) ?: $map[] = ['is_recommend', '=', $data['is_recommend']];
        is_empty_parm($data['is_new']) ?: $map[] = ['is_new', '=', $data['is_new']];
        is_empty_parm($data['is_hot']) ?: $map[] = ['is_hot', '=', $data['is_hot']];
        is_empty_parm($data['status']) || !empty($data['is_delete']) ?: $map[] = ['status', '=', $data['status']];
        $map[] = ['is_delete', '=', !empty($data['is_delete']) ? 1 : 0]; // 回收站中不存在"上下架"概念

        // 支持多个关键词搜索(空格分隔)
        if (!empty($data['keywords'])) {
            $keywords = explode(' ', $data['keywords']);
            foreach ($keywords as &$value) {
                $value = '%' . $value . '%';
            }

            unset($value);
            $map[] = ['name|short_name', 'like', $keywords, 'OR'];
        }

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['goods_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 根据商品分类获取指定类型的商品(推荐,热卖,新品,积分,同品牌,同价位)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsIndexType(array $data)
    {
        if (!$this->validateData($data, 'type_list')) {
            return false;
        }

        $map[] = ['status', '=', 1];
        $map[] = ['is_delete', '=', 0];
        $map[] = ['store_qty', '>', 0];
        empty($data['goods_category_id']) ?: $map[] = ['goods_category_id', '=', $data['goods_category_id']];
        is_empty_parm($data['brand_id']) ?: $map[] = ['brand_id', '=', $data['brand_id']];
        !isset($data['shop_price']) ?: $map[] = ['shop_price', 'between', $data['shop_price']];

        if (!is_empty_parm($data['goods_type'])) {
            switch ($data['goods_type']) {
                case 'integral':
                    $map[] = ['is_integral', '>', 0];
                    break;
                case 'recommend':
                    $map[] = ['is_recommend', '=', 1];
                    break;
                case 'new':
                    $map[] = ['is_new', '=', 1];
                    break;
                case 'hot':
                    $map[] = ['is_hot', '=', 1];
                    break;
            }
        }

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['goods_id' => 'desc'], ['sort' => 'asc'])
            ->field('goods_id,name,short_name,sales_sum,is_postage,market_price,shop_price,attachment')
            ->where($map)
            ->withSearch(['page'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 筛选价格与品牌后获取商品Id
     * @access private
     * @param array $data 外部数据
     * @return array
     */
    private function getGoodsIdByBrandPrice(array $data)
    {
        if (empty($data['shop_price']) && empty($data['brand_id'])) {
            return [];
        }

        // 搜索条件
        $map = [];

        if (!empty($data['shop_price'])) {
            $map[] = ['shop_price', 'between', $data['shop_price']];
        }

        if (!empty($data['brand_id'])) {
            $map[] = ['brand_id', 'in', $data['brand_id']];
        }

        // 启用全局搜索条件
        return $this->scope('global')->where($map)->column('goods_id');
    }

    /**
     * 筛选规格后获取商品Id
     * @access private
     * @param array $specList 规格列表
     * @return array
     */
    private function getGoodsIdBySpec(array $specList)
    {
        // 数组首位对应的是"cs_spec"中的"spec_id",非同一类值
        is_array(current($specList)) ?: array_shift($specList);

        if (empty($specList)) {
            return [];
        }

        // 子查询语句
        $subQuery = self::$specGoods->field("goods_id,concat('_', `key_name`, '_') as key_sub")->buildSql();

        foreach ($specList as $item) {
            if (is_array($item)) {
                array_shift($item);
                foreach ($item as &$value) {
                    $value = '%\_' . $value . '\_%';
                }

                unset($value);
            }

            if (empty($item)) {
                return [];
            }

            if (is_array($item)) {
                self::$specGoods->where([['s.key_sub', 'like', $item, 'or']]);
            } else {
                self::$specGoods->whereOr([['s.key_sub', 'like', '%\_' . $item . '\_%']]);
            }
        }

        return self::$specGoods->table($subQuery . ' s')->group('s.goods_id')->column('s.goods_id');
    }

    /**
     * 筛选属性后获取商品Id
     * @access private
     * @param array $attrList 属性列表
     * @return array
     */
    private function getGoodsIdByAttr(array $attrList)
    {
        if (empty($attrList)) {
            return [];
        }

        $attributeIdList = [];
        $valueList = [];

        if (is_array(current($attrList))) {
            foreach ($attrList as $value) {
                $attributeIdList[] = array_shift($value);
                $valueList = array_merge($valueList, $value);

                if (empty($value)) {
                    return [];
                }
            }
        } else {
            $attributeIdList[] = array_shift($attrList);
            $valueList = array_merge($valueList, $attrList);
        }

        if (empty($attributeIdList) || empty($valueList)) {
            return [];
        }

        $attributeIdList = array_unique($attributeIdList);
        $valueList = array_unique($valueList);

        // 排除主体属性
        $map[] = ['parent_id', '<>', 0];
        $map[] = ['goods_attribute_id', 'in', $attributeIdList];
        $map[] = ['attr_value', 'in', $valueList];

        return self::$goodsAttr->where($map)->group('goods_id')->column('goods_id');
    }

    /**
     * 获取筛选条件选中后的菜单
     * @access private
     * @param array $filterParam 筛选的参数
     * @return array
     */
    private function getFilterMenu(array $filterParam)
    {
        // 菜单列表
        $menuList = [];

        if (!empty($filterParam['brand'])) {
            $brandResult = Brand::cache(true, null, 'Brand')
                ->where('brand_id', 'in', $filterParam['brand'])
                ->column('name', 'brand_id');

            if ($brandResult) {
                $brand['text'] = '品牌：';
                foreach ($filterParam['brand'] as $value) {
                    if (isset($brandResult[$value])) {
                        $brand['text'] .= $brandResult[$value] . '、';
                    }
                }
                !Str::endsWith($brand['text'], '、') ?: $brand['text'] = Str::substr($brand['text'], 0, -1);
                $brand['value'] = $filterParam['brand'];
                $brand['param'] = 'brand_id';
                $menuList[] = $brand;
            }
        }

        if (!empty($filterParam['price'])) {
            $price['text'] = '价格：' . implode($filterParam['price'], '-');
            $price['value'] = $filterParam['price'];
            $price['param'] = 'shop_price';
            $menuList[] = $price;
        }

        if (!empty($filterParam['spec'])) {
            $specList = [];
            $specItemList = [];
            $specGroup = [];

            if (!is_array(current($filterParam['spec']))) {
                $specList = array_shift($filterParam['spec']);
                $specItemList = $filterParam['spec'];
                $specGroup[$specList] = $specItemList;
            } else {
                foreach ($filterParam['spec'] as $item) {
                    $specKey = array_shift($item);
                    $specGroup[$specKey] = $item;
                    $specList[] = $specKey;
                    $specItemList = array_merge($specItemList, $item);
                }
            }

            $specResult = Spec::where('spec_id', 'in', $specList)->column('name', 'spec_id');
            $specItemResult = SpecItem::where('spec_item_id', 'in', $specItemList)->column('item_name', 'spec_item_id');

            foreach ($specGroup as $key => $item) {
                if (isset($specResult[$key])) {
                    $spec['text'] = $specResult[$key] . '：';
                    foreach ($item as $value) {
                        if (isset($specItemResult[$value])) {
                            $spec['text'] .= $specItemResult[$value] . '、';
                        }
                    }
                    !Str::endsWith($spec['text'], '、') ?: $spec['text'] = Str::substr($spec['text'], 0, -1);
                    $spec['value'] = array_merge([$key], $item);
                    $spec['param'] = 'spec_list';
                    $menuList[] = $spec;
                }
            }
        }

        if (!empty($filterParam['attr'])) {
            $attrList = [];
            $attrGroup = [];

            if (!is_array(current($filterParam['attr']))) {
                $attrList = array_shift($filterParam['attr']);
                $attrGroup[$attrList] = $filterParam['attr'];
            } else {
                foreach ($filterParam['attr'] as $item) {
                    $attrKey = array_shift($item);
                    $attrGroup[$attrKey] = $item;
                    $attrList[] = $attrKey;
                }
            }

            $attrResult = GoodsAttribute::where('parent_id', '<>', 0)
                ->where('goods_attribute_id', 'in', $attrList)
                ->column('attr_name', 'goods_attribute_id');

            foreach ($attrGroup as $key => $item) {
                if (isset($attrResult[$key])) {
                    $attr['text'] = $attrResult[$key] . '：' . implode($attrGroup[$key], '、');
                    $attr['value'] = array_merge([$key], $item);
                    $attr['param'] = 'attr_list';
                    $menuList[] = $attr;
                }
            }
        }

        return $menuList;
    }

    /**
     * 根据商品Id生成价格筛选菜单
     * @access private
     * @param array $goodsIdList 商品编号
     * @param int   $page        价格分段
     * @return array
     */
    private function getFilterPrice(array $goodsIdList, $page = 5)
    {
        if (empty($goodsIdList)) {
            return [];
        }

        $priceResult = $this->where('goods_id', 'in', $goodsIdList)->group('shop_price')->column('shop_price');
        if (!$priceResult) {
            return [];
        }

        rsort($priceResult);
        $maxPrice = (int)$priceResult[0]; // 最大金额值
        $pageSize = ceil($maxPrice / $page); // 每一段累积的值
        $price = [];

        for ($i = 0; $i < $page; $i++) {
            $start = $i * $pageSize;
            $end = $start + $pageSize;

            $isIn = false;
            foreach ($priceResult as $value) {
                if ($value > $start && $value <= $end) {
                    $isIn = true;
                    continue;
                }
            }

            if ($isIn == false)
                continue;

            if ($i == 0) {
                $price[] = ['text' => $end . '以下', 'value' => [$start, $end]];
            } elseif ($i == ($page - 1)) {
                $price[] = ['text' => $start . '以上', 'value' => [$start, $end]];
            } else {
                $price[] = ['text' => $start . '-' . $end, 'value' => [$start, $end]];
            }
        }

        return $price;
    }

    /**
     * 根据商品Id生成品牌筛选菜单
     * @access private
     * @param array $goodsIdList 商品编号
     * @return array
     * @throws
     */
    private function getFilterBrand(array $goodsIdList)
    {
        if (empty($goodsIdList)) {
            return [];
        }

        // 子查询语句(此处查询没有进行全局查询)
        $map[] = ['brand_id', '>', 0];
        $map[] = ['goods_id', 'in', $goodsIdList];
        $subQuery = $this->field('brand_id')->where($map)->group('brand_id')->buildSql();

        $brandResult = Brand::cache(true, null, 'Brand')
            ->field('brand_id,name,phonetic,logo')
            ->where('status', '=', 1)
            ->whereExp('brand_id', 'IN ' . $subQuery)
            ->order(['sort' => 'asc', 'brand_id' => 'desc'])
            ->select();

        $result = [];
        foreach ($brandResult as $key => $value) {
            $result[$key]['text'] = $value->getAttr('name');
            $result[$key]['value'] = $value->toArray();
        }

        return $result;
    }

    /**
     * 提取规格或属性的主项Id
     * @access private
     * @param array  $filterParam 完整的筛选参数
     * @param string $key         筛选参数的键名
     * @return array
     */
    private function getSpecOrAttrItem(array $filterParam, string $key)
    {
        if (!isset($filterParam[$key])) {
            return [];
        }

        $data = [];
        foreach ($filterParam[$key] as $value) {
            if (is_array($value)) {
                $data[] = array_shift($value);
                continue;
            }

            $data[] = array_shift($filterParam[$key]);
            break;
        }

        return $data;
    }

    /**
     * 根据商品Id生成规格筛选菜单
     * @access private
     * @param array $goodsIdList 商品编号
     * @param array $filterParam 已筛选的条件
     * @return array
     * @throws
     */
    private function getFilterSpec(array $goodsIdList, array $filterParam)
    {
        if (empty($goodsIdList)) {
            return [];
        }

        // 根据商品编号获取所有规格项
        $specKeyList = self::$specGoods->field(['group_concat(key_name separator "_")' => 'key_name'])
            ->where('goods_id', 'in', $goodsIdList)
            ->find();

        if ($specKeyList) {
            $specKeyList = array_unique(explode('_', $specKeyList->getAttr('key_name')));
            $specKeyList = array_filter($specKeyList);
        }

        if (empty($specKeyList)) {
            return [];
        }

        // 获取筛选已选中的规格
        $selectSpec = $this->getSpecOrAttrItem($filterParam, 'spec');

        // 获取可检索的规格
        $map = [['goods_type_id', '<>', 0], ['spec_index', '=', 1]];
        empty($selectSpec) ?: $map[] = ['spec_id', 'not in', $selectSpec];

        $specResult = Spec::where($map)
            ->order(['sort' => 'asc', 'spec_id' => 'asc'])
            ->column('name', 'spec_id');

        // 根据规格获取对应的规格项
        $map = [
            ['spec_item_id', 'in', $specKeyList],
            ['spec_id', 'in', array_keys($specResult)],
            ['is_contact', '=', 1],
        ];

        // 生成(排除不符合的)规格筛选菜单,必须以"$spec_result"做循环,否则排序无效
        $result = [];
        $specItemResult = SpecItem::where($map)->column('spec_id,item_name', 'spec_item_id');

        foreach ($specResult as $key => $item) {
            foreach ($specItemResult as $value) {
                if ($value['spec_id'] == $key) {
                    $result[$key]['text'] = $item;
                    $result[$key]['value'][] = $value;
                    unset($specItemResult[$value['spec_item_id']]); // 加速性能
                    continue;
                }
            }
        }

        // 必须"array_values"返回,否则排序无效
        return array_values($result);
    }

    /**
     * 根据商品Id生成属性筛选菜单
     * @access private
     * @param array $goodsIdList 商品编号
     * @param array $filterParam 已筛选的条件
     * @return array
     * @throws
     */
    private function getFilterAttr(array $goodsIdList, array $filterParam)
    {
        if (empty($goodsIdList)) {
            return [];
        }

        // 根据商品编号获取所有属性列表
        $goodsArrtResult = self::$goodsAttr->field('goods_attribute_id,attr_value,sort')
            ->where('goods_id', 'in', $goodsIdList)
            ->where([['parent_id', '<>', 0], ['attr_value', '<>', '']])
            ->group('attr_value')
            ->order(['sort' => 'asc', 'goods_attribute_id' => 'asc'])
            ->select();

        if ($goodsArrtResult->isEmpty()) {
            return [];
        }

        // 获取筛选已选中的属性
        $selectAttr = $this->getSpecOrAttrItem($filterParam, 'attr');

        // 获取可检索的属性
        $map[] = ['parent_id&attr_index', '<>', 0];
        $map[] = ['is_delete', '=', 0];
        empty($selectAttr) ?: $map[] = ['goods_attribute_id', 'not in', $selectAttr];

        $attrResult = GoodsAttribute::field('goods_attribute_id,attr_name')
            ->where($map)
            ->order(['sort' => 'asc', 'goods_attribute_id' => 'asc'])
            ->select()
            ->toArray();

        // 生成属性筛选菜单,必须以"$attr_result"做循环,否则排序无效
        $result = [];
        foreach ($attrResult as $item) {
            foreach ($goodsArrtResult as $key => $value) {
                if ($item['goods_attribute_id'] == $value['goods_attribute_id']) {
                    $result[$item['goods_attribute_id']]['text'] = $item['attr_name'];
                    $result[$item['goods_attribute_id']]['value'][] = $value->toArray();
                    unset($goodsArrtResult[$key]); // 加速性能
                    continue;
                }
            }
        }

        // 必须"array_values"返回,否则排序无效
        return array_values($result);
    }

    /**
     * 搜索商品时返回对应的商品分类
     * @access private
     * @param array $goodsIdList 商品编号
     * @param array $data        外部数据
     * @return array
     */
    private function getFilterCate(array $goodsIdList, array $data)
    {
        if (empty($data['keywords'])) {
            return [];
        }

        // 如果分类Id为空表示搜索全部商品
        if (empty($data['goods_category_id'])) {
            $map[] = ['goods_id', 'in', $goodsIdList];
            $data['goods_category_id'] = array_unique($this->where($map)->column('goods_category_id'));

            $result = [];
            $cateList = GoodsCategory::getCategoryList();
            foreach ($data['goods_category_id'] as $item) {
                foreach ($cateList as $value) {
                    if ($value['goods_category_id'] == $item) {
                        $result[] = $value;
                        break;
                    }
                }
            }

            return $result;
        }

        $result = GoodsCategory::getCategoryList($data['goods_category_id'], false, true);
        if (false === $result) {
            return [];
        }

        return $result;
    }

    /**
     * 判断商品分类是否存在,并且取该分类所有的子Id
     * @access public
     * @param array &$data          外部数据
     * @param array  $goodsCateList 购物车商品列表
     * @return bool
     */
    private function isCategoryList(array $data, array &$goodsCateList)
    {
        $categoryId = isset($data['goods_category_id']) ? $data['goods_category_id'] : 0;
        $cateList = GoodsCategory::getCategoryList($categoryId, false, true);

        if (empty($cateList)) {
            return false;
        }

        $goodsCateList = array_column((array)$cateList, 'goods_category_id');
        return true;
    }

    /**
     * 根据商品分类获取前台商品列表页
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getGoodsIndexList(array $data)
    {
        if (!$this->validateData($data, 'index_list')) {
            return false;
        }

        // 需要保留"$data['goods_category_id']",用以判断搜索时的分类条件
        $goodsCateList = [];
        if (!$this->isCategoryList($data, $goodsCateList)) {
            return $this->setError('商品分类不存在');
        }

        // 搜索条件
        $map[] = ['goods_category_id', 'in', $goodsCateList];
        is_empty_parm($data['is_postage']) ?: $map[] = ['is_postage', '=', $data['is_postage']];
        is_empty_parm($data['is_integral']) ?: $map[] = ['is_integral', '>', 0];
        empty($data['bar_code']) ?: $map[] = ['bar_code', '=', $data['bar_code']];

        // 支持多个关键词搜索(空格分隔)
        if (!empty($data['keywords'])) {
            $keywords = explode(' ', $data['keywords']);
            foreach ($keywords as &$value) {
                $value = '%' . $value . '%';
            }

            unset($value);
            $map[] = ['name|short_name', 'like', $keywords, 'OR'];
        }

        $result = [];
        $filterParam = []; // 将筛选条件归类(所有的筛选都是数组)

        // 根据分类数组获取所有对应的商品Id
        $goodsIdList = $this->scope('global')->where($map)->column('goods_id');

        // 对商品进行价格与品牌筛选
        if (!empty($data['shop_price']) || !empty($data['brand_id'])) {
            $priceBrandIdList = $this->getGoodsIdByBrandPrice($data);
            $goodsIdList = array_intersect($goodsIdList, $priceBrandIdList);

            empty($data['shop_price']) ?: $filterParam['price'] = $data['shop_price'];
            empty($data['brand_id']) ?: $filterParam['brand'] = $data['brand_id'];
        }

        // 对商品进行规格筛选
        if (!empty($data['spec_list'])) {
            $specIdList = $this->getGoodsIdBySpec($data['spec_list']);
            $goodsIdList = array_intersect($goodsIdList, $specIdList);
            $filterParam['spec'] = $data['spec_list'];
        }

        // 对商品进行属性筛选
        if (!empty($data['attr_list'])) {
            $attrIdList = $this->getGoodsIdByAttr($data['attr_list']);
            $goodsIdList = array_intersect($goodsIdList, $attrIdList);
            $filterParam['attr'] = $data['attr_list'];
        }

        // 根据筛选后的商品Id生成各项菜单
        $result['filter_menu'] = $this->getFilterMenu($filterParam);
        $result['filter_price'] = empty($filterParam['price']) ? $this->getFilterPrice($goodsIdList) : [];
        $result['filter_brand'] = empty($filterParam['brand']) ? $this->getFilterBrand($goodsIdList) : [];
        $result['filter_spec'] = $this->getFilterSpec($goodsIdList, $filterParam);
        $result['filter_attr'] = $this->getFilterAttr($goodsIdList, $filterParam);
        $result['filter_cate'] = $this->getFilterCate($goodsIdList, $data);

        // 获取总数量,为空直接返回
        $result['total_result'] = count($goodsIdList);
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 过滤不需要的字段
        $field = 'goods_category_id,goods_code,goods_spu,goods_sku,bar_code,integral_type,give_integral,';
        $field .= 'is_integral,measure,unit,measure_type,keywords,description,content,goods_type_id,status,';
        $field .= 'is_delete,create_time,update_time';

        $result['items'] = $this->setDefaultOrder(['goods_id' => 'desc'])
            ->where('goods_id', 'in', $goodsIdList)
            ->withoutField($field)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 设置商品排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setGoodsSort(array $data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['goods_id', '=', $data['goods_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 获取商品关键词联想词
     * @access public
     * @param array $data 外部数据
     * @return array
     */
    public function getGoodsKeywordsSuggest(array $data)
    {
        if (!$this->validateData($data, 'suggest')) {
            return [];
        }

        $url = 'https://suggest.taobao.com/sug?code=utf-8&q=' . urlencode($data['keywords']);
        $httpResult = json_decode(Http::httpGet($url), true);

        $result = [];
        if (isset($httpResult['result'])) {
            $result = array_column($httpResult['result'], 0);
        }

        return $result;
    }

    /**
     * 复制一个商品
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function copyGoodsItem(array $data)
    {
        if (!isset($data['goods_id'])) {
            return $this->setError('商品编号不能为空');
        }

        $result = $this->with('goods_spec_item')->where('goods_id', '=', $data['goods_id'])->find();
        if (is_null($result)) {
            return $this->setError('商品不存在');
        }

        // 清理原始数据
        $copyData = $result->toArray();
        $copyData['goods_code'] = '';
        $copyData['comment_sum'] = 0;
        $copyData['sales_sum'] = 0;

        $copyData['spec_combo'] = $copyData['goods_spec_item'];
        unset($copyData['goods_spec_item']);

        // 获取规格与属性配置数据
        $specConfigData = (new SpecConfig())->getSpecConfigItem($data);
        if ($specConfigData) {
            $copyData['spec_config'] = $specConfigData['spec_config'];
        }

        $attrConfigData = (new GoodsAttrConfig())->getAttrConfigItem($data);
        if ($attrConfigData) {
            $copyData['attr_config'] = $attrConfigData['attr_config'];
        }

        return $this->addGoodsItem($copyData);
    }

    /**
     * 获取指定商品的规格菜单数据
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     */
    public function getGoodsSpecMenu(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $specList = $this->getGoodsSpecList($data);
        if (false === $specList) {
            return false;
        }

        if (empty($specList)) {
            return [];
        }

        $result = \app\common\service\SpecGoods::specItemToMenu($specList, $data['goods_id']);
        return ['spec_combo' => $specList, 'spec_config' => $result];
    }
}
