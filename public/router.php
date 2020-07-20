<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    PHP自带WebServer支持
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

/**
 * 启动命令：php think run
 * 如有特殊指定：php think run -H tp.com -p 80
 */
if (is_file($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SCRIPT_NAME"])) {
    return false;
} else {
    $_SERVER["SCRIPT_FILENAME"] = __DIR__ . '/index.php';
    if (!isset($_SERVER['PATH_INFO'])) {
        $_SERVER['PATH_INFO'] = $_SERVER['SCRIPT_NAME'];
    }

    require __DIR__ . "/index.php";
}
