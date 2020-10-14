<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    文章分类模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\common\model;

use think\facade\Cache;

class ArticleCat extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'article_cat_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'article_cat_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'article_cat_id' => 'integer',
        'parent_id'      => 'integer',
        'cat_type'       => 'integer',
        'sort'           => 'integer',
        'is_navi'        => 'integer',
    ];

    /**
     * 添加一个文章分类
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addArticleCatItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['article_cat_id']);

        if ($this->save($data)) {
            Cache::tag('ArticleCat')->clear();
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个文章分类
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setArticleCatItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 父分类不能设置成本身或本身的子分类
        if (isset($data['parent_id'])) {
            if ($data['parent_id'] == $data['article_cat_id']) {
                return $this->setError('上级分类不能设为自身');
            }

            $result = self::getArticleCatList($data['article_cat_id']);
            foreach ($result as $value) {
                if ($data['parent_id'] == $value['article_cat_id']) {
                    return $this->setError('上级分类不能设为自身的子分类');
                }
            }
        }

        // 搜索条件
        $map[] = ['article_cat_id', '=', $data['article_cat_id']];
        $result = self::update($data, $map);

        Cache::tag('ArticleCat')->clear();
        return $result->toArray();
    }

    /**
     * 批量删除文章分类(支持检测是否存在子节点与关联文章)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delArticleCatList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $idList = $result = [];
        isset($data['not_empty']) ?: $data['not_empty'] = 0;

        if (!empty($data['not_empty'])) {
            $idList = $data['article_cat_id'];
            $result = self::getArticleCatList();
        }

        // 过滤不需要的分类
        $catFilter = [];
        foreach ($result as $value) {
            if ($value['children_total'] > 0 || $value['article_total'] > 0) {
                $catFilter[$value['article_cat_id']] = $value;
            }
        }

        foreach ($idList as $catId) {
            if (array_key_exists($catId, $catFilter)) {
                if ($catFilter[$catId]['children_total'] > 0) {
                    return $this->setError('Id:' . $catId . ' 分类名称"' . $catFilter[$catId]['cat_name'] . '"存在子分类');
                }

                if ($catFilter[$catId]['article_total'] > 0) {
                    return $this->setError('Id:' . $catId . ' 分类名称"' . $catFilter[$catId]['cat_name'] . '"存在关联内容');
                }
            }
        }

        self::destroy($data['article_cat_id']);
        Cache::tag('ArticleCat')->clear();

        return true;
    }

    /**
     * 获取一个文章分类
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getArticleCatItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['article_cat_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取分类所有列表
     * @access public static
     * @param int  $catId   分类Id
     * @param bool $isLayer 是否返回本级分类
     * @param null $level   分类深度
     * @param null $isNavi  过滤是否导航
     * @return array
     */
    public static function getArticleCatList($catId = 0, $isLayer = false, $level = null, $isNavi = null)
    {
        // 子查询,查询关联的文章数量
        $article = Article::field('article_cat_id,count(*) num')
            ->group('article_cat_id')
            ->where(is_client_admin() ? [] : [['status', '=', 1]])
            ->buildSql();

        // 搜索条件
        $map = [];
        is_null($isNavi) ?: $map[] = ['c.is_navi', '=', $isNavi];

        $result = self::cache(true, null, 'ArticleCat')
            ->alias('c')
            ->field('c.*,count(s.article_cat_id) children_total,ifnull(a.num, 0) article_total')
            ->join('article_cat s', 's.parent_id = c.article_cat_id', 'left')
            ->join([$article => 'a'], 'a.article_cat_id = c.article_cat_id', 'left')
            ->where($map)
            ->group('c.article_cat_id')
            ->order('c.parent_id,c.sort,c.article_cat_id')
            ->select();

        // 生成参数缓存标签
        $treeCache = sprintf('ArticleCat:id%dis_layer%dlevel%dis_navi%d',
            $catId, $isLayer, is_null($level) ? -1 : $level, is_null($isNavi) ? -1 : $isNavi);

        if (Cache::has($treeCache)) {
            return Cache::get($treeCache);
        }

        // 处理原始数据至分类数据
        $tree = self::setArticleCatTree((int)$catId, $result, $level, $isLayer);
        Cache::tag('ArticleCat')->set($treeCache, $tree);

        return $tree;
    }

    /**
     * 过滤和排序所有分类
     * @access private
     * @param int    $parentId   上级分类Id
     * @param object $list       原始模型对象
     * @param null   $limitLevel 显示多少级深度 null:全部
     * @param bool   $isLayer    是否返回本级分类
     * @param int    $level      分类深度
     * @return array
     */
    private static function setArticleCatTree(int $parentId, &$list, $limitLevel = null, $isLayer = false, $level = 0)
    {
        static $tree = [];
        $parentId != 0 ?: $isLayer = false; // 返回全部分类不需要本级

        foreach ($list as $key => $value) {
            // 获取分类主Id
            $articleCatId = $value->getAttr('article_cat_id');
            if ($value->getAttr('parent_id') !== $parentId && $articleCatId !== $parentId) {
                continue;
            }

            // 是否返回本级分类
            if ($articleCatId === $parentId && !$isLayer) {
                continue;
            }

            // 限制分类显示深度
            if (!is_null($limitLevel) && $level > $limitLevel) {
                break;
            }

            $value->setAttr('level', $level);
            $tree[] = $value->toArray();

            // 需要返回本级分类时保留列表数据,否则引起树的重复,并且需要自增层级
            if (true == $isLayer) {
                $isLayer = false;
                $level++;
                continue;
            }

            // 删除已使用数据,减少查询次数
            unset($list[$key]);

            if ($value->getAttr('children_total') > 0) {
                self::setArticleCatTree($articleCatId, $list, $limitLevel, $isLayer, $level + 1);
            }
        }

        return $tree;
    }

    /**
     * 获取分类导航数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getArticleCatNavi(array $data)
    {
        if (!$this->validateData($data, 'navi')) {
            return false;
        }

        if (empty($data['article_cat_id'])) {
            return [];
        }

        $list = $this->cache('ArticleCatNavi', null, 'ArticleCat')->column('parent_id,cat_name', 'article_cat_id');
        $isLayer = !is_empty_parm($data['is_layer']) ? (bool)$data['is_layer'] : true;

        if (!$isLayer && isset($list[$data['article_cat_id']])) {
            $data['article_cat_id'] = $list[$data['article_cat_id']]['parent_id'];
        }

        $result = [];
        while (true) {
            if (!isset($list[$data['article_cat_id']])) {
                break;
            }

            $result[] = $list[$data['article_cat_id']];

            if ($list[$data['article_cat_id']]['parent_id'] <= 0) {
                break;
            }

            $data['article_cat_id'] = $list[$data['article_cat_id']]['parent_id'];
        }

        return array_reverse($result);
    }

    /**
     * 设置文章分类排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setArticleCatSort(array $data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        // 搜索条件
        $map[] = ['article_cat_id', '=', $data['article_cat_id']];

        self::update(['sort' => $data['sort']], $map);
        Cache::tag('ArticleCat')->clear();

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setArticleCatIndex(array $data)
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['article_cat_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['article_cat_id' => $value]);
        }

        Cache::tag('ArticleCat')->clear();
        return true;
    }

    /**
     * 批量设置是否导航
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setArticleCatNavi(array $data)
    {
        if (!$this->validateData($data, 'nac')) {
            return false;
        }

        // 搜索条件
        $map[] = ['article_cat_id', 'in', $data['article_cat_id']];

        self::update(['is_navi' => $data['is_navi']], $map);
        Cache::tag('ArticleCat')->clear();

        return true;
    }
}
