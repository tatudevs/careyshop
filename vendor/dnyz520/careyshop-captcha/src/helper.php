<?php

use careyshop\facade\Captcha;

/**
 * @param string|array|null $config
 * @param string|null       $key_id
 * @return mixed
 */
function captcha($config = null, $key_id = '')
{
    return Captcha::create($config, $key_id);
}

/**
 * @param string      $value
 * @param string|null $key_id
 * @return mixed
 */
function captcha_check($value, $key_id = '')
{
    return Captcha::check($value, $key_id);
}

/**
 * @param string $key
 * @return string
 */
function captcha_key($key)
{
    return Captcha::getKeyId($key);
}
