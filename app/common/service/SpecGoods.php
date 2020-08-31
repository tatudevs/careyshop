<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格列表服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/1
 */

namespace app\common\service;

use app\common\model\Spec;
use app\common\model\SpecImage;
use app\common\model\SpecItem;

class SpecGoods extends CareyShop
{
    /**
     * 将商品规格项还原成菜单结构数据
     * @access public
     * @param array $data    待处理数据
     * @param null  $goodsId 商品编号
     * @return array
     * @throws
     */
    public static function specItemToMenu(array $data, $goodsId = null)
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

        $map = [['spec_item_id', 'in', $keyList]];
        $specItemResult = SpecItem::where($map)->column('spec_id,item_name', 'spec_item_id');

        $idList = array_column($specItemResult, 'spec_id');
        if (empty($idList)) {
            return [];
        }

        $idList = array_unique($idList);
        $map = [['spec_id', 'in', $idList]];
        $specResult = Spec::where($map)->column('name,spec_type', 'spec_id');

        if (empty($specResult)) {
            return [];
        }

        // 如果存在图片或色彩数据
        $imageList = [];
        if ($goodsId) {
            $imageResult = SpecImage::where('goods_id', '=', $goodsId)->select();
            $imageList = array_column($imageResult->toArray(), null, 'spec_item_id');
        }

        $getImage = function ($specItemId, $type) use ($imageList) {
            if (0 != $type && !empty($imageList)) {
                if (array_key_exists($specItemId, $imageList)) {
                    if ($imageList[$specItemId]['spec_type'] == $type) {
                        return $imageList[$specItemId][$type == 1 ? 'image' : 'color'];
                    }
                }
            }

            return $type == 1 ? [] : '';
        };

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

            // 项的主体不存在时创建一次
            $key = array_search($specId, $sort);
            $specType = $specResult[$specId]['spec_type'];

            if (false === $key) {
                $sort[] = $specId;
                $result[] = [
                    'spec_id'   => $specId,
                    'name'      => $specResult[$specId]['name'],
                    'spec_type' => $specType,
                    'spec_item' => [],
                ];

                $key = count($sort) - 1;
            }

            // 将项压入到主体中
            unset($specItemResult[$value]['spec_id']);
            $specItemResult[$value]['image'] = $getImage($value, 0 != $specType ? 1 : 0);
            $specItemResult[$value]['color'] = $getImage($value, 0 != $specType ? 2 : 0);
            $result[$key]['spec_item'][] = $specItemResult[$value];
        }

        return $result;
    }

    /**
     * 检测规格菜单是否存在自定义,并且替换原始数据
     * @access public
     * @param array $data 外部数据
     */
    public static function validateSpecMenu(array &$data)
    {
        if (empty($data['spec_config'])) {
            return;
        }

        // 待替换内容 key=查找内容 value=替换为
        $replace = [];

        // 待从配置数据中提取图集
        $data['spec_image'] = [];

        foreach ($data['spec_config'] as &$value) {
            // 判断主体是否有变更,如果主体变更,则项值无条件重新生成
            $isChange = false;

            // 检测是否需要添加规格主体
            if ($value['spec_id'] <= 0) {
                $specModel = Spec::create([
                    'goods_type_id' => 0,
                    'name'          => $value['name'],
                    'spec_index'    => 0,
                    'spec_type'     => $value['spec_type'],
                ]);

                $isChange = true;
                $value['spec_id'] = $specModel->getAttr('spec_id');
            }

            // 处理规格列表
            foreach ($value['spec_item'] as &$item) {
                if ($isChange || $item['spec_item_id'] <= 0) {
                    $specItemModel = SpecItem::create([
                        'spec_id'    => $value['spec_id'],
                        'item_name'  => $item['item_name'],
                        'is_contact' => 0,
                    ]);

                    // spec_item_id: key=旧值 value=新值
                    $replace[$item['spec_item_id']] = $specItemModel->getAttr('spec_item_id');
                    $item['spec_item_id'] = $replace[$item['spec_item_id']];
                }

                // 提取图集与颜色 0=文字
                if (0 == $value['spec_type']) {
                    continue;
                }

                if (!empty($item['image']) || !empty($item['color'])) {
                    $data['spec_image'][] = [
                        'spec_item_id' => $item['spec_item_id'],
                        'spec_type'    => $value['spec_type'],
                        'image'        => $item['image'],
                        'color'        => $item['color'],
                    ];
                }
            }

            // 处理选中项
            foreach ($value['check_list'] as &$check) {
                if (array_key_exists($check, $replace)) {
                    $check = $replace[$check];
                }
            }

            unset($item, $check);
        }

        unset($value);

        // 如果需要替换,则将规格组合中的编号进行更新
        if (!empty($replace) && !empty($data['spec_combo'])) {
            foreach ($data['spec_combo'] as &$value) {
                if (is_string($value['key_name'])) {
                    $value['key_name'] = explode('_', $value['key_name']);
                }

                foreach ($value['key_name'] as $key => $item) {
                    if (array_key_exists($item, $replace)) {
                        $value['key_name'][$key] = $replace[$item];
                    }
                }
            }

            unset($value);
        }
    }
}
