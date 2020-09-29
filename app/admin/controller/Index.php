<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商城后台控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;

class Index
{
    public function index()
    {
        return View::fetch();
    }
}
