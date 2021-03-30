<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    安装向导公共文件
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/10
 */

/**
 * 生成URL
 * @param string $vars 参数
 * @param mixed  $idx  下步序号
 * @return string
 */
function get_url($vars = '', $idx = null): string
{
    $url = request()->baseFile();
    if (!empty($vars) && $vars != '/') {
        $url .= "?s=/index/{$vars}";
        !is_null($idx) && $url .= "/idx/{$idx}";
        $url .= '.html';
    }

    return $url;
}

/**
 * 系统环境检测
 * @return array
 */
function check_env(): array
{
    $items = [
        'os'     => ['操作系统', '不限制', '类Unix', PHP_OS, 'check'],
        'php'    => ['PHP版本', '7.4', '7.4+', PHP_VERSION, 'check'],
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
        $disk_size = floor(disk_free_space(public_path()) / (1024 * 1024));
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
 * @return string[][]
 */
function check_dirfile(): array
{
    $items = [
        ['dir', '可写', 'check', '../runtime'],
        ['file', '可写', 'check', '../.env'],
        ['dir', '可写', 'check', './static'],
        ['dir', '可写', 'check', './uploads'],
    ];

    foreach ($items as &$val) {
        $item = public_path() . $val[3];
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
 * @return string[][]
 */
function check_func(): array
{
    $items = [
        ['pdo', '支持', 'check', '类'],
        ['pdo_mysql', '支持', 'check', '模块'],
        ['openssl', '支持', 'check', '模块'],
        ['exif', '支持', 'check', '模块'],
        ['fileinfo', '支持', 'check', '模块'],
        ['curl', '支持', 'check', '模块'],
        ['bcmath', '支持', 'check', '模块'],
        ['mbstring', '支持', 'check', '模块'],
        ['scandir', '支持', 'check', '函数'],
        ['mysqli_connect', '支持', 'check', '函数'],
        ['file_get_contents', '支持', 'check', '函数'],
        ['version_compare', '支持', 'check', '函数'],
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

/**
 * 替换语句中的宏
 * @param string $sql  源SQL语句
 * @param array  $data 配置数据
 * @return string|string[]
 */
function macro_str_replace(string $sql, array $data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $sql = str_replace("{{$key}}", $value, $sql);
        }
    }

    return $sql;
}

/**
 * 获取SQL语句动作
 * @param string $sql SQL语句
 * @return string
 */
function get_sql_message(string $sql): string
{
    if (preg_match('/CREATE TABLE? `([^ ]*)`/is', $sql, $matches)) {
        return !empty($matches[1]) ? "创建数据库表 {$matches[1]} 完成" : '';
    }

    if (preg_match('/INSERT INTO? `([^ ]*)`/is', $sql, $matches)) {
        return !empty($matches[1]) ? "插入数据库行 {$matches[1]} 完成" : '';
    }

    if (preg_match('/ALTER TABLE? `([^ ]*)`/is', $sql, $matches)) {
        if (mb_strpos($sql, 'MODIFY')) {
            return !empty($matches[1]) ? "调整数据库索引 {$matches[1]} 完成" : '';
        } else {
            return !empty($matches[1]) ? "创建数据库索引 {$matches[1]} 完成" : '';
        }
    }

    return '';
}
