<p align="center">
    <img src="https://aliyun.oss.careyshop.cn/poster/banner.png"  alt="careyshop_banner"/>
</p>

<h1 align="center">CareyShop 商城框架系统</h1>

<p align="center">
    <b>如果对您有帮助，请帮忙点右上角 "Star" 支持一下 谢谢！</b>
</p>

## 介绍
CareyShop（简称 CS）是一套基于 ThinkPHP6 框架开发的高性能开源商城框架系统，秉承极简、极速、极致的开发理念，采用前后端分离，支持分布式部署。框架内部使用面向对象模块化调用，在多终端、跨平台时采用 REST API 进行数据交互，可直接对接 PC、移动设备、小程序、云部署，构建 Android、IOS 的 APP。

框架引入渠道概念，配合管理后台您可以动态配置各大平台至“渠道”中进行管理，比如“微信公众号”、“微信小程序”、“头条小程序”等，并且支持店群（绑定多个运营账号）运营模式。

**严正声明：**   
**为配合国家打击跨境赌博、电信诈骗、断卡等行动，本源码仅限中国大陆地区使用，且！使用站点必须有ICP备案与网安备案，否则一律上报至公安机关！**

**CareyShop（QQ）交流1群：714593455 交流2群：817814861**

## 前言
放眼移动热潮、新零售概念、各类<abbr title="泛指各类终端机器或各类平台，终端机有IOS与Android为代表，平台以“微信小程序”为代表">终端</abbr>的增多，服务端会与各类前端进行交互，和第三方相互协作也更加紧密、频繁。出于这样的整体环境，我们摒弃传统开发方向，直接以 REST API 架构为基础，将各类业务处理层模块化。

框架所有的业务处理逻辑存放在公共模块目录中，对于框架内的其他模块可通过面向对象的方式调用公共模块，而外部则以 API 接口进行交互，如此真正做到业务处理层的入口路径统一。具体开发使用请参见[「CareyShop 完全开发手册」](https://doc.careyshop.cn/)

## 预览
[后台 Demo 预览](https://demo.careyshop.cn/admin "后台 Demo 预览") | [RestAPI 接口调试](https://demo.careyshop.cn/api "RestAPI 接口调试") | [经典配套款](https://doc.careyshop.cn/guide/white/) | [深灰商务款](https://doc.careyshop.cn/guide/gray/)

## 文档
[CareyShop开发文档](https://doc.careyshop.cn "CareyShop开发文档")

## 功能
* 发票申请、审批管理（1.3.2）
* OAuth2.0支持微信、QQ、抖音、淘宝等（1.3.2）
* 通知订阅（1.3.2）
* 渠道系统，支持微信公众号等（1.3.2）
* 数据统计
* 重构至ThinkPHP6.0
* REST API内置调试工具
* 商品管理
* 商品分类
* 商品品牌
* 商品模型
* 商品规格
* 商品属性
* 商品评价
* 内置商品预览
* 商品咨询
* 购物车
* 我的足迹
* 我的收藏夹
* 订单管理
* 订单详情
* 订单导出
* 订单打印
* 订单退款
* 退款日志
* 售后管理
* 售后详情
* 满额包邮
* 商品折扣
* 订单促销
* 优惠劵管理
* 优惠劵发放
* 购物卡(充值卡)管理
* 购物卡(充值卡)使用管理
* 会员账户
* 会员账户资金(可充值)
* 会员提现账户
* 会员收货地址
* 会员等级折扣
* 交易结算日志
* 积分发放兑换机制
* 提现管理
* 问答列表
* 内置后台消息通知体制
* 广告位置
* 广告列表
* 文章管理
* 文章分类
* 专题管理
* 资源OSS管理
* 资源OSS样式管理
* 客服模块
* 配送轨迹模块
* 友情链接
* 二维码模块
* 条形码模块
* 管理组人员管理
* 操作日志
* 用户组
* 菜单管理
* 权限规则
* 系统配置管理
* 前台导航
* 支付模块
* 支付日志
* 支付原路退回
* 区域管理
* 快递公司管理
* 配送方式配置
* APP 应用管理
* APP 安装包
* 短信消息
* 邮件消息
* 接口批量调用

## 安装
必须将项目下的`public`目录设为`web访问`目录，第一次访问首页时会进入`安装向导`，务必请通过向导完成安装。

## 快速启动
切换到项目根目录，在命令行输入`php think run -H 127.0.0.1 -p 8080`，启动 PHP 自带的`webserver`服务，按键`Ctrl + C`退出服务。

> 建议使用`IP`启动，避免使用`localhost`，并且此方法只适合调试环境。

## 常见问题
- 如何隐藏`index.php`入口文件?  
建议采用`PATH_INFO`访问地址，隐藏入口文件可做伪静态，请参见：  
[https://doc.careyshop.cn/guide/rewrite/](https://doc.careyshop.cn/guide/rewrite/)

## 接口调试
![](https://aliyun.oss.careyshop.cn/poster/rest_api.png)

## 管理后台
![](https://aliyun.oss.careyshop.cn/poster/gray/%E9%A6%96%E9%A1%B5-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%95%86%E5%93%81%E5%88%97%E8%A1%A8-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%95%86%E5%93%81%E8%A7%84%E6%A0%BC-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%AA%92%E4%BD%93%E8%AE%BE%E7%BD%AE-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%95%86%E5%93%81%E9%A2%84%E8%A7%88-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%95%86%E5%93%81%E5%88%86%E7%B1%BB-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%95%86%E5%93%81%E8%AF%84%E4%BB%B7-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%AF%84%E4%BB%B7%E6%98%8E%E7%BB%86-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%AE%A2%E5%8D%95%E5%88%97%E8%A1%A8-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%AE%A2%E5%8D%95%E8%AF%A6%E6%83%85-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%AE%A2%E5%8D%95%E6%89%93%E5%8D%B0-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%94%AE%E5%90%8E%E5%88%97%E8%A1%A8-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E5%94%AE%E5%90%8E%E8%AF%A6%E6%83%85-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%B5%84%E6%BA%90%E7%AE%A1%E7%90%86-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E7%BC%A9%E7%95%A5%E5%9B%BE%E8%AE%BE%E8%AE%A1-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%B5%84%E6%BA%90%E9%80%89%E6%8B%A9-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E7%89%A9%E6%B5%81%E8%BD%A8%E8%BF%B9-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%A7%86%E9%A2%91%E6%92%AD%E6%94%BE-%E7%81%B0.png)
![](https://aliyun.oss.careyshop.cn/poster/gray/%E8%B5%84%E6%BA%90%E9%A2%84%E8%A7%88-%E7%81%B0.png)

#### 声明
版权归宁波互明科技有限公司所有