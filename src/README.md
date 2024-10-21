## CozeX

cozex系统基于Yii2框架，采用PHP7.0以上版本开发，具有很强的可扩展性，并且完全开放源代码。拥有灵活扩展特性之外更安全、高效、数据过滤，同时采用vue作为页面模板渲染引擎，让系统更简单。

## 版本说明

cozex V1.0.0是一个基础版本，将一直持续更新发展，可以[点击下载](https://www.baidu.com)获取

## 技术要求

- Linux基本命令使用、文件、进程管理、Nginx+PHP+MySQL+Redis环境配置

- PHP开发

- MySQL数据库

- Redis数据库

- <a href="https://www.yiiframework.com/doc/guide/2.0/zh-cn" target="_blank">Yii框架</a>

- <a href="https://cn.vuejs.org/index.html" target="_blank">Vue</a>

- <a href="https://element.eleme.cn/#/zh-CN" target="_blank">Element-UI</a>

- <a href="https://getcomposer.org/doc/00-intro.md" target="_blank">Composer</a>

## 平台要求

1.Windows 平台、Linux/Unix 平台

IIS/Apache/Nginx + PHP7/PHP8 + MySQL5.6/MySQL5.7 + Redis(4|5)

2.PHP必须环境或启用的系统函数

CURL：数据采集

Redis：数据处理与记录

proc_open：执行脚本命令

MySQL扩展库：数据存储

3.基本目录结构

```
/bootstrap #启动文件
/condif #配置文件
/controllers #控制器
/events #事件定义类
/forms #表单处理
/handlers #事件处理
/helpers #公共函数、助手类
/jobs #队列任务
/models #数据库表模型
/validators #自定义验证器
/views #视图文件
/web #入口文件、资源文件
```

## 程序安装使用

1.下载程序解压到本地目录;

2.上传程序目录中的`/`到网站根目录；

3.浏览器访问您的网站，自动进入安装界面，填写数据库配置信息完成安装；

## 配置

### 数据库配置

复制`db.example.php`到`db.php`，按相关参数配置。

### 本地化配置

- 环境变量

复制`.env.example.php`到`.env`按需配置相关选项。

在`YII_DEBUG = true`的情况下，所有错误结果将由Yii框架处理，`YII_DEBUG = false`或未配置`YII_DEBUG`的情况下，所有错误结果将统一处理，HTTP不再直接返回相关错误码，错误码在ajax下返回在`code`字段中。

- 系统配置

复制`local.example.php`到`local.php`按需配置相关选项。