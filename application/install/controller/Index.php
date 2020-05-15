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
            $this->redirect($this->request->baseFile());
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
            $this->success('', $this->request->baseFile() . '?s=/index/step4.html');
        }

//        if (session('step') != 3 && !session('reinstall')) {
//            $this->redirect($this->request->baseFile());
//        }

        session('step', 4);
        return $this->fetch();
    }

    /**
     * @throws \think\Exception
     */
    public function install()
    {
        // 连接数据库
        $data = session('installData');
        $dbInstance = Db::connect([
            'type'     => $data['type'],
            'hostname' => $data['hostname'],
            'database' => $data['database'],
            'username' => $data['username'],
            'password' => $data['password'],
            'hostport' => $data['hostport'],
            'charset'  => 'utf8mb4',
            'prefix'   => $data['prefix'],
        ]);

        // 创建数据表
        create_data($dbInstance, $data);

        // 生成配置文件
        write_config($data);

        $this->redirect($this->request->baseFile() . '?s=/index/complete.html');
    }

    /**
     * 完成安装
     * @return mixed
     */
    public function complete()
    {
        if (session('step') != 4) {
            $this->error('请按步骤安装系统', $this->request->baseFile());
        }

        if (session('error')) {
            $this->error('安装出错，请重新安装！', $this->request->baseFile());
        }

        // 安装锁定文件
//        $lockPath = APP_PATH . 'install' . DS . 'data' . DS . 'install.lock';
//        file_put_contents($lockPath, 'lock');

        session('step', null);
        session('error', null);
        session('reinstall', null);
        session('installData', null);

        return $this->fetch();
    }
}
