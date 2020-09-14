<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    安装控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/11
 */

declare (strict_types=1);

namespace app\install\controller;

use think\facade\Cache;
use think\facade\Db;
use think\facade\Session;
use think\facade\Validate;
use think\facade\View;

class Index
{
    // 引入Jump
    use \careyshop\Jump;

    /**
     * 安装首页
     * @return mixed
     */
    public function index()
    {
        // 获取运行目录
        $appPath = app_path();

        if (is_file($appPath . 'data' . DIRECTORY_SEPARATOR . 'install.lock')) {
            $this->error('已安装，如需重新安装，请删除 install 模块 data 目录下的 install.lock 文件');
        }

        if (is_file(root_path() . '.env')) {
            Session::set('step', 2);
            View::assign('next', '重新安装');
            View::assign('nextUrl', get_url('step3'));
        } else {
            Session::set('step', 1);
            View::assign('next', '接 受');
            View::assign('nextUrl', get_url('step2'));
        }

        Session::set('error', false);
        return View::fetch();
    }

    /**
     * 步骤二，检查环境
     * @return mixed
     */
    public function step2()
    {
        Session::set('step', 2);
        Session::set('error', false);

        // 环境检测
        $env = check_env();
        View::assign('env', $env);

        // 目录文件读写检测
        $dirFile = check_dirfile();
        View::assign('dirFile', $dirFile);

        // 函数检测
        $func = check_func();
        View::assign('func', $func);

        // 是否可执行下一步
        View::assign('isNext', !Session::get('error'));

        return View::fetch();
    }

    /**
     * 步骤三，设置数据
     * @return mixed
     */
    public function step3()
    {
        if (Session::get('step') != 2) {
            $this->redirect(get_url());
        }

        Session::set('step', 3);
        Session::set('error', false);

        View::assign('apiBase', url('/api', [], false, true)->build());
        return View::fetch();
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
                'hostname|数据库服务器'      => 'require',
                'database|数据库名'        => 'require',
                'username|数据库用户名'      => 'require',
                'password|数据库密码'       => 'require',
                'hostport|数据库端口'       => 'require|number',
                'prefix|数据表前缀'         => 'require',
                'admin_user|管理员账号'     => 'require|length:4,20',
                'admin_password|管理员密码' => 'require|min:6|confirm',
                'base_api|API接口路径'     => 'require',
                'is_cover|覆盖同名数据库'     => 'require|in:0,1',
                'is_demo|导入演示数据'       => 'require|in:0,1',
            ];

            $validate = Validate::rule($rule);
            $data = $this->request->post();

            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            // 缓存配置数据
            $data['type'] = 'mysql';
            Session::set('installData', $data);

            // 数据库检测
            try {
                // 连接数据库
                $mysqli = @mysqli_connect($data['hostname'], $data['username'], $data['password'], '', (int)$data['hostport']);
                if (!$mysqli) {
                    throw new \Exception(mysqli_connect_error());
                }

                // 设置编码
                mysqli_set_charset($mysqli, 'utf8mb4');
                $version = mysqli_get_server_info($mysqli);

                // 检测数据库版本号
                if (version_compare($version, '5.5.3', '<')) {
                    throw new \Exception('数据库版本过低，必须 5.5.3 及以上');
                }

                // 检测是否已存在数据库
                if (!$data['is_cover'] && mysqli_select_db($mysqli, $data['database'])) {
                    throw new \Exception('数据库名已存在，请更换名称或选择覆盖');
                }

                // 创建数据库
                if (!mysqli_query($mysqli, "CREATE DATABASE IF NOT EXISTS `{$data['database']}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")) {
                    throw new \Exception(mysqli_error($mysqli));
                }

                // 断开数据库
                $mysqli->close();

                // 创建配置文件
                $dataPath = app_path() . 'data' . DIRECTORY_SEPARATOR;
                $envFile = file_get_contents($dataPath . 'env.tpl');
                $envFile = macro_str_replace($envFile, $data);

                if (!file_put_contents(root_path() . '.env', $envFile)) {
                    $this->error('配置文件写入失败');
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
            $this->success('success', get_url('step4'));
        }

        if (Session::get('step') != 3) {
            $this->redirect(get_url());
        }

        Session::set('step', 4);
        Cache::tag('install')->clear();

        return View::fetch();
    }

