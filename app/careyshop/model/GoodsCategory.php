<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品分类模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/27
 */

namespace app\careyshop\model;

use think\facade\Cache;
use think\helper\Str;
use util\Phonetic;

class GoodsCategory extends CareyShop
{
    /**
     * 分类树
     * @var array
     */
    private static array $tree = [];

    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'goods_category_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'goods_category_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'goods_category_id' => 'integer',
        'parent_id'         => 'integer',
        'category_type'     => 'integer',
        'sort'              => 'integer',
        'is_navi'           => 'integer',
        'status'            => 'integer',
    ];

    /**
     * 添加一个商品分类
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addCategoryItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['goods_category_id']);

        // 识别并转换分类名称首拼
        if (empty($data['name_phonetic'])) {
            $data['name_phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
            $data['name_phonetic'] = Str::lower($data['name_phonetic']);
        }

        // 识别并转换分类别名首拼
        if (!empty($data['alias']) && empty($data['alias_phonetic'])) {
            $data['alias_phonetic'] = Phonetic::encode(Str::substr($data['alias'], 0, 1));
            $data['alias_phonetic'] = Str::lower($data['alias_phonetic']);
        }

        if ($this->save($data)) {
            Cache::tag('GoodsCategory')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个商品分类
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setCategoryItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 父分类不能设置成本身或本身的子分类
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['goods_category_id']) {
                return $this->setError('上级分类不能设为自身');
            }

            if (false === ($result = self::getCategoryList($data['goods_category_id']))) {
                return false;
            }

            foreach ($result as $value) {
                if ($data['parent_id'] == $value['goods_category_id']) {
                    return $this->setError('上级分类不能设为自身的子分类');
                }
            }
        }

        if (!empty($data['name']) && empty($data['name_phonetic'])) {
            $data['name_phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
            $data['name_phonetic'] = Str::lower($data['name_phonetic']);
        }

        if (!empty($data['alias']) && empty($data['alias_phonetic'])) {
            $data['alias_phonetic'] = Phonetic::encode(Str::substr($data['alias'], 0, 1));
            $data['alias_phonetic'] = Str::lower($data['alias_phonetic']);
        }

        $map[] = ['goods_category_id', '=', $data['goods_category_id']];
        $result = self::update($data, $map);
        Cache::tag('GoodsCategory')->clear();

        return $result->toArray();
    }

    /**
     * 批量删除商品分类(支持检测是否存在子节点与关联商品)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCategoryList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $idList = $result = [];
        $data['not_empty'] ??= 0;

        if (1 == $data['not_empty']) {
            $idList = $data['goods_category_id'];
            if (false === ($result = self::getCategoryList(0, true))) {
                return false;
            }
        }

        // 过滤不需要的分类
        $catFilter = [];
        foreach ($result as $value) {
            if ($value['children_total'] > 0 || $value['goods_total'] > 0) {
                $catFilter[$value['goods_category_id']] = $value;
            }
        }

        foreach ($idList as $catId) {
            if (array_key_exists($catId, $catFilter)) {
                if ($catFilter[$catId]['children_total'] > 0) {
                    return $this->setError('Id:' . $catId . ' 分类名称"' . $catFilter[$catId]['name'] . '"存在子分类');
                }

                if ($catFilter[$catId]['goods_total'] > 0) {
                    return $this->setError('Id:' . $catId . ' 分类名称"' . $catFilter[$catId]['name'] . '"存在关联商品');
                }
            }
        }

        self::destroy($data['goods_category_id']);
        Cache::tag('GoodsCategory')->clear();

        return true;
    }

    /**
     * 获取一个商品分类
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCategoryItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['goods_category_id'])->toArray();
    }

    /**
     * 获取分类导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCategoryNavi(array $data)
    {
        if (!$this->validateData($data, 'navi')) {
            return false;
        }

        if (empty($data['goods_category_id'])) {
            return [];
        }

        $catList = $this
            ->cache('GoodsCategoryNavi', null, 'GoodsCategory')
            ->order('sort,goods_category_id')
            ->column('goods_category_id,parent_id,name,alias', 'goods_category_id');

        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;
        if (!$isLayer && isset($catList[$data['goods_category_id']])) {
            $data['goods_category_id'] = $catList[$data['goods_category_id']]['parent_id'];
        }

        $result = [];
        for ($i = 0; true; $i++) {
            if (!isset($catList[$data['goods_category_id']])) {
                break;
            }

            $result[$i] = $catList[$data['goods_category_id']];
            if (!empty($data['is_same_level'])) {
                foreach ($catList as $key => $value) {
                    if ($result[$i]['goods_category_id'] == $key) {
                        continue;
                    }

                    if ($value['parent_id'] == $result[$i]['parent_id']) {
                        // 既然是同级,那么就没必要再返回父级Id
                        unset($value['parent_id']);
                        $result[$i]['same_level'][] = $value;
                    }
                }
            }

            if ($catList[$data['goods_category_id']]['parent_id'] <= 0) {
                break;
            }

            $data['goods_category_id'] = $catList[$data['goods_category_id']]['parent_id'];
        }

        // 导航需要反转的顺序返回
        return array_reverse($result);
    }

    /**
     * 批量设置是否显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCategoryStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['goods_category_id', 'in', $data['goods_category_id']];
        self::update(['status' => $data['status']], $map);
        Cache::tag('GoodsCategory')->clear();

        return true;
    }

    /**
     * 过滤和排序所有商品分类
     * @access private
     * @param int     $parentId   上级分类Id
     * @param object &$list       原始模型对象
     * @param null    $limitLevel 显示多少级深度 null:全部
     * @param bool    $isLayer    是否返回本级分类
     * @param int     $level      分类深度
     * @return array
     */
    private static function setCategoryTree(int $parentId, object &$list, $limitLevel = null, $isLayer = false, $level = 0): array
    {
        $parentId != 0 ?: $isLayer = false; // 返回全部分类不需要本级
        foreach ($list as $key => $value) {
            // 获取分类主Id
            $goodsCategoryId = $value->getAttr('goods_category_id');
            if ($value->getAttr('parent_id') !== $parentId && $goodsCategoryId !== $parentId) {
                continue;
            }

            // 是否返回本级分类
            if ($goodsCategoryId === $parentId && false == $isLayer) {
                continue;
            }

            // 限制分类显示深度
            if (!is_null($limitLevel) && $level > $limitLevel) {
                break;
            }

            $value->setAttr('level', $level);
            self::$tree[] = $value->toArray();

            // 需要返回本级分类时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }

            // 删除已使用数据,减少查询次数
            unset($list[$key]);

            if ($value->getAttr('children_total') > 0) {
                self::setCategoryTree($goodsCategoryId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }

        return self::$tree;
    }

    /**
     * 获取所有商品分类
     * @access public
     * @param int  $catId        分类Id
     * @param bool $isGoodsTotal 是否获取关联商品数
     * @param bool $isLayer      是否返回本级分类
     * @param null $level        分类深度
     * @return array
     * @throws
     */
    public static function getCategoryList($catId = 0, $isGoodsTotal = false, $isLayer = false, $level = null): array
    {
        // 搜索条件
        $map = [];
        $joinMap = '';

        if (!is_client_admin()) {
            $map[] = ['c.status', '=', 1];
            $joinMap = ' AND s.status = ' . 1;
        }

        // 构建子查询
        $goodsSql = $goodsTotal = '';
        if ($isGoodsTotal) {
            $goodsTotal = ',ifnull(g.num, 0) goods_total';
            $goodsSql = Goods::field('goods_category_id,count(*) num')
                ->where('is_delete', '=', 0)
                ->group('goods_category_id')
                ->buildSql();
        }

        // 构建主查询
        $db = self::where($map)
            ->alias('c')
            ->field('c.*,count(s.goods_category_id) children_total' . $goodsTotal)
            ->join('goods_category s', 's.parent_id = c.goods_category_id' . $joinMap, 'left')
            ->group('c.goods_category_id')
            ->order('c.parent_id,c.sort,c.goods_category_id')
            ->cache(true, null, 'GoodsCategory');

        if ($isGoodsTotal) {
            $db = $db->join([$goodsSql => 'g'], 'g.goods_category_id = c.goods_category_id', 'left');
        }

        // 获取商品全部分类
        $result = $db->select();

        // 缓存名称
        $treeCache = sprintf('GoodsCat:admin%dtotal%d', is_client_admin(), $isGoodsTotal);
        $treeCache .= sprintf('id%dis_layer%dlevel%d', $catId, $isLayer, $level ?? -1);

        if (Cache::has($treeCache)) {
            return Cache::get($treeCache);
        }

        self::$tree = [];
        $tree = self::setCategoryTree((int)$catId, $result, $level, $isLayer);
        Cache::tag('GoodsCategory')->set($treeCache, $tree);

        return $tree;
    }

    /**
     * 根据主Id集合获取所有子级
     * @access public
     * @param array $data 外部数据
     * @return array
     */
    public static function getCategorySon(array $data): array
    {
        if (empty($data['goods_category_id'])) {
            return [];
        }

        $level = $data['level'] ?? null;
        $isGoodsTotal = !is_empty_parm($data['goods_total']) ? $data['goods_total'] : false;
        $isLayer = !is_empty_parm($data['is_layer']) ? $data['is_layer'] : true;

        $result = [];
        foreach ($data['goods_category_id'] as $value) {
            self::$tree = [];
            $list = self::getCategoryList($value, $isGoodsTotal, $isLayer, $level);

            if ($list) {
                $result = [...$result, ...$list];
            }
        }

        return $result;
    }

    /**
     * 设置商品分类排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCategorySort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['goods_category_id', '=', $data['goods_category_id']];
        self::update(['sort' => $data['sort']], $map);
        Cache::tag('GoodsCategory')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setCategoryIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['goods_category_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['goods_category_id' => $value]);
        }

        Cache::tag('GoodsCategory')->clear();
        return true;
    }

    /**
     * 批量设置是否导航
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCategoryNavi(array $data): bool
    {
        if (!$this->validateData($data, 'nac')) {
            return false;
        }

        $map[] = ['goods_category_id', 'in', $data['goods_category_id']];
        self::update(['is_navi' => $data['is_navi']], $map);
        Cache::tag('GoodsCategory')->clear();

        return true;
    }
}
