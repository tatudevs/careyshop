<?php
declare (strict_types = 1);

namespace careyshop\facade;

use think\Facade;

/**
 * Class Captcha
 * @package careyshop\facade
 * @mixin \careyshop\Captcha
 * @method static mixed create(string $config = null, bool $api = false) 输出验证码
 * @method static bool check(string $code, bool $api) 验证验证码是否正确
 */
class Captcha extends Facade
{
    protected static function getFacadeClass()
    {
        return \careyshop\Captcha::class;
    }
}
