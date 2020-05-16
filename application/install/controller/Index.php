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
use think\Validate;
use think\Cache;
use think\Db;

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
            session('step', 2);
            $this->assign('next', '重新安装');
            $this->assign('nextUrl', url('step3'));
        } else {
            session('step', 1);
            $this->assign('next', '接 受');
            $this->assign('nextUrl', url('step2'));
        }

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

        // 环境检测
        $env = check_env();
        $this->assign('env', $env);

        // 目录文件读写检测
        $dirFile = check_dirfile();
        $this->assign('dirFile', $dirFile);

        // 函数检测
        $func = check_func();
        $this->assign('func', $func);

        // 是否可执行下一步
        $this->assign('isNext', false === session('error'));

        return $this->fetch();
    }

    /**
     * 步骤三，设置数据
     * @return mixed
     */
    public function step3()
    {
        if (session('step') != 2) {
            $this->redirect(url('/'));
        }

        session('step', 3);
        session('error', false);

        return $this->fetch();
    }

    /**
     * 步骤四，创建配置
     * @return mixed
     */
    public function step4()
    {
        // POST 用于验证
        if ($this->request->isPost()) {
            // 验证配置数据
            $rule = [
                'hostname'       => 'require',
                'database'       => 'require',
                'username'       => 'require',
                'password'       => 'require',
                'hostport'       => 'require|number',
                'prefix'         => 'require',
                'admin_user'     => 'require|length:4,20',
                'admin_password' => 'require|min:6|confirm',
                'is_cover'       => 'require|in:0,1',
                'is_demo'        => 'require|in:0,1',
                'is_region'      => 'require|in:0,1',
            ];

            $field = [
                'hostname'       => '数据库服务器',
                'database'       => '数据库名',
                'username'       => '数据库用户名',
                'password'       => '数据库密码',
                'hostport'       => '数据库端口',
                'prefix'         => '数据表前缀',
                'admin_user'     => '管理员账号',
                'admin_password' => '管理员密码',
                'is_cover'       => '覆盖同名数据库',
                'is_demo'        => '导入演示数据',
                'is_region'      => '区域数据',
            ];

            $data = $this->request->post();
            $validate = new Validate($rule, [], $field);

            if (false === $validate->check($data)) {
                $this->error($validate->getError());
            }

            // 缓存配置数据
            $data['type'] = 'mysql';
            session('installData', $data);

            try {
                // 创建数据库连接
                $dbInstance = Db::connect([
                    'type'     => $data['type'],
                    'hostname' => $data['hostname'],
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'hostport' => $data['hostport'],
                    'charset'  => 'utf8mb4',
                    'prefix'   => $data['prefix'],
                ]);

                // 检测数据库连接并检测版本
                $version = $dbInstance->query('select version() as version limit 1;');
                if (version_compare(reset($version)['version'], '5.5.3', '<')) {
                    throw new \Exception('数据库版本过低，必须 5.5.3 及以上');
                }

                // 检测是否已存在数据库
                if (!$data['is_cover']) {
                    $sql = 'SELECT * FROM information_schema.schemata WHERE schema_name=?';
                    $result = $dbInstance->execute($sql, [$data['database']]);

                    if ($result) {
                        throw new \Exception('数据库名已存在，请更换名称或选择覆盖');
                    }
                }

                // 创建数据库
                $sql = "CREATE DATABASE IF NOT EXISTS `{$data['database']}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
                if (!$dbInstance->execute($sql)) {
                    throw new \Exception($dbInstance->getError());
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $textType = mb_detect_encoding($error, ['UTF-8', 'GBK', 'LATIN1', 'BIG5']);

                if ($textType != 'UTF-8') {
                    $error = mb_convert_encoding($error, 'UTF-8', $textType);
                }

                $this->error($error);
            }

            // 准备工作完成
            $this->success('success', url('step4'));
        }

        if (session('step') != 3) {
            $this->redirect(url('/'));
        }

        session('step', 4);
        Cache::clear('install');

        return $this->fetch();
    }

    public function install()
    {
        if (session('step') != 4 || !$this->request->isAjax()) {
            $this->error('请按步骤安装');
        }

        // 数据准备
        $data = session('installData');
        $type = $this->request->post('type');
        $result = ['status' => 1, 'type' => $type];
        $path = APP_PATH . 'install' . DS . 'data' . DS;

        if (!$type) {
            $result['type'] = 'function';
            $this->success('开始安装数据库函数', url('install'), $result);
        }

        // 安装数据库函数
        if ('function' == $type) {
            try {
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
                if (!$mysqli->multi_query($sql)) {
                    throw new \Exception($mysqli->error);
                }

                $mysqli->close();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $result['type'] = 'database';
            $this->success('开始安装数据库表', url('install', 'idx=0'), $result);
        }

        // 连接数据库
        $db = null;
        if (in_array($type, ['database', 'region', 'config'])) {
            try {
                $db = Db::connect([
                    'type'     => $data['type'],
                    'hostname' => $data['hostname'],
                    'database' => $data['database'],
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'hostport' => $data['hostport'],
                    'charset'  => 'utf8mb4',
                    'prefix'   => $data['prefix'],
                ]);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }

        // 安装数据库表
        if ('database' == $type) {
            $database = Cache::remember('database', function () use ($data, $path) {
                $sql = file_get_contents($path . sprintf('careyshop%s.sql', $data['is_demo'] == 1 ? '_demo' : ''));
                $sql = macro_str_replace($sql, $data);
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);

                Cache::tag('install', 'database');
                return $sql;
            });

            // 数据库表安装完成
            $msg = '';
            $idx = $this->request->param('idx');

            if ($idx >= count($database)) {
                if ($data['is_region']) {
                    $result['type'] = 'region';
                    $this->success('开始安装精准区域', url('install', 'idx=0'), $result);
                } else {
                    $result['type'] = 'config';
                    $this->success('开始安装配置文件', url('install'), $result);
                }
            }

            // 插入数据库表
            if (array_key_exists($idx, $database)) {
                $sql = $value = trim($database[$idx]);

                if (!empty($value)) {
                    try {
                        if (false !== $db->execute($sql)) {
                            $msg = get_sql_message($sql);
                        } else {
                            throw new \Exception($db->getError());
                        }
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            }

            // 返回下一步
            $this->success($msg, url('install', 'idx=' . ($idx + 1)), $result);
        }

        // 安装精准区域
        if ('region' == $type) {
            $region = Cache::remember('region', function () use ($data, $path) {
                $sql = file_get_contents($path . 'cs_region_full.sql');
                $sql = macro_str_replace($sql, $data);
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);

                Cache::tag('install', 'region');
                return $sql;
            });

            // 精准区域安装完成
            $msg = '';
            $idx = $this->request->param('idx');

            if ($idx >= count($region)) {
                $result['type'] = 'config';
                $this->success('开始安装配置文件', url('install'), $result);
            }

            // 插入数据库表
            if (array_key_exists($idx, $region)) {
                $sql = $value = trim($region[$idx]);

                if (!empty($value)) {
                    try {
                        if (false !== $db->execute($sql)) {
                            $msg = get_sql_message($sql);
                        } else {
                            throw new \Exception($db->getError());
                        }
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            }

            // 返回下一步
            $this->success($msg, url('install', 'idx=' . ($idx + 1)), $result);
        }

        // 安装配置文件
        if ('config' == $type) {
            // 创建超级管理员
            $adminData = [
                'admin_id'    => 1,
                'username'    => $data['admin_user'],
                'password'    => user_md5($data['admin_password']),
                'group_id'    => AUTH_SUPER_ADMINISTRATOR,
                'nickname'    => 'CareyShop',
                'create_time' => '1530289832',
                'update_time' => time(),
            ];

            try {
                $db->name('admin')->insert($adminData);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            // 创建APP
            $appData = [
                'app_id'     => 1,
                'app_name'   => 'Admin(后台管理)',
                'app_key'    => rand_number(8),
                'app_secret' => rand_string(),
                'captcha'    => 1,
            ];

            try {
                $db->name('app')->insert($appData);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            // 创建数据库配置文件
            $fileConfig = file_get_contents($path . 'database.tpl');
            $fileConfig = macro_str_replace($fileConfig, $data);

            if (!file_put_contents(APP_PATH . 'database.php', $fileConfig)) {
                $this->error('数据库配置文件写入失败');
            }

            // 创建后台配置文件
            $fileAdmin = file_get_contents($path . 'production.tpl');
            $fileAdmin = macro_str_replace($fileAdmin, $appData);

            $pathPro = ROOT_PATH . 'public' . DS . 'static' . DS . 'admin' . DS . 'static' . DS . 'config';
            if (!file_put_contents($pathPro . DS . 'production.js', $fileAdmin)) {
                $this->error('后台配置文件写入失败');
            }

            $result['status'] = 0;
            $this->success('安装完成！', url('complete'), $result);
        }

        // 结束
        $this->error('异常结束，安装未完成');
    }

    /**
     * 完成安装
     * @return mixed
     */
    public function complete()
    {
        if (session('step') != 4) {
            $this->error('请按步骤安装系统', url('/'));
        }

        if (session('error')) {
            $this->error('安装出错，请重新安装！', url('/'));
        }

        // 安装锁定文件
        $lockPath = APP_PATH . 'install' . DS . 'data' . DS . 'install.lock';
        file_put_contents($lockPath, 'lock');

        session('step', null);
        session('error', null);
        session('installData', null);
        Cache::clear('install');

        return $this->fetch();
    }
}
