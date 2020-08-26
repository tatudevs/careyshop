<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格配置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/4
 */

namespace app\common\model;

class SpecConfig extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'spec_config_id';

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
     * hasMany cs_spec_goods
     * @access public
     * @return mixed
     */
    public function specCombo()
    {
        return $this->hasMany(SpecGoods::class, 'goods_id', 'goods_id');
    }

    /**
     * 新增或编辑指定的商品规格配置
     * @access public
     * @param number $goodsId    商品编号
     * @param array  $configData 属性配置数据
     * @throws
     */
    public static function updateSpecConfig($goodsId, $configData)
    {
        $result = self::where(['goods_id' => $goodsId])->find();
        if (is_null($result)) {
            self::create(['goods_id' => $goodsId, 'config_data' => $configData]);
        } else {
            $result->setAttr('config_data', $configData);
            $result->save();
        }
    }

    /**
     * 获取指定商品的规格配置数据
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getSpecConfigItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        $map[] = ['goods_id', '=', $data['goods_id']];
        $result = $this->with('spec_combo')->where($map)->find();

        $resultData = ['spec_config' => [], 'spec_combo' => [], 'spec_key' => []];
        if (is_null($result)) {
            return $resultData;
        }

        $resultData['spec_config'] = $result->getAttr('config_data');
        $resultData['spec_combo'] = $result->getAttr('spec_combo');
        $resultData['spec_key'] = array_column($resultData['spec_config'], 'spec_id');

        if (!empty($data['key_to_array'])) {
            foreach ($resultData['spec_combo'] as &$value) {
                $value['key_name'] = explode('_', $value['key_name']);
            }

            unset($value);
        }

        return $resultData;
    }
}
