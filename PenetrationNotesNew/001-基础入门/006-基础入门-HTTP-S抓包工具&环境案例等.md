# 第6天：基础入门-抓包技术&HTTPS协议&APP&小程序&PC应用&WEB&转发联动

![image-20250920180719317](https://img.yatjay.top/md/20250920180719404.png)

## 知识点

1、抓包技术-HTTP/S-Web&APP&小程序&PC应用等

2、抓包工具-Burp&Fidder&Charles&Proxifier

## 演示案例

注意：本节内容仅能抓取HTTP/S协议流量，一些程序使用的不是HTTP/S流量时无法抓取

准备工作：

为了能够抓取https数据包，在抓包之前必须安装证书，web抓包需要在本地浏览器安装证书，app抓包需要安卓系统安装证书(另外宿主机注意关闭Windows防火墙)

1、浏览器安装证书：解决本地抓HTTPS

2、模拟器安装证书：解决模拟器抓HTTPS

实现目的：

0、掌握几种抓包工具证书安装操作

1、掌握几种HTTP/S抓包工具的使用

2、学会Web,APP,小程序,PC应用等抓包

3、了解此课抓包是针对那些目标什么协议

### 抓包工具介绍

[Fiddler](https://www.telerik.com/fiddler)：是一个http协议调试代理工具，它能够记录并检查所有你的电脑和互联网之间的http通讯，设置断点，查看所有的“进出”Fiddler的数据（指cookie,html,js,css等文件）。 Fiddler 要比其他的网络调试器要更加简单，因为它不仅仅暴露http通讯还提供了一个用户友好的格式。

[Charles](https://www.charlesproxy.com/)：是一个HTTP代理服务器,HTTP监视器,反转代理服务器，当浏览器连接Charles的代理访问互联网时，Charles可以监控浏览器发送和接收的所有数据。它允许一个开发者查看所有连接互联网的HTTP通信，这些包括request, response和HTTP headers （包含cookies与caching信息）。

TCPDump：是可以将网络中传送的数据包完全截获下来提供分析。它支持针对网络层、协议、主机、网络或端口的过滤，并提供and、or、not等逻辑语句来帮助你去掉无用的信息。

BurpSuite：是用于攻击web 应用程序的集成平台，包含了许多工具。Burp Suite为这些工具设计了许多接口，以加快攻击应用程序的过程。所有工具都共享一个请求，并能处理对应的HTTP 消息、持久性、认证、代理、日志、警报。

[Wireshark](https://www.wireshark.org/)：是一个网络封包分析软件。网络封包分析软件的功能是截取网络封包，并尽可能显示出最为详细的网络封包资料。Wireshark使用WinPCAP作为接口，直接与网卡进行数据报文交换。

[科来网络分析系统](https://www.colasoft.com.cn/*)：是一款由科来软件全自主研发，并拥有全部知识产品的网络分析产品。该系统具有行业领先的专家分析技术，通过捕获并分析网络中传输的底层数据包，对网络故障、网络安全以及网络性能进行全面分析，从而快速排查网络中出现或潜在的故障、安全及性能问题。

WPE&封包分析：是强大的网络封包编辑器，wpe可以截取网络上的信息，修改封包数据，是外挂制作的常用工具。一般在安全测试中可用来调试数据通讯地址。

### 几种抓包工具的证书安装

#### 证书导出保存

Charles

![image-20250920212720665](https://img.yatjay.top/md/20250920212720697.png)

Fiddler

![image-20250920214336511](https://img.yatjay.top/md/20250920214336581.png)

Burpsuite

注意：der格式后缀在安卓系统不识别，可以将der后缀改成cer格式再导入安装

![image-20250920215542508](https://img.yatjay.top/md/20250920215542562.png)

#### 模拟器证书安装

模拟器配置共享目录，使得宿主机文件能够和模拟器互通

![image-20250920213001473](https://img.yatjay.top/md/20250920213001539.png)

此时当我们将证书文件拖到宿主机本地的`C:\Users\ASUS\Music\逍遥安卓音乐`下时，能够实现文件共享

![image-20250920213245708](https://img.yatjay.top/md/20250920213245734.png)

在逍遥模拟器中依次点击设置——安全性和位置信息——加密与凭据——从SD卡安装证书，根据提示需要输入模拟器锁屏密码(0000)。安装成功可在“信任的凭据”中找到该证书

![image-20250920214019670](https://img.yatjay.top/md/20250920214019696.png)

#### 浏览器证书安装

以火狐浏览器为例，导入证书的位置在设置——隐私与安全——证书

![image-20250920220258607](https://img.yatjay.top/md/20250920220258668.png)

这里可以导入我们的burp证书，添加信任后即可测试是否能成功进行https抓包

![image-20250920220536922](https://img.yatjay.top/md/20250920220536950.png)

#### 操作系统证书安装

下载证书

![image-20250921220342994](https://img.yatjay.top/md/20250921220343040.png)

下载后双击，然后点击安装证书

![image-20250921220423135](https://img.yatjay.top/md/20250921220423172.png)

为测试方便，可直接为本地计算机安装

![image-20250921220451640](https://img.yatjay.top/md/20250921220451680.png)

根据提示操作即可。安装后，Burp才可以抓取本机程序中的HTTPS数据包

### Web应用-http/s-Burp&Charles&Fiddler

#### Charles

Fiddler默认监听端口为8888，且浏览器不需要配置代理，刷新页面可以抓到访问百度的数据包

![image-20250920221524932](https://img.yatjay.top/md/20250920221524967.png)

#### Fiddler

Fiddler默认监听端口为8888，且浏览器不需要配置代理，刷新页面可以抓到访问百度的数据包

![image-20250920221202890](https://img.yatjay.top/md/20250920221202955.png)

Fiddler支持抓包修改后重新发包，也支持进程抓包

#### Burp

安全测试中，最常用的抓包工具是Burp，Burp需要在软件和浏览器分别设置代理，而后才能抓包

![image-20250920221743232](https://img.yatjay.top/md/20250920221743311.png)

Burp也支持拦截数据包、改包重放

### APP应用-http/s-Burp&Charles&Fiddler

在宿主机本地抓取模拟器数据包，需要提前配置网络环境，在模拟器WLAN设置中配置代理，代理服务器IP地址为宿主机IP地址，代理服务器端口为抓包工具监听端口(8888)

![image-20250920222832801](https://img.yatjay.top/md/20250920222832846.png)

#### Fiddler

Fiddler抓本地流量：工具——选项——HTTPS——勾选解密HTTPS通信——从所有进程

Fiddler抓模拟器流量：

- 工具——选项——HTTPS——勾选解密HTTPS通信——从远程客户端

  ![image-20250920230201486](https://img.yatjay.top/md/20250920230201517.png)

- 工具——选项——连接——勾选允许远程客户端连接

  ![image-20250920230219217](https://img.yatjay.top/md/20250920230219242.png)

(注意更改配置后重启Fiddler生效)

之后，使用模拟器浏览器访问网页，Fiddler可以抓取到数据包

![image-20250920225746346](https://img.yatjay.top/md/20250920225746426.png)

打开某个APP，也能够正常抓取流量

![image-20250920230132302](https://img.yatjay.top/md/20250920230132429.png)

#### Charles

Charles无需配置其他操作，可以直接抓取模拟器流量

![image-20250920230534855](https://img.yatjay.top/md/20250920230534892.png)

#### Burp

Burp抓取模拟器流量，需要修改地址，不能使用默认的本地地址127.0.0.1，而需要使用宿主机的IP地址(模拟器绑定的代理IP地址)

![image-20250920230833295](https://img.yatjay.top/md/20250920230833333.png)

模拟器浏览器访问，Burp能够抓取流量

![image-20250920230937806](https://img.yatjay.top/md/20250920230937860.png)

模拟器打开APP，Burp能够抓取流量

![image-20250920231225236](https://img.yatjay.top/md/20250920231225316.png)

#### Charles二次代理转发给Burp抓包

有时需要先试用Charles抓取数据包后，再转发给Burp做改包等操作，需要在Charles上做如下外带代理服务器设置

![image-20250920233333586](https://img.yatjay.top/md/20250920233333635.png)

填写外带代理服务器地址，由于Charles默认监听8888端口，我们本地Burp工作的端口不能和其冲突，因此Burp监听端口修改为8889

![image-20250920233635948](https://img.yatjay.top/md/20250920233635996.png)

配置完成后打开Burp，并监听本地8889端口。模拟器中测试APP流量，可以在Burp中抓到数据包

![image-20250920234213342](https://img.yatjay.top/md/20250920234213446.png)

这种二次代理的方法在抓取微信小程序流量时比较好用

### WX小程序-http/s-Burp&Charles&Fiddler

#### Fiddler

无需任何配置(实际上Fiddler自动开启了系统代理)，Fiddler能够抓取微信小程序流量，下图是使用Fiddler抓取知乎小程序流量

![image-20250921001845649](https://img.yatjay.top/md/20250921001845732.png)

#### Charles

当不做任何配置时(实际上Charles自动开启了系统代理)，Charles也能直接抓取小程序流量。打开一个微信小程序，可以抓取到小程序流量

![image-20250921002119955](https://img.yatjay.top/md/20250921002120001.png)

#### Charles转发至Burp实现二次代理

现在一些微信小程序有了代理检测，无法直接使用Burp抓包。另外如果Burp抓取微信小程序流量，需要在微信登录时配置好代理服务器地址和端口，但会影响微信聊天，因此常常使用二次转发至Burp这种操作

> 笔者思考：Charles和Fiddler类似Wireshark，是类似监测网卡的全量流量，无需配置代理服务器IP和端口；Burp本身则是模拟代理服务器，需要配置代理服务器IP和端口
>
> 笔者思考纠正：Charles和Fiddler并不类似Wireshark，其实开了Charles或者Fiddler，都会自动开启系统代理(设置——网络——代理——手动配置代理)，Charles和burpsuite联动就相当于系统代理+burpsuite了

配置好Charles外带流量设置，将流量转发至本地127.0.0.1:8889端口，然后Burp监听本地8889端口

![image-20250921001255240](https://img.yatjay.top/md/20250921001255344.png)

打开一个微信小程序，可以抓取到小程序流量。实际上，charles会自动为Windows系统配置系统代理，和clash一样，只是Charles默认监听8888端口。现在一些微信小程序开启了代理检测，似乎无法二次代理转发给Burp

![image-20250921211823104](https://img.yatjay.top/md/20250921211823169.png)

#### Proxifier+Burp指定进程代理

配置Proxifier代理服务器，指向本地Burp即127.0.0.1:8889

![image-20250921213113920](https://img.yatjay.top/md/20250921213113968.png)

开启Burp并使其监听本地8889端口

![image-20250921213134138](https://img.yatjay.top/md/20250921213134186.png)

配置Proxifier代理规则，将wechat*.exe相关进程流量代理至127.0.0.1:8889服务器

![image-20250921213204879](https://img.yatjay.top/md/20250921213204925.png)

此时打开微信某个小程序，在Burp可看到数据包(很多开了代理检测，无法正常跳转，弹幕给出解决方法：要给本地电脑安装证书,如果只是给用户安装,会有抓不到的动态资源包)

![image-20250921213440772](https://img.yatjay.top/md/20250921213440833.png)

### PC端应用-http/s-Burp&Charles&Fiddler

一些程序自带代理检测，配置代理后会直接加载不出来，测试时注意一下

#### 系统代理转发至Burp

在Windows系统设置中配置代理，设置——网络——代理——手动设置代理，代理服务器指向Burp即127.0.0.1:8889

![image-20250921221102449](https://img.yatjay.top/md/20250921221102504.png)

此时，系统所有流量都会转发至Burp处理，后续略

#### Proxifier指定进程代理转发至Burp

系统代理数据包太乱，不容易定位，因此可以使用Proxifier指定需要代理至Burp的进程，将该进程的流量转发至Burp

下面测试将网易云音乐流量包转发至Burp

Proxifier配置代理服务器为Burp

![image-20250921213113920](https://img.yatjay.top/md/20250921213113968.png)

Proxifier配置代理规则

![image-20250921215517361](https://img.yatjay.top/md/20250921215517415.png)

开启网易云音乐并刷新，查看测试结果

![image-20250921215335321](https://img.yatjay.top/md/20250921215335445.png)

注意：一些大型程序如杀毒软件权限极高(锁死)，管理员权限也不行(应该涉及操作系统内核级别的权限)，此时无法使用代理工具进行抓包

### 软件联动-http/s-Proxifier&Charles&Burp

软件联动见上



