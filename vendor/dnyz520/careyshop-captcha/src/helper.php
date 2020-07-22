<?php

use think\Response;
use careyshop\facade\Captcha;

/**
 * @param string $config
 * @param bool   $api
 * @return Response
 */
function captcha($config = null, $api = false): Response
{
    return Captcha::create($config, $api);
}

/**
 * @param string $value
 * @param bool   $api
 * @return bool
 */
function captcha_check($value, $api = false)
{
    return Captcha::check($value);
}
