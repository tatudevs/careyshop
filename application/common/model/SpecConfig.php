<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/11/28
 */

namespace app\common\model;

class SpecConfig extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'spec_config_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'spec_config_id' => 'integer',
        'goods_id'       => 'integer',
        'config_data'    => 'array',
    ];

    /**
     * 新增或编辑指定的商品规格配置
     * @access public
     * @param  number $goodsId    商品编号
     * @param  array  $configData 属性配置数据
     * @throws
     */
    public static function updateSpecConfig($goodsId, $configData)
    {
        $result = self::where(['goods_id' => ['eq', $goodsId]])->find();
        if (is_null($result)) {
            self::create(['goods_id' => $goodsId, 'config_data' => $configData]);
        } else {
            $result->setAttr('config_data', $configData)->save();
        }
    }

    /**
     * 获取指定商品的规格配置数据
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecConfigItem($data)
    {
        if (!$this->validateData($data, 'SpecConfig')) {
            return false;
        }

        $result = self::get(function ($query) use ($data) {
            $query->where(['goods_id' => ['eq', $data['goods_id']]]);
        });

        if (false !== $result) {
            return is_null($result) ? null : $result->getAttr('config_data');
        }

        return false;
    }
}
