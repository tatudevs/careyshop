<?php

namespace careyshop\captcha\facade;

use think\Facade;

/**
 * Class Captcha
 * @package careyshop\captcha\facade
 */
class Captcha extends Facade
{
    protected static function getFacadeClass()
    {
        return \careyshop\captcha\Captcha::class;
    }
}
