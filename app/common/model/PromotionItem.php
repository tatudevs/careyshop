<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单促销方案模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\model;

class PromotionItem extends CareyShop
{
    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'promotion_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'quota'    => 'float',
        'settings' => 'array',
    ];

    /**
     * 添加促销方案
     * @access public
     * @param array $settings    促销方案配置
     * @param int   $promotionId 促销编号
     * @return array|false
     * @throws
     */
    public function addPromotionItem($settings, $promotionId)
    {
        // 处理外部填入数据并进行验证
        foreach ($settings as $key => $item) {
            if (!$this->validateData($settings[$key], 'add')) {
                return false;
            }

            foreach ($item['settings'] as $value) {
                if (!$this->validateData($value, 'settings')) {
                    return false;
                }
            }

            $settings[$key]['promotion_id'] = $promotionId;
        }

        return $this->saveAll($settings)->toArray();
    }
}
