<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    安装向导公共文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/5/12
 */

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = [
        'os'     => ['操作系统', '不限制', '类Unix', PHP_OS, 'check'],
        'php'    => ['PHP版本', '5.6', '5.6+', PHP_VERSION, 'check'],
        'upload' => ['附件上传', '不限制', '2M+', '未知', 'check'],
        'gd'     => ['GD库', '2.0', '2.0+', '未知', 'check'],
        'disk'   => ['磁盘空间', '100M', '不限制', '未知', 'check'],
    ];

    // PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }

    // 附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    // GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : [];
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    // 磁盘空间检测
    if (function_exists('disk_free_space')) {
        $disk_size = floor(disk_free_space(INSTALL_APP_PATH) / (1024 * 1024));
        $items['disk'][3] = $disk_size . 'M';
        if ($disk_size < 100) {
            $items['disk'][4] = 'error';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = [
        ['dir', '可写', 'check', '../application'],
        ['dir', '可写', 'check', '../runtime'],
        ['dir', '可写', 'check', './static'],
        ['dir', '可写', 'check', './uploads'],
    ];

    foreach ($items as &$val) {
        $item = INSTALL_APP_PATH . $val[3];
        if ('dir' == $val[0]) {
            if (!is_writable($item)) {
                if (is_dir($item)) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }

    return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = [
        ['pdo', '支持', 'check', '类'],
        ['pdo_mysql', '支持', 'check', '模块'],
        ['openssl', '支持', 'check', '模块'],
        ['fileinfo', '支持', 'check', '模块'],
        ['curl', '支持', 'check', '模块'],
        ['file_get_contents', '支持', 'check', '函数'],
        ['mb_strlen', '支持', 'check', '函数'],
    ];

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0]))
            || ('模块' == $val[3] && !extension_loaded($val[0]))
            || ('函数' == $val[3] && !function_exists($val[0]))
        ) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}
