<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品分类控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/1
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use think\exception\ValidateException;

class GoodsCategory extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一个商品分类
            'add.goods.category.item'   => ['addCategoryItem'],
            // 编辑一个商品分类
            'set.goods.category.item'   => ['setCategoryItem'],
            // 批量删除商品分类(支持检测是否存在子节点与关联商品)
            'del.goods.category.list'   => ['delCategoryList'],
            // 获取一个商品分类
            'get.goods.category.item'   => ['getCategoryItem'],
            // 获取商品分类列表
            'get.goods.category.list'   => ['getCategoryList'],
            // 获取所有子级分类
            'get.goods.category.son'    => ['getCategorySon'],
            // 获取分类导航数据
            'get.goods.category.navi'   => ['getCategoryNavi'],
            // 批量设置是否显示
            'set.goods.category.status' => ['setCategoryStatus'],
            // 设置商品分类排序
            'set.goods.category.sort'   => ['setCategorySort'],
            // 根据编号自动排序
            'set.goods.category.index'  => ['setCategoryIndex'],
            // 批量设置是否导航
            'set.goods.category.navi'   => ['setCategoryNavi'],
        ];
    }

    /**
     * 获取所有子级分类
     * @access public
     * @return array|false
     */
    public function getCategorySon()
    {
        try {
            $catData = $this->getParams();
            self::$model->validateData($catData, 'son');
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        return self::$model->getCategorySon($catData);
    }

    /**
     * 获取商品分类列表
     * @access public
     * @param int  $catId        分类Id
     * @param bool $isGoodsTotal 是否获取关联商品数
     * @param bool $isLayer      是否返回本级分类
     * @param null $level        分类深度
     * @return array|false
     */
    public function getCategoryList(int $catId = 0, bool $isGoodsTotal = false, bool $isLayer = false, $level = null)
    {
        try {
            $catData = $this->getParams();
            self::$model->validateData($catData, 'list');
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        !isset($catData['goods_category_id']) ?: $catId = $catData['goods_category_id'];
        !isset($catData['level']) ?: $level = $catData['level'];
        empty($catData['goods_total']) || ($isGoodsTotal = true);
        empty($catData['is_layer']) || ($isLayer = true);

        return self::$model->getCategoryList($catId, $isGoodsTotal, $isLayer, $level);
    }
}
