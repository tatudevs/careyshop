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
        ['bcmath', '支持', 'check', '模块'],
        ['mbstring', '支持', 'check', '模块'],
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
 * @return mixed
 */
function macro_str_replace($sql, $data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $sql = str_replace("{{$key}}", $value, $sql);
        }
    }

    return $sql;
}

/**
 * 创建数据表
 * @param Object $db   数据库实列
 * @param array  $data 配置数据
 */
function create_data($db, $data)
{
    // 各类资源路径
    $path = APP_PATH . 'install' . DS . 'data' . DS;

    // 创建数据库函数
    $sql = file_get_contents($path . 'careyshop_function.tpl');
    $sql = macro_str_replace($sql, $data);

    $mysqli = mysqli_connect(
        $data['hostname'],
        $data['username'],
        $data['password'],
        $data['database'],
        $data['hostport']
    );

    $mysqli->set_charset('utf8mb4');

    if ($mysqli->multi_query($sql)) {
        insert_log('创建数据库函数完成');
    } else {
        insert_log('创建数据库函数失败', true);
        session('error', true);
    }

    $mysqli->close();

    // 创建数据库表
    $sql = file_get_contents($path . sprintf('careyshop%s.sql', $data['is_demo'] == 1 ? '_demo' : ''));
    $sql = macro_str_replace($sql, $data);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        if (false !== $db->execute($value)) {
            insert_log('创建数据库表完成');
        } else {
            insert_log('创建数据库表失败', true);
            session('error', true);
        }

        sleep(1);
    }
}

/**
 * 生成配置文件
 * @param array $data 配置数据
 */
function write_config($data)
{
}

/**
 * 发送日志
 * @param string $msg   信息
 * @param bool   $error 是否错误
 */
function insert_log($msg, $error = false)
{
    $html = sprintf('<li><i class="%s"/>%s<span style="float: right;">%s</span></li>',
        $error ? 'icon_error' : 'icon_check', $msg, date('m-d H:i:s'));

    echo "<script type=\"text/javascript\">insert_log('{$html}')</script>";
    flush();
    ob_flush();
}
