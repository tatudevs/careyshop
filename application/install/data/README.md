CareyShop
===============

# 安装向导资源文件

请不要直接导入SQL数据库或手动配置任何模板，正确做法是将`web`访问目录指向到根目录下的`public`，访问站点之后便可通过`安装向导`页完成安装。

```
careyshop.tpl       数据库初始化结构及数据
careyshop_demo.tpl  可选择导入演示数据
function_sql.tpl    数据库函数模板
database.tpl        数据库配置模板文件
production.tpl      后台 APP 配置文件
install.lock        文件存在则表示已安装，删除后可重新执行“安装向导”
```
