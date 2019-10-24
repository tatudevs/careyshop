<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格列表服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/10/24
 */

namespace app\common\service;

use app\common\model\SpecItem;

class SpecGoods extends CareyShop
{
    public function specItemToMenu($data)
    {
        if (!is_array($data) || empty($data)) {
            return [];
        }

        // 筛选出规格项编号集合
        $keyList = [];
        foreach ($data as $value) {
            $keyList = array_merge($keyList, explode('_', $value['key_name']));
        }

        if (empty($keyList)) {
            return [];
        }

        // 根据规格项编号获取数据
        $map = ['spec_item_id' => ['in', $keyList]];
        $specItemResult = SpecItem::where($map)->column('spec_id,item_name', 'spec_item_id');

        print_r($specItemResult);exit();
    }
}
