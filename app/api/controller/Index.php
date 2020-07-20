<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Api控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

declare (strict_types=1);

namespace app\api\controller;

use think\facade\Db;

class Index
{
    public function index()
    {
        $map = ['code' => 'open_api_rest', 'module' => 'system_info'];
        $isRest = Db::name('setting')->where($map)->value('value');

        $result = [
            'status' => 200,
            'data'   => '欢迎使用CareyShop商城框架系统 - Api',
        ];

        return $isRest || input('?get.key') ? view() : json($result);
    }
}
