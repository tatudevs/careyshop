<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    自定义接口版本号v2演示
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/10
 */

namespace app\api\controller\v2;

use app\common\service\Index as IndexService;

class Index extends \app\api\controller\v1\Index
{
    /**
     * 方法路由器
     * 该代码片段演示了新增与修改共同存在时的处理方式,如不需要可删除此文件
     * 如果只是简单的修改或只是新增额外自定义接口,请参考 https://doc.careyshop.cn/guide/version/
     *
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        // 从父类中获取了方法路由,此时就可以进行新增或修改
        $parent = parent::initMethod();

        // 父方法路由键名不存在"get.system.v2"方法,表示新增一个接口
        $parent['get.system.v2'] = ['getMethodV2', false];

        // 父方法路由键名存在"get.system.version"方法,表示修改一个接口
        $parent['get.system.version'] = ['getVersion', false];

        // 删除从父方法路由继承过来的"set.system.optimize"方法
        unset($parent['set.system.optimize']);

        /**
         * 至此"Index"控制器下的最终方法结果:
         * "get.index.host"与"clear.cache.all" v1、v2同时存在方法,且结果相同
         * "set.system.optimize" v1可请求,v2不可请求
         * "get.system.version" v1、v2都可请求,但返回内容不同
         * "get.system.v2" v1不可请求,v2可请求
         * 至于方法路由中的键值含义请参考 https://doc.careyshop.cn/guide/controller/
         * 最后,只需要将"$parent"变量返回即可
         */

        return $parent;
    }

    /**
     * 假设请求条件同为 "method": "get.system.v2"时,
     * 访问 http://{{host}}/v1/index 会提示错误,原因是v1版本下不存在此接口
     * 访问 http://{{host}}/v2/index 会正确返回执行结果
     *
     * @access protected
     * @return string
     */
    protected function getMethodV2()
    {
        return '基于CareyShop商城框架系统,新增v2接口数据返回';
    }

    /**
     * 假设请求条件同为 "method": "get.system.version"时,
     * 访问 http://{{host}}/v1/index 返回:
     * {"status":200,"message":"success","data":{"version":"1.3.0"}}
     * 访问 http://{{host}}/v2/index 返回:
     * {"status":200,"message":"success","data":{"version":"1.3.0","v2":"基于CareyShop商城框架系统,修改为v2接口后数据返回"}}
     *
     * @access protected
     * @return array
     */
    protected function getVersion()
    {
        $indexService = new IndexService();
        $data = $indexService->getVersion();
        $data['v2'] = '基于CareyShop商城框架系统,修改为v2接口后数据返回';

        return $data;
    }
}
