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
 * @return bool
 */
function captcha_check($value)
{
    return Captcha::check($value);
}
