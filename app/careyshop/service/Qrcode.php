<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    二维码服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\service;

use think\facade\Route;

class Qrcode extends CareyShop
{
    /**
     * 获取二维码调用地址
     * @access public
     * @return array
     */
    public function getQrcodeCallurl(): array
    {
        $vars = ['method' => 'get.qrcode.item'];
        $data['call_url'] = Route::buildUrl("api/$this->version/qrcode", $vars)->domain(true)->build();

        return $data;
    }

    /**
     * 判断本地资源或网络资源,最终将返回实际需要的路径
     * @access public
     * @param string $path 路径
     * @return string
     */
    public static function getQrcodeLogoPath(string $path): string
    {
        // 如果是网络文件直接返回
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return urldecode($path);
        }

        $public = public_path();
        $DS = DIRECTORY_SEPARATOR;

        $path = $public . $path;
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        if (is_file($path)) {
            return $path;
        }

        return $public . 'static' . $DS . 'api' . $DS . 'images' . $DS . 'qrcode_logo.png';
    }
}
