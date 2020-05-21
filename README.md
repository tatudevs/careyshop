CareyShop 商城框架系统
===============
CareyShop（简称 CS）是一套基于 ThinkPHP5 框架开发的高性能商城框架系统，秉承简洁、快速、极致的开发理念，采用前后端分离，支持分布式部署。框架内部使用面向对象模块化调用，在多终端、跨平台时采用 REST API 进行数据交互，可直接对接 PC、移动设备、小程序、云部署，构建 Android、IOS 的 APP。

**CareyShop（QQ）交流一群：714593455**

### 传送门
[后台 Demo 预览](https://demo.careyshop.cn/admin "后台 Demo 预览") | [官方网站](https://www.careyshop.cn "CareyShop官方网站") | [文档中心](https://doc.careyshop.cn "CareyShop文档中心") | [数据库词典](https://doc.careyshop.cn/docs/data_dict "数据库词典")

后端项目 Git  
[Github 仓库](https://github.com/dnyz520/careyshop "Github 仓库") | [码云仓库](https://gitee.com/careyshop/careyshop "码云仓库") | [Coding 仓库](https://e.coding.net/careyshop/careyshop.git "Coding 仓库")

后台项目 Git  
[Github 仓库](https://github.com/dnyz520/careyshop-admin "Github 仓库") | [码云仓库](https://gitee.com/careyshop/careyshop-admin "码云仓库") | [Coding 仓库](https://e.coding.net/careyshop/careyshop-admin.git "Coding 仓库")

### 安装
将项目下的`public`目录设为`web访问`目录，第一次访问时会进入`安装向导`，通过向导完成安装。

### 快速启动
切换到项目`public`目录下，输入命令行`php -S 127.0.0.1:8080 router.php`，便可使用 PHP 自带的`webserver`服务快速访问，按键`Ctrl + C`退出服务。

> 建议使用`IP`启动，避免使用`localhost`，并且此方法只适合调试环境。

### 常见问题
- 不习惯将入口文件部署在`public`或部署环境不支持怎么办?  
可以灵活变动，请参见：  
[https://doc.careyshop.cn/docs/word/a-61530552870](https://doc.careyshop.cn/docs/word/a-61530552870)

- 如何隐藏`index.php`入口文件?  
建议采用`PATH_INFO`访问地址，隐藏入口文件可做伪静态，请参见：  
[https://doc.careyshop.cn/docs/word/a-61530552870](https://doc.careyshop.cn/docs/word/a-61530552870)

#### 声明
CareyShop 原则上使用 AGPLv3 开源，请遵守 AGPLv3 的相关条款，或者与我们联系获取商业授权，   
本项目包含的源码（包括第三方）和二进制文件存在版权信息另行标注的情况。
