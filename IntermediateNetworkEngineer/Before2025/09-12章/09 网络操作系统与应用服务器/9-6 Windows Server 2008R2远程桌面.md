# 9-6 远程桌面

## 远程桌面基础

远程桌面协议**RDP**，基于**TCP 3389**

Windows默认可以使用远程桌面服务(即自带的远程桌面，不太完整)，但只能2人使用，必须为Administrators或RmoteDesktop users才能登陆，安装**远程桌面服务**（相当于完整版的远程桌面服务）后，可以突破此限制

在一台客户端主机打开远程桌面连接

- 图形化：开始-所有程序-附件-远程桌面连接

- 命令快捷键：**mstsc**

即可尝试远程连接登录另一台主机，如下图所示

![image-20240305225816378](https://img.yatjay.top/md/image-20240305225816378.png)

计算机名填写远程主机的IP地址，用户名填写远程主机上可用的用户名

## 远程桌面基本配置项

### 初始配置完整功能的远程桌面服务

服务器管理器——角色——添加角色——服务器角色——勾选“远程桌面服务”

![image-20240305230149964](https://img.yatjay.top/md/image-20240305230149964.png)

下一步之后，在角色服务中可以勾选具体的远程桌面服务项，可以继续下一步配置兼容性、身份验证等项目

![image-20240305230326601](https://img.yatjay.top/md/image-20240305230326601.png)

### 远程桌面支持配置用户远程登陆之后自动执行某个脚本

支持配置用户远程登陆之后自动执行某个脚本：在下图所示的**RDP-Tcp属性——环境——用户登录时启用下列程序中进行配置**

![image-20230924171255596](https://img.yatjay.top/md/image-20230924171255596.png)

### 远程桌面支持配置指定谁可以连接

在“系统属性——远程——远程桌面”下进行配置

- 不允许连接到这台计算机

- 允许运行任意版本的远程桌面的计算机连接：不同时期的发行的Windows都可以连接，如Winxp连接Winserver2019

- 仅允许运行使用网络级别身份验证的远程桌面的计算机连接：同一时期发行的Windows才可以连接

![image-20240305230804840](https://img.yatjay.top/md/image-20240305230804840.png)

### 远程桌面用户组的权限控制

比如可以为远程桌面用户组提供是否允许完全控制、用户访问、来宾访问等

![image-20230924171124528](https://img.yatjay.top/md/image-20230924171124528.png)

## MMC管理控制台(了解，不会考)

MMC管理控制台集成了用来管理网络、计算机、服务的管理工具

（是一个自定义管理工具的工具箱，比如可以将用户管理单元添加至该工具箱，但是管理操作内容和右键计算机——管理——用户管理是一样的）

是一个工具箱，主打管理员的自定义，可以将常用的管理工具放进来

![image-20230924172219832](https://img.yatjay.top/md/image-20230924172219832.png)

可以用来管理硬件、软件和windows系统的网络组建

MMC相当于一个工具箱，可以在里面自由放各类管理工具