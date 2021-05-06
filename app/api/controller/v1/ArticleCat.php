<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    文章分类控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/30
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use think\exception\ValidateException;

class ArticleCat extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一个文章分类
            'add.article.cat.item'  => ['addArticleCatItem'],
            // 编辑一个文章分类
            'set.article.cat.item'  => ['setArticleCatItem'],
            // 批量删除文章分类
            'del.article.cat.list'  => ['delArticleCatList'],
            // 获取一个文章分类
            'get.article.cat.item'  => ['getArticleCatItem'],
            // 获取文章分类列表
            'get.article.cat.list'  => ['getArticleCatList'],
            // 获取分类导航数据
            'get.article.cat.navi'  => ['getArticleCatNavi'],
            // 设置文章分类排序
            'set.article.cat.sort'  => ['setArticleCatSort'],
            // 根据编号自动排序
            'set.article.cat.index' => ['setArticleCatIndex'],
            // 批量设置是否导航
            'set.article.cat.navi'  => ['setArticleCatNavi'],
        ];
    }

    /**
     * 获取文章分类列表
     * @access public
     * @param int  $articleCatId 文章分类Id
     * @param bool $isLayer      是否返回本级分类
     * @param null $level        分类深度
     * @return array|false
     */
    public function getArticleCatList(int $articleCatId = 0, bool $isLayer = false, $level = null)
    {
        try {
            $catData = $this->getParams();
            self::$model->validateData($catData, 'list');
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        !isset($catData['level']) ?: $level = $catData['level'];
        !isset($catData['article_cat_id']) ?: $articleCatId = $catData['article_cat_id'];
        empty($catData['is_layer']) || ($isLayer = true);
        $isNavi = is_empty_parm($catData['is_navi']) ? null : $catData['is_navi']; // 处理是否过滤导航

        return self::$model->getArticleCatList($articleCatId, $isLayer, $level, $isNavi);
    }
}
