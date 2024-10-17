## CozeX1.0
Cozex是一款可以开源免费使用的管理coze智能体、知识库的系统。实现私有化部署，多账号统一管理。快速部署配置自己的智能体发布使用自己的智能体。
![alt text](agents.jpg)

## 当前开发说明
cozex系统基于Yii2框架，采用PHP7.0以上版本开发，具有很强的可扩展性，并且完全开放源代码。拥有灵活扩展特性之外更安全、高效、数据过滤，同时采用vue作为页面模板渲染引擎，让系统更简单。
cozex V1.0.0是一个基础版本，将一直持续更新发展，可以最新更新下载获取

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

1.推荐 Linux/Unix 平台

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

1.下载安装程序源码解压到本地目录;

2.上传程序目录中的`/src/`全部文件到网站根目录，输入网址；
根据提示信息：
![alt text](installimg.png)

  重新安装可以直接删除根目录的install.lock文件，
  
 
```
// 为了安全,防止重装建议修改根目录文件:index.php里面仅保留下面代码：
  <?php  header('location: web/index.php');
  ```
![alt text](index22.png)

