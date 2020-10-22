<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 配置类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/19
 */

namespace app\common\wechat;

use app\common\model\MiniService;
use EasyWeChat\MiniProgram\Application as MiniProgram;
use EasyWeChat\OfficialAccount\Application as OfficialAccount;
use EasyWeChat\OpenPlatform\Application as OpenPlatform;
use EasyWeChat\Payment\Application as Payment;
use EasyWeChat\Work\Application as Work;
use EasyWeChat\OpenWork\Application as OpenWork;
use EasyWeChat\MicroMerchant\Application as MicroMerchant;
use think\facade\Cache;

class WeChat
{
    /**
     * 默认配置
     * @var array
     */
    protected $setting = [
        /**
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         * 使用自定义类名时，构造函数将会接收一个 `EasyWeChat\Kernel\Http\Response` 实例
         */
        'response_type' => 'array',

        /**
         * 日志配置
         *
         * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log'           => [
            'default'  => 'dev',
            'channels' => [
                // 测试环境
                'dev'  => [
                    'driver' => 'single',
                    'path'   => '../runtime/log/wechat.log',
                    'level'  => 'debug',
                ],
                // 生产环境
                'prod' => [
                    'driver' => 'daily',
                    'path'   => '../runtime/log/wechat.log',
                    'level'  => 'info',
                ],
            ],
        ],
    ];

    /**
     * 实例化
     * @var null
     */
    protected $app = null;

    /**
     * 对应模块
     * @var string[]
     */
    private $models = [
        'official_account' => OfficialAccount::class,
        'work'             => Work::class,
        'mini_program'     => MiniProgram::class,
        'payment'          => Payment::class,
        'open_platform'    => OpenPlatform::class,
        'open_work'        => OpenWork::class,
        'micro_merchant'   => MicroMerchant::class,
    ];

    /**
     * 构造函数
     * @access public
     * @param string $code 微服务编码
     * @throws
     */
    public function __construct(string $code)
    {
        // 从数据库获取配置
        $setting = Cache::remember($code, function () use ($code) {
            $map = [
                ['code', '=', $code],
                ['platform', '=', 'wechat'],
                ['status', '=', 1],
            ];

            // 数据不存在时会抛出异常
            $result = MiniService::where($map)->findOrFail();
            Cache::tag('MiniService')->append($code);

            return $result->toArray();
        });

        // 修改部分配置参数
        $channel = env('app_debug', true) ? 'dev' : 'prod';
        $path = runtime_path() . sprintf('wechat/%s.log', date('d'));

        $this->setting['log']['default'] = $channel;
        $this->setting['log']['channels'][$channel]['path'] = $path;

        // 合并配置参数
        foreach ($setting['setting'] as $key => $value) {
            $this->setting[$key] = $value['value'];
        }

        // 实例化对应模块
        if (!array_key_exists($setting['model'], $this->models)) {
            throw new \Exception("model {$setting['model']} is does not exist");
        }

        $this->app = new $this->models[$setting['model']]($this->setting);
        $this->app->rebind('cache', app(\app\common\wechat\Cache::class));
    }

    /**
     * 获取 WeChat 实列
     * @access public
     * @return mixed|null
     */
    public function getApp()
    {
        return $this->app;
    }
}
