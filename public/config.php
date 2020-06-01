<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    全局配置文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/11/21
 */

// 公共文件夹目录,index.php目录更换后需要改动
define('APP_PUBLIC_PATH', '');

// 管理模块入口
define('ADMIN_MODULE', 'admin');

/**
 * 检查是否安装
 * 在未安装完成之前,除"index"模块外,访问其他模块都会报错
 */
$path = APP_PATH . 'install/data';
if (file_exists($path) && !is_file($path . '/install.lock')) {
    define('BIND_MODULE', 'install');
}