    /**
     * 数据导入
     * @throws \Throwable
     */
    public function install()
    {
        if (Session::get('step') != 4 || !$this->request->isAjax()) {
            $this->error('请按步骤安装');
        }

        // 数据准备
        $data = Session::get('installData');
        $type = $this->request->post('type');
        $result = ['status' => 1, 'type' => $type];
        $dataPath = app_path() . 'data' . DIRECTORY_SEPARATOR;

        if (!$type) {
            $result['type'] = 'function';
            $this->success('开始安装数据库函数', get_url('install'), $result);
        }

        // 安装数据库函数
        if ('function' == $type) {
            try {
                $sql = file_get_contents($dataPath . 'function_sql.tpl');
                $sql = macro_str_replace($sql, $data);

                $mysqli = mysqli_connect($data['hostname'], $data['username'], $data['password'], $data['database'], (int)$data['hostport']);
                $mysqli->set_charset('utf8mb4');

                if (!$mysqli->multi_query($sql)) {
                    throw new \Exception(mysqli_error($mysqli));
                }

                $mysqli->close();
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            $result['type'] = 'database';
            $this->success('开始安装数据库表', get_url('install', 0), $result);
        }

        // 安装数据库表
        if ('database' == $type) {
            $database = Cache::remember('database', function () use ($data, $dataPath) {
                $sql = file_get_contents($dataPath . sprintf('careyshop%s.tpl', $data['is_demo'] == 1 ? '_demo' : ''));
                $sql = macro_str_replace($sql, $data);
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);

                Cache::tag('install')->append('database');
                return $sql;
            });

            // 数据库表安装完成
            $msg = '';
            $idx = $this->request->param('idx');

            if ($idx >= count($database)) {
                $result['type'] = 'config';
                $this->success('开始安装配置文件', get_url('install'), $result);
            }

            // 插入数据库表
            if (array_key_exists($idx, $database)) {
                $sql = $value = trim($database[$idx]);

                if (!empty($value)) {
                    try {
                        if (false !== Db::execute($sql)) {
                            $msg = get_sql_message($sql);
                        }
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            }

            // 返回下一步
            $this->success($msg, get_url('install', $idx + 1), $result);
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
                Db::name('admin')->insert($adminData);
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
                Db::name('app')->insert($appData);
                $appData['base_api'] = $data['base_api'];
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }

            // 创建后台配置文件
            $fileAdmin = file_get_contents($dataPath . 'production.tpl');
            $fileAdmin = macro_str_replace($fileAdmin, $appData);

            $pathPro = public_path() . 'static' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'config';
            if (!file_put_contents($pathPro . DIRECTORY_SEPARATOR . 'production.js', $fileAdmin)) {
                $this->error('后台配置文件写入失败');
            }

            $result['status'] = 0;
            $adminData['password'] = $data['admin_password'];

            $baseData = [
                'admin' => $adminData,
                'app'   => $appData,
            ];

            Session::set('system_data', $baseData);
            $this->success('安装完成！', get_url('complete'), $result);
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
        if (Session::get('step') != 4) {
            $this->error('请按步骤安装系统', get_url());
        }

        if (Session::get('error')) {
            $this->error('安装出错，请重新安装！', get_url());
        }

        // 安装锁定文件
        $lockPath = app_path() . 'data' . DIRECTORY_SEPARATOR . 'install.lock';
        file_put_contents($lockPath, 'lock');

        // 清理缓存资源(Cache::clear()其实可以不写,clear命令同样清理缓存)
        // 但防止系统不支持"shell_exec"还是需要单独清理
        Cache::clear();

        // 获取系统生成数据
        $data = Session::get('system_data', []);

        // 清理Session
        Session::clear();

        if (!ini_get('safe_mode') && function_exists('shell_exec')) {
            shell_exec(sprintf('php "%s" %s', root_path() . 'think', 'clear'));
        }

        View::assign('system_data', $data);
        return View::fetch();
    }
}
