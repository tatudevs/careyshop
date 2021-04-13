<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格项模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class SpecItem extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'spec_item_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'spec_item_id',
        'spec_id',
    ];

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'spec_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'spec_item_id' => 'integer',
        'spec_id'      => 'integer',
        'is_contact'   => 'integer',
        'sort'         => 'integer',
    ];

    /**
     * 断开关联或更新商品规格项
     * @access public
     * @param int   $specId 商品规格Id
     * @param array $item   规格项列表
     * @return bool
     */
    public static function updateItem(int $specId, array $item): bool
    {
        // 去重规格项
        $item = array_unique($item);

        // 获取有关联的规格项列表
        $map[] = ['spec_id', '=', $specId];
        $map[] = ['is_contact', '=', 1];
        $result = self::order(['sort' => 'asc'])->where($map)->column('item_name', 'spec_item_id');

        // 取消关联项
        foreach ($result as $key => $value) {
            if (!in_array($value, $item)) {
                self::update(['is_contact' => 0], ['spec_item_id' => $key]);
                unset($result[$key]);
            }
        }

        foreach ($item as $key => $value) {
            $specItem = array_search($value, $result);
            if ($specItem) {
                // 更新排序值
                self::update(['sort' => $key], ['spec_item_id' => $specItem]);
            } else {
                // 写入新的项
                self::insert(['spec_id' => $specId, 'item_name' => $value, 'is_contact' => 1, 'sort' => $key]);
            }
        }

        return true;
    }
}
