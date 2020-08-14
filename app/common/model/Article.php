<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    文章管理模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\common\model;

class Article extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'article_id';

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
        'article_id',
    ];

    /**
     * hasOne cs_article_cat
     * @access public
     * @return mixed
     */
    public function getArticleCat()
    {
        return $this
            ->hasOne(ArticleCat::class, 'article_cat_id', 'article_cat_id')
            ->joinType('left')
            ->field('article_cat_id,cat_name,cat_type')
            ->hidden(['article_cat_id']);
    }

    /**
     * 添加一篇文章
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addArticleItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['article_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一篇文章
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setArticleItem($data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['article_id', '=', $data['article_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除文章
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delArticleList($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['article_id']);
        return true;
    }

    /**
     * 获取一篇文章
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getArticleItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['article_id', '=', $data['article_id']];
        is_client_admin() ?: $map[] = ['status', '=', 1];

        $result = $this->where($map)->find();
        if (!is_null($result)) {
            $result->inc('page_views')->update();

            $views = $result->getAttr('page_views');
            $result->setAttr('page_views', $views + 1);

            return $result->toArray();
        }

        return null;
    }

    /**
     * 获取文章列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getArticleList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 获取分类Id,包括子分类
        $catIdList = [];
        if (isset($data['article_cat_id'])) {
            $catIdList[] = (int)$data['article_cat_id'];
            $articleCat = ArticleCat::getArticleCatList($data['article_cat_id']);

            foreach ($articleCat as $value) {
                $catIdList[] = $value['article_cat_id'];
            }
        }

        // 搜索条件
        $map = [];
        empty($catIdList) ?: $map['article.article_cat_id'] = ['in', $catIdList];
        empty($data['title']) ?: $map['article.title'] = ['like', '%' . $data['title'] . '%'];

        // 后台管理搜索
        if (is_client_admin()) {
            is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];
            is_empty_parm($data['is_top']) ?: $map[] = ['is_top', '=', $data['is_top']];
            empty($data['keywords']) ?: $map[] = ['keywords', 'like', '%' . $data['keywords'] . '%'];
        } else {
            $map[] = ['status', '=', 1];
        }

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $fixed = !is_client_admin() ? ['is_top' => 'desc'] : [];
        $result['items'] = $this->setDefaultOrder(['article_id' => 'desc'], $fixed)
            ->with('get_article_cat')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->withoutField('content')
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量设置文章置顶
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setArticleTop($data)
    {
        if (!$this->validateData($data, 'top')) {
            return false;
        }

        $map[] = ['article_id', 'in', $data['article_id']];
        self::update(['is_top' => $data['is_top']], $map);

        return true;
    }

    /**
     * 批量设置文章是否显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setArticleStatus($data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['article_id', 'in', $data['article_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }
}
