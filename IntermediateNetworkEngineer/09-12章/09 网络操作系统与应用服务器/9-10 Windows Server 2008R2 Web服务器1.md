# 9-10 Windows Server 2008R2 IIS服务器1（重点，经常考）

## IIS简介

IIS服务可以提供多种服务，其中最典型最常用的是Web服务和FTP服务

llS (Internet lnformation Server)因特网信息服务器

可以构建www服务器、FTP服务器和SMTP服务器**【不能构建也没有POP3和IMAP】**

安装lIlS服务：开始→管理工具→服务器管理→角色→添加角色→web服务器(IS)

## IIS编辑网站信息

![image-20230924203027519](https://img.yatjay.top/md/image-20230924203027519.png)

区分一个网站的3种方式

- IP地址：192.168.200.122和192.168.200.123分别是2个网站
- 端口：192.168.200.122:80和192.168.200.122:8080分别是2个网站
- 主机名：即域名，IP地址和端口号相同，主机名不同，分别指向不同网站

## Web默认文档

即新建网站后缺省的默认页面，优先访问`Default.htm`，若没有则按照下面默认文档列表依次往下进行访问

![image-20230924203357181](https://img.yatjay.top/md/image-20230924203357181.png)

## 网站安全配置（偶尔考）

一般网站都是匿名访问，IIS网站也支持身份认证访问，需要用户名密码才可以访问

IIS可以选择身份认证方式

IIS也支持IP地址和域限制：即添加IP白名单或黑名单

![image-20230924203531280](https://img.yatjay.top/md/image-20230924203531280.png)

## 例题

![image-20230924203747274](https://img.yatjay.top/md/image-20230924203747274.png)

解析：IIS不支持DNS搭建，DNS需要单独搭建，不依托于IIS

![image-20230924203801258](https://img.yatjay.top/md/image-20230924203801258.png)

解析：IIS支持4种身份认证方式，如下图所示

![image-20230924203905038](https://img.yatjay.top/md/image-20230924203905038.png)

**安全级别最高的是Windows身份验证**，可以做本地认证和域认证
