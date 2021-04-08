<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    配送轨迹服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\service;

use think\facade\Route;
use think\helper\Str;

class DeliveryDist extends CareyShop
{
    /**
     * 生成快递鸟签名
     * @access public
     * @param string $data 请求内容
     * @return string
     */
    public static function getCallbackSign(string $data): string
    {
        return urlencode(base64_encode(md5($data . config('careyshop.delivery_dist.api_key', ''))));
    }

    /**
     * 获取配送回调URL接口
     * @access public
     * @return array
     */
    public function getDistCallback(): array
    {
        $vars = ['method' => 'put.delivery.dist.data'];
        $callbackUrl = Route::buildUrl("api/$this->version/delivery_dist", $vars)->domain(true)->build();

        return ['callback_url' => $callbackUrl];
    }

    /**
     * 将数组键名驼峰转下划线
     * @access public
     * @param array $data 数据
     * @return array
     */
    public static function snake(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        foreach ($data as $itemKey => $item) {
            foreach ($item as $valueKey => $value) {
                $data[$itemKey][Str::snake($valueKey)] = $value;
                unset($data[$itemKey][$valueKey]);
            }
        }

        return $data;
    }
}
