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

define('INSTALL_APP_PATH', realpath('./') . '/');

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

    /**
     * 步骤二，检查环境
     * @return mixed
     */
    public function step2()
    {
        session('step', 2);
        session('error', false);

        if (session('reinstall')) {
            $this->redirect($this->request->baseFile() . '?s=/index/step4.html');
        }

        // 环境检测
        $env = check_env();
        $this->assign('env', $env);

//        dump($env);exit();

        // 目录文件读写检测
        $dirFile = check_dirfile();
        $this->assign('dirFile', $dirFile);

        // 函数检测
        $func = check_func();
        $this->assign('func', $func);

        return $this->fetch();
    }
}
