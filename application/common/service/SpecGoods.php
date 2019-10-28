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

use app\common\model\Spec;
use app\common\model\SpecItem;

class SpecGoods extends CareyShop
{
    /**
     * 将商品规格项还原成菜单结构数据
     * @access public
     * @param  array $data 待处理数据
     * @return array
     */
    public static function specItemToMenu($data)
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

        // 去重之后"$keyList"保持了项的排序先后
        $keyList = array_unique($keyList);

        $map = ['spec_item_id' => ['in', $keyList]];
        $specItemResult = SpecItem::where($map)->column('spec_id,item_name', 'spec_item_id');

        $idList = array_column($specItemResult, 'spec_id');
        if (empty($idList)) {
            return [];
        }

        $idList = array_unique($idList);
        $map = ['spec_id' => ['in', $idList]];
        $specResult = Spec::where($map)->column('name', 'spec_id');

        if (empty($specResult)) {
            return [];
        }

        // 必须使用"$keyList"做为循环主体,否则项的先后顺序无法保证输入前后的一致
        $sort = [];
        $result = [];

        foreach ($keyList as $value) {
            if (!array_key_exists($value, $specItemResult)) {
                continue;
            }

            $specId = $specItemResult[$value]['spec_id'];
            if (!array_key_exists($specId, $specResult)) {
                continue;
            }

            // 项的主体不存在时创建
            $key = array_search($specId, $sort);
            if (false === $key) {
                $sort[] = $specId;
                $result[] = [
                    'spec_id' => $specId,
                    'text'    => $specResult[$specId],
                    'value'   => [],
                ];

                $key = count($sort) - 1;
            }

            // 将项压入到主体中
            unset($specItemResult[$value]['spec_id']);
            $result[$key]['value'][] = $specItemResult[$value];
        }

        return $result;
    }
}
