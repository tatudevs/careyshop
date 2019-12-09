<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/11/28
 */

namespace app\common\model;

class GoodsAttrConfig extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'goods_attr_config_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'goods_attr_config_id' => 'integer',
        'goods_id'             => 'integer',
        'config_data'          => 'array',
    ];

    /**
     * 新增或编辑指定的商品属性配置
     * @access public
     * @param  number $goodsId    商品编号
     * @param  array  $configData 属性配置数据
     * @throws
     */
    public static function updateAttrConfig($goodsId, $configData)
    {
        $result = self::where(['goods_id' => ['eq', $goodsId]])->find();
        if (is_null($result)) {
            self::create(['goods_id' => $goodsId, 'config_data' => $configData]);
        } else {
            $result->setAttr('config_data', $configData)->save();
        }
    }

    /**
     * 获取指定商品的属性配置数据
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAttrConfigItem($data)
    {
        if (!$this->validateData($data, 'GoodsAttrConfig')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $query->where(['goods_id' => ['eq', $data['goods_id']]]);
        });

        if (false !== $result) {
            $resultData = ['attr_config' => [], 'attr_key' => []];
            if (is_null($result)) {
                return $resultData;
            }

            $resultData['attr_config'] = $result->getAttr('config_data');
            $resultData['attr_key'] = array_column($resultData['attr_config'], 'goods_attribute_id');

            return $resultData;
        }

        return false;
    }
}
