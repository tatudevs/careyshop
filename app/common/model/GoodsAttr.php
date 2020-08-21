<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性列表模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\model;

class GoodsAttr extends CareyShop
{
    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'id',
        'goods_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'goods_id',
        'goods_attribute_id',
        'parent_id',
    ];

    /**
     * 添加商品属性列表
     * @access public
     * @param int   $goodsId 商品编号
     * @param array $data    外部数据
     * @return bool
     * @throws
     */
    public function addGoodsAttr($goodsId, $data)
    {
        // 处理部分数据,并进行验证
        foreach ($data as $key => $value) {
            $data[$key]['goods_id'] = $goodsId;

            if (!$this->validateData($data[$key])) {
                return false;
            }
        }

        $result = $this->saveAll($data);
        return false !== $result;
    }
}
