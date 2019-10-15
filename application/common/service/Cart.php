<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物车服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/1/26
 */

namespace app\common\service;

class Cart extends CareyShop
{
    /**
     * 验证购物车商品
     * @access public
     * @param  array $goodsData  商品数据(附带商品规格)
     * @param  bool  $isCheckout 结算调用(创建订单)
     * @param  bool  $isConcise  是否返回简洁数据
     * @return false|array
     */
    public function checkCartGoodsList($goodsData, $isCheckout, $isConcise = false)
    {
        // 此处并非不再检测是否限购,在添加至购物车时已进行过检测,
        // 只有在用户已添加至购物车,而管理组进行了调整才会出现,几率很小,但影响效率
        foreach ($goodsData as $key => $value) {
            // 补全需要的数据
            $goodsData[$key]['error'] = 0;
            $goodsData[$key]['error_msg'] = '';
            // TODO:订单商品的封面图得根据顾客购物时选择的规格进行,如果规格存在图片,则取规格的第一张图
            // TODO:如果规格不存在图,则取商品主图,否则直接留空
            // TODO:并且商品规格图允许多图集
            $goodsData[$key]['goods']['goods_image'] = isset($goodsData[$key]['goods']['attachment'][0]) ? $goodsData[$key]['goods']['attachment'][0] : '';
            unset($goodsData[$key]['goods']['attachment']);

            // 检测商品是否存在
            if (is_null($value['goods'])) {
                unset($goodsData[$key]);
                continue;
            }

            // 检测商品状态
            if ($value['goods']['store_qty'] <= 0 || $value['goods']['status'] != 1 || $value['goods']['is_delete'] != 0) {
                $goodsData[$key]['error'] = 1;
                $goodsData[$key]['error_msg'] = '商品已失效';
            }

            // 检测商品规格是否已选择
            if (!empty($value['goods_spec_item']) && empty($value['key_name'])) {
                $goodsData[$key]['error'] = 1;
                $goodsData[$key]['error_msg'] = '请选择商品规格';
            }

            // 检测商品规格与库存
            if (!empty($value['key_name'])) {
                $goodsSpec = array_column($value['goods_spec_item'], null, 'key_name');
                if (!array_key_exists($value['key_name'], $goodsSpec)) {
                    $goodsData[$key]['error'] = 1;
                    $goodsData[$key]['error_msg'] = '商品规格错误';
                } else {
                    $goodsData[$key]['goods']['store_qty'] = $goodsSpec[$value['key_name']]['store_qty'];
                    $goodsData[$key]['goods']['shop_price'] = $goodsSpec[$value['key_name']]['price'];
                    $goodsData[$key]['goods']['goods_sku'] = $goodsSpec[$value['key_name']]['goods_sku'];
                    $goodsData[$key]['goods']['bar_code'] = $goodsSpec[$value['key_name']]['bar_code'];
                }
            }

            if ($goodsData[$key]['goods_num'] <= 0 || $goodsData[$key]['goods_num'] > $goodsData[$key]['goods']['store_qty']) {
                $goodsData[$key]['error'] = 1;
                $goodsData[$key]['error_msg'] = '库存不足';
            }

            if ($isCheckout && $goodsData[$key]['error'] == 1) {
                $error = $value['goods']['name'] . '，';
                empty($value['key_value']) ?: $error .= $value['key_value'] . '，';
                $error .= $goodsData[$key]['error_msg'];

                return $this->setError($error);
            }

            if ($isConcise) {
                unset($goodsData[$key]['is_selected']);
                unset($goodsData[$key]['update_time']);
                unset($goodsData[$key]['goods']['status']);
                unset($goodsData[$key]['goods']['is_delete']);
                unset($goodsData[$key]['goods_spec_item']);
            }
        }


        return $goodsData;
    }
}
