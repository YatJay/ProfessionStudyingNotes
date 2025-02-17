# 9-12 Windows Server 2008R2 FTP服务器

## FTP服务器配置

FTP端口：TCP 20（数据）21（控制）数据端口比控制端口小1

添加FTP站点：开始→管理工具→IIS管理器→网站→添加FTP站点

![image-20230924205532674](https://img.yatjay.top/md/image-20230924205532674.png)

## FTP权限控制

![image-20230924205556842](https://img.yatjay.top/md/image-20230924205556842.png)

## FTP站点访问

客户端访问：浏览器或windows搜索ftp://192.168.1.10

命令行访问：DOS下ftp命令访问

- dir：展示目录下的文件
- get：从服务器端下载文件
- put ：向FTP服务器端上传文件
- lcd：设置客户端当前的目录
- bye：退出FTP连接

## 例题

![image-20230924205746134](https://img.yatjay.top/md/image-20230924205746134.png)

![image-20230924205808615](https://img.yatjay.top/md/image-20230924205808615.png)

## Linux Apache服务器配置简介

Apache提供web、ftp等服务，其中web配置文件 httpd.conf

Apache站点默认WEB根目录是：/var/www/html

虚拟主机：**基于IP地址、基于端口、基于名字**

