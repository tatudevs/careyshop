<?php

namespace careyshop\facade;

use think\Facade;

/**
 * Class Captcha
 * @package careyshop\facade
 */
class Captcha extends Facade
{
    protected static function getFacadeClass()
    {
        return \careyshop\Captcha::class;
    }
}
