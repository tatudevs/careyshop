<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    安装控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/5/11
 */

namespace app\install\controller;

use think\Controller;

class Index extends Controller
{
    /**
     * 安装首页
     * @return mixed
     */
    public function index()
    {
        if (is_file(APP_PATH . 'install' . DS . 'data' . DS . 'install.lock')) {
            $this->error('已安装，如需重新安装，请删除 install 模块 data 目录下的 install.lock 文件');
        }

        if (is_file(APP_PATH . 'database.php')) {
            session('reinstall', true);
            $this->assign('next', '重新安装');
        } else {
            session('reinstall', false);
            $this->assign('next', '接 受');
        }

        session('step', 1);
        session('error', false);
        return $this->fetch();
    }
}
