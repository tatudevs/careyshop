<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/23
 */

namespace app\common\service;

use careyshop\facade\Captcha;
use think\facade\Route;

class App extends CareyShop
{
    /**
     * 获取应用验证码调用地址
     * @access public
     * @return array
     */
    public function getCaptchaCallurl(): array
    {
        $vars = ['method' => 'image.app.captcha'];
        $data['call_url'] = Route::buildUrl("api/{$this->version}/app", $vars)->domain(true)->build();

        return $data;
    }

    /**
     * 获取应用验证码
     * @access public
     * @return array
     */
    public function imageAppCaptcha(): array
    {
        $keyID = input('param.session_id', '');
        $generate = input('param.generate');

        // 获取验证码
        $config = ['mode' => 'api', 'cacheType' => 'cache'];
        $image = Captcha::create($config, $keyID);

        if ($generate == 'base64') {
            return [
                'content_type' => 'image/png',
                'base64'       => base64_encode($image),
            ];
        } else {
            $result = response($image, 200, ['Content-Length' => strlen($image)])
                ->contentType('image/png');

            return [
                'callback_return_type' => 'response',
                'is_callback'          => $result,
            ];
        }
    }

    /**
     * 验证应用验证码
     * @access public
     * @param string|null $code 验证码
     * @return mixed
     */
    public static function checkCaptcha(?string $code)
    {
        $keyID = input('param.session_id', '');
        return Captcha::check((string)$code, $keyID);
    }
}
