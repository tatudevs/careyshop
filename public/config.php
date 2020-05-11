<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    全局配置文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/11/21
 */

// 定义额外的系统常量
define('APP_PUBLIC_PATH', '');
define('ADMIN_MODULE', 'admin');

/**
 * 检查是否安装
 * 在未安装完成之前,除"index"模块外,访问其他模块都会报错
 */
if (!is_file(APP_PATH . 'install/data/install.lock')) {
    define('BIND_MODULE', 'install');
}
