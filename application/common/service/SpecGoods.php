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
        $specResult = Spec::where($map)->column('name,spec_type', 'spec_id');

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
                    'text'    => $specResult[$specId]['name'],
                    'type'    => $specResult[$specId]['spec_type'],
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

    /**
     * 检测规格菜单是否存在自定义,并且替换原始数据
     * @access public
     * @param  array $data 外部数据
     * @return array
     */
    public static function validateSpecMenu(&$data)
    {
        // 待替换内容 key=查找内容 value=替换为
        $replace = [];
        $specMenu = $data['goods_spec_menu'];

        foreach ($specMenu as &$value) {
            // 判断主体是否有变更,如果主体变更,则项无条件重新插入
            $isChange = false;

            // 检测是否需要添加规格主体
            if ($value['spec_id'] <= 0) {
                $specModel = Spec::create([
                    'goods_type_id' => 0,
                    'name'          => $value['text'],
                    'spec_index'    => 0,
                ]);

                $isChange = true;
                $value['spec_id'] = $specModel->getAttr('spec_id');
            }

            foreach ($value['value'] as &$item) {
                if ($isChange || $item['spec_item_id'] <= 0) {
                    $specItemModel = SpecItem::create([
                        'spec_id'    => $value['spec_id'],
                        'item_name'  => $item['item_name'],
                        'is_contact' => 0,
                    ]);

                    $replace[$item['spec_item_id']] = $specItemModel->getAttr('spec_item_id');
                    $item['spec_item_id'] = $specItemModel->getAttr('spec_item_id');
                }
            }
        }

        // 释放上次循环的引用
        unset($value, $item);

        // 如果需要替换,开始将旧值换为新的值
        if (!empty($replace)) {
            if (!empty($data['goods_spec_item'])) {
                foreach ($data['goods_spec_item'] as &$value) {
                    if (is_string($value['key_name'])) {
                        $value['key_name'] = explode('_', $value['key_name']);
                    }

                    foreach ($value['key_name'] as $key => $item) {
                        if (array_key_exists($item, $replace)) {
                            $value['key_name'][$key] = $replace[$item];
                        }
                    }
                }
            }

            if (!empty($data['spec_image'])) {
                foreach ($data['spec_image'] as &$value) {
                    if (array_key_exists($value['spec_item_id'], $replace)) {
                        $value['spec_item_id'] = $replace[$value['spec_item_id']];
                    }
                }
            }
        }

        return $specMenu;
    }
}
