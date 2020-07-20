<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商城前台控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

declare (strict_types=1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;

class Index
{
    public function index()
    {
        $map = ['code' => 'open_api_rest', 'module' => 'system_info'];
        $showRest = Db::name('setting')->where($map)->value('value');

        View::assign('showRest', $showRest);
        return View::fetch();
    }
}
