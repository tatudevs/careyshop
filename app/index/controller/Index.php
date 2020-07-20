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
        $status = Db::name('setting')->where(['code' => 'open_api_rest', 'module' => 'system_info'])->value('value');

        View::assign('status', $status);
        return View::fetch();
    }
}
