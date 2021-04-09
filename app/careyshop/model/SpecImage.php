<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格图片模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class SpecImage extends CareyShop
{
    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'id',
        'goods_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'goods_id'     => 'integer',
        'spec_item_id' => 'integer',
        'spec_type'    => 'integer',
        'image'        => 'array',
    ];

    /**
     * 添加商品规格图片
     * @access public
     * @param int   $goodsId 商品编号
     * @param array $data    外部数据
     * @return bool
     */
    public function addSpecImage(int $goodsId, array $data): bool
    {
        // 处理部分数据,并进行验证
        foreach ($data as $key => $value) {
            $data[$key]['goods_id'] = $goodsId;

            if (!$this->validateData($data[$key])) {
                return false;
            }

            $data[$key]['image'] = json_encode($value['image'], JSON_UNESCAPED_UNICODE);
        }

        $this->insertAll($data);
        return true;
    }
}
