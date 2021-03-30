<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品属性配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class GoodsAttrConfig extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'goods_attr_config_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'goods_attr_config_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'goods_attr_config_id' => 'integer',
        'goods_id'             => 'integer',
        'config_data'          => 'array',
    ];

    /**
     * 新增或编辑指定的商品属性配置
     * @access public
     * @param number $goodsId    商品编号
     * @param array  $configData 属性配置数据
     * @throws
     */
    public static function updateAttrConfig($goodsId, array $configData)
    {
        $result = self::where('goods_id', '=', $goodsId)->find();
        if (is_null($result)) {
            self::create(['goods_id' => $goodsId, 'config_data' => $configData]);
        } else {
            $result->setAttr('config_data', $configData);
            $result->save();
        }
    }

    /**
     * 获取指定商品的属性配置数据
     * @access public
     * @param array $data 外部数据
     * @return array[]|false
     * @throws
     */
    public function getAttrConfigItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        $map[] = ['goods_id', '=', $data['goods_id']];
        $result = $this->where($map)->find();

        $resultData = ['attr_config' => [], 'attr_key' => []];
        if (is_null($result)) {
            return $resultData;
        }

        $resultData['attr_config'] = $result->getAttr('config_data');
        $resultData['attr_key'] = array_column($resultData['attr_config'], 'goods_attribute_id');

        return $resultData;
    }
}
