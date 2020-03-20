CareyShop 商城框架系统
===============
CareyShop（简称 CS）是一套基于 ThinkPHP5 框架开发的高性能商城框架系统，秉承简洁、快速、极致的开发理念，框架内使用面向对象模块化调用，在多终端、跨平台时采用 REST API 架构来进行数据交互，可直接对接 PC、移动设备、小程序、云部署，构建 Android、IOS 的 APP。

### 设计理念
放眼移动热潮、新零售概念、各类<abbr title="泛指各类终端机器或各类平台，终端机有IOS与Android为代表，平台以“微信小程序”为代表">终端</abbr>的增多，服务端会与各类前端进行交互，和第三方相互协作也更加紧密、频繁。出于这样的整体环境，我们摒弃传统开发方向，直接以 REST 架构为基础，将各类业务处理层模块化。

框架所有的业务处理逻辑存放在公共模块目录中，对于框架内的其他模块可通过面向对象的方式调用公共模块，而外部则以 API 接口进行交互，如此真正做到业务处理层的入口路径统一。具体开发使用请参见[「CareyShop 完全开发手册」](https://doc.careyshop.cn/docs/word/)

### 导航向导
> CareyShop 交流一群（QQ）：714593455

[官方网站](https://www.careyshop.cn "CareyShop官方网站") | [Demo 后台预览](https://demo.careyshop.cn/admin "Demo 后台预览") | [Demo API 沙盒](https://demo.careyshop.cn/api "Demo API 沙盒") | [文档中心](https://doc.careyshop.cn "CareyShop文档中心") | [客户组 API 文档](https://doc.careyshop.cn/docs/client_api/a-61295176156 "客户组 API 使用手册") | [管理组 API 文档](https://doc.careyshop.cn/docs/admin_api/a-11523287990 "管理组 API 使用手册") | [数据库词典](https://doc.careyshop.cn/docs/data_dict "数据库词典")

后端项目 Git

[Github 仓库](https://github.com/dnyz520/careyshop "Github 仓库") | [码云仓库](https://gitee.com/careyshop/careyshop "码云仓库") | [Coding 仓库](https://e.coding.net/careyshop/careyshop.git "Coding 仓库")

后台项目 Git

[Github 仓库](https://github.com/dnyz520/careyshop-admin "Github 仓库") | [码云仓库](https://gitee.com/careyshop/careyshop-admin "码云仓库") | [Coding 仓库](https://e.coding.net/careyshop/careyshop-admin.git "Coding 仓库")

### 安装使用
您除了在下方提供的`Git`下载获得源代码外，还可以通过`Composer`安装。数据库 SQL 导入文件位于：`install\careyshop.sql`

#### Composer
如果还没有安装 Composer，在 Linux 和 Mac OS X 中可以运行如下命令：
```shell
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

在 Windows 中，你需要下载并运行 [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe "Composer-Setup.exe")。
如果遇到任何问题或者想更深入地学习 Composer，请参考 Composer 文档（[英文文档](https://getcomposer.org/doc/ "英文文档")，[中文文档](http://www.kancloud.cn/thinkphp/composer "中文文档")）。

由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议使用国内镜像（阿里云）。

打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：
```shell
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

#### 安装&更新
如果你是第一次安装的话，在命令行下面，切换到你的 web 根目录下面并执行下面的命令：
```shell
composer create-project dnyz520/careyshop cs
```

这里的`cs`目录名你可以任意更改，这个目录是应用根目录。

如果你之前已经安装过，那么切换到你的应用根目录下面，然后执行下面的命令进行更新：
```shell
composer update dnyz520/careyshop
```

### 初始数据
**超级管理员**

账号：admin

密码：admin888

**App**

app_key：86757125

app_secret：ea1bd533d001fd73b09944f04c96a6fc

#### 声明
CareyShop 原则上使用 AGPLv3 开源，请遵守 AGPLv3 的相关条款，或者与我们联系获取商业授权，

本项目包含的源码（包括第三方）和二进制文件存在版权信息另行标注的情况。

证书号：软著登字第2395639号

登记号：2018SR066544
