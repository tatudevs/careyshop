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
        $DS = DIRECTORY_SEPARATOR;
        $public = public_path();
        $file = $public . 'static' . $DS . 'api' . $DS . 'images' . $DS . 'qrcode_logo.png';

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            // 如果是网络资源
            if (false !== stripos(parse_url($path, PHP_URL_SCHEME), 'http')) {
                return urldecode($path);
            }
        } else {
            // 尝试查找本地资源
            $path = $public . $path;
            $path = str_replace('/', $DS, $path);

            if (is_file($path)) {
                return $path;
            }
        }

        return $file;
    }
}
