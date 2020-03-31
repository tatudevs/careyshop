<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/2/28
 */

namespace app\common\service;

use captcha\Captcha;
use think\Url;
use think\Request;

class App extends CareyShop
{
    /**
     * 获取应用验证码调用地址
     * @access public
     * @return mixed
     */
    public function getCaptchaCallurl()
    {
        $vars = ['method' => 'image.app.captcha'];
        $data['call_url'] = Url::bUild('/api/v1/app', $vars, true, true);

        return $data;
    }

    /**
     * 获取应用验证码
     * @access public
     * @return mixed
     */
    public function imageAppCaptcha()
    {
        $config = [
            'length'   => 4,
            'useCurve' => false,
            'fontttf'  => '1.ttf',
            'codeSet'  => '02345689',
            'bg'       => [255, 255, 255],
        ];

        $captcha = new Captcha($config);
        $id = Request::instance()->param('session_id');

        $data['callback_return_type'] = 'response';
        $data['is_callback'] = $captcha->entry($id);

        return $data;
    }

    /**
     * 验证应用验证码
     * @access public
     * @param string $code 验证码
     * @return bool
     */
    public static function checkCaptcha($code)
    {
        $captcha = new Captcha();
        $id = Request::instance()->param('session_id');

        $result = $captcha->check($code, $id);
        return $result === false ? $captcha->getError() : true;
    }
}
