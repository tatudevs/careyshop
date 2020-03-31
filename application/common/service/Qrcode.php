<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    二维码服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/1/26
 */

namespace app\common\service;

use think\Url;

class Qrcode extends CareyShop
{
    /**
     * 获取二维码调用地址
     * @access public
     * @return array
     */
    public function getQrcodeCallurl()
    {
        $vars = ['method' => 'get.qrcode.item'];
        $data['call_url'] = Url::bUild('/api/v1/qrcode', $vars, true, true);

        return $data;
    }

    /**
     * 判断本地资源或网络资源,最终将返回实际需要的路径
     * @access public
     * @param  string $path 路径
     * @return string
     */
    public static function getQrcodeLogoPath($path)
    {
        // 如果是网络文件直接返回
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return urldecode($path);
        }

        $path = ROOT_PATH . 'public' . DS . $path;
        $path = str_replace(IS_WIN ? '/' : '\\', DS, $path);

        if (is_file($path)) {
            return $path;
        }

        $path = ROOT_PATH . 'public' . DS . 'static' . DS . 'api' . DS . 'images' . DS . 'qrcode_logo.png';
        return $path;
    }
}
