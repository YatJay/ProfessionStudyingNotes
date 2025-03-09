## 6-7 虚拟专用网

### 6-7-1 虚拟专用网L2TP和PPTP

#### 虚拟专用网基础

虚拟专用网（VirtualPrivate Network)：一种建立在公网上的，由某一组织或某一群用户专用的通信网络。类似公交专用通道

![image-20250309091159535](https://img.yatjay.top/md/20250309091159619.png)

实现虚拟专用网关键技术

- **隧道技术(Tuneling)**：假设公司在上海，公司局域网中有一台文件服务器(192.168.1.1)，文件服务器并未映射至公网，员工到北京出差需访问该文件服务器，于是使用VPN隧道技术，员工PC使用192.168.1.2，实现文件服务器访问，相当于和文件服务器接在同一交换机之下
- **加解密技术(Encryption&Decryption)**：北京PC访问上海服务器，对通信内容进行加密
- **密钥管理技术 (Key Management)**：北京PC访问上海服务器，通信期间每隔几个小时跟换一次秘钥
- **身份认证技术(Authentication)**：北京PC访问上海服务器，进行身份认证后才允许接入如用户密密码、UKey等

#### VPN分类

##### 根据应用场景

VPN现在广泛应用于企业网络分支机构和出差员工连接总部网络的场景，以下是VPN常见的几种分类方式。

根据应用场景不同分类：

- <font color="red">Client-to-Site VPN</font>：也叫Remote VPN或Easy-VPN，即**客户端与企业内网之间**通过VPN隧道建立连接，客户端可以是一台防火墙、路由器，也可以是个人计算机(个人多一点)。此场景可以使用以下几种VPN技术实现：<font color="red">SSL</font>、IPSec、L2TP和L2TP over IPSec(L2TP本身不加密，over IPSec就加密了)。![image-20250309083752260](https://img.yatjay.top/md/20250309083752297.png)

- <font color="red">Site-to-SiteVPN</font>：即**两个局域网之间**(如公司的的2个分支机构之间，学校的2个校区之间)通过VPN隧道建立连接，部署的设备通常为路由器或者防火墙(在出口路由器或防火墙上开通IPSec)。

  此场景可以使用以下几种VPN技术实现(IPSec和GRE over IPSec用的最多)：<font color="red">IPSec</font>、L2TP、L2TP over IPSeC、<font color="red">GRE over IPSec</font>(IPSec支持加密不支持组播，GRE支持组播不支持加密，GRE over IPSec就支持加密和组播了)和IPSec over GRE![image-20250309083805892](https://img.yatjay.top/md/20250309083805930.png)

##### 根据VPN实现层次

根据VPN技术实现的网络层次分类：

数据链路层：常用PPTP和L2TP，L2TP不加密，<font color="red">PPTP支持加密</font>。

网络层：GRE和IPSec，**IPSec支持加密不支持组播，GRE支持组播不支持加密，GRE over IPSec就支持加密和组播了**

传输层：SSL/TLS VPN，TLS就是基于SSL，SSL/TLS用来保护HTTP

![image-20250309083852820](https://img.yatjay.top/md/20250309083852850.png)

#### 二层隧道协议

二层隧道协议有<font color="red">PPTP和L2TP</font>，都基于**PPP协议**。但存在如下差异：(考选择题)

1. PPTP只支持TCP/IP体系，**网络层必须是IP协议**，而L2TP可以运行在IP（使用UDP）、X.25帧中继或ATM网络上。
2. PPTP只能在两端点间建立<font color="red">单一隧道</font>，L2TP支持在两端点间使用**多个隧道**。
3. L2TP可以提供**包头压缩**。当压缩包头时，系统开销占用4个字节，而在PPTP占用6个字节。
4. L2TP可以<font color="red">提供隧道验证</font>，而PPTP不支持隧道验证。L2TP不加密，<font color="red">PPTP支持加密</font>。

PPP协议包含<font color="red">链路控制协议LCP和网络控制协议NCP</font>。其中LCP向下对接物理层，NCP向上对接网络层

##### PPP认证方式：PAP和CHAP

PAP：两次握手验证协议，<font color="red">口令以明文传送</font>，被验证方首先发起请求

![image-20250309083950484](https://img.yatjay.top/md/20250309083950518.png)

CHAP：<font color="red">三次握手</font>，认证过程**不传送认证口令**，传送HMAC散列值。

![image-20250309084004167](https://img.yatjay.top/md/20250309084004199.png)

见6-4 哈希算法：典型的应用是在PPPoE中使用的用户认证：

- 增加一个随机数R做哈希HMAC = Hash(密码+R)
- 需要双方预先知道这个R
- MAC：消除中间人攻击源认证(避免密码直接传输)+完整性校验

#### 例题

##### 例题1

![image-20250309084049672](https://img.yatjay.top/md/20250309084049715.png)

解析：考查CHAP认证过程采用3次握手返回的是HASH值，该哈希值也叫HMAC

##### 例题2

![image-20250309084100859](https://img.yatjay.top/md/20250309084100893.png)

##### 例题3

![image-20250309084112867](https://img.yatjay.top/md/20250309084112912.png)

解析：

- GRE和IPSec都是是三层协议
- 二层协议中PPTP必须要求TCP/IP支持
