## ARP和RARP

### 为何需要ARP

数据链路层在进行数据封装时，需要目的MAC地址。数据封装时，只知道本机IP地址、本机MAC地址、目的IP地址，不知道目的MAC地址。因此需要<font color="red">通过ARP协议根据IP地址获取MAC地址</font>

![image-20250226193511143](https://img.yatjay.top/md/20250226193511176.png)

### ARP协议工作过程

例如，主机A要向主机C发送数据

主机A发送数据包给主机C之前，首先通过<font color="red">ARP协议根据IP地址获取主机MAC地址</font>

![image-20250226193658223](https://img.yatjay.top/md/20250226193658254.png)

#### 查询本地ARP缓存

查看主机A的ARP缓存`arp -a`，发现主机A的ARP缓存中没有主机C的MAC地址

![image-20250226193720700](https://img.yatjay.top/md/20250226193720735.png)

可在本机查看到本地的ARP表，ARP表共有IP地址、MAC地址、类型这三个字段

类型字段为“动态”，说明该条目是通过ARP协议学习到的

类型字段为“静态”，说明该条目是手动绑定的

![image-20250226194512131](https://img.yatjay.top/md/20250226194512172.png)

#### ARP请求

若本地没有所需ARP缓存，需要发送ARP请求(广播)

ARP请求**报文内容**如下：

- 目的IP：已知
- 源IP：已知
- 目的MAC：全0即<font color="red">00-00-00-00-00-00</font>
- 源MAC：已知
- 操作类型：<font color="red">Request</font>

ARP请求报文被直接封装在以太网帧内，其中以太网帧的字段：

- 源MAC：已知
- 目的MAC：广播地址即<font color="red">FF-FF-FF-FF-FF-FF</font>

![image-20250226194554296](https://img.yatjay.top/md/20250226194554339.png)

#### 其他主机更新ARP缓存

其他主机收到ARP请求报文后，首先更新自己的ARP缓存

即将ARP请求报文中的源IP、源MAC写入自己的ARP缓存表中

![image-20250226195441008](https://img.yatjay.top/md/20250226195441045.png)

#### 其他主机ARP响应

主机B在更新ARP缓存之后，发现所请求的目的IP不是自己，丢弃该ARP请求报文

主机C在更新ARP缓存之后，发现所请求的目的IP正是自己，返回ARP响应报文(单播)

- 目的IP：已知
- 源IP：已知
- 目的MAC：00-01-02-03-04-AA即主机A的MAC地址
- 源MAC：<font color="red">00-01-02-03-04-CC</font>即自己的MAC地址
- 操作类型：<font color="red">Reply</font>

ARP的响应报文被直接封装在以太网帧内，其中以太网帧的字段：

- 源MAC：00-01-02-03-04-CC即自己的MAC地址
- 目的MAC：单播至00-01-02-03-04-AA即主机A的MAC地址

![image-20250226195515131](https://img.yatjay.top/md/20250226195515162.png)

#### 本机更新ARP缓存

主机A收到ARP响应报文后即更新自己的ARP缓存，类型字段为动态Dynamic

![image-20250226195537477](https://img.yatjay.top/md/20250226195537517.png)

### ARP地址解析过程概述

![image-20250226200242164](https://img.yatjay.top/md/20250226200242204.png)

ARP解析主要过程如下：

1. 主机A查询自己的ARP缓存表，发现没有主机B对应的MAC地址;
2. 主机A发送ARP-Request广播报文;
3. 主机B把主机A的IP、MAC对应信息填入自己的ARP缓存中（ARP学习）；
4. 主机B发送ARP-Reply，把自己的MAC地址信息回复给主机A；
5. 主机A收到主机B的ARP-Reply信息，将主机B的IP、MAC对应信息填入自己的ARP缓存中。

### 免费ARP

<font color="red">免费ARP可以用来探测IP地址是否冲突。</font>

原理：将ARP请求报文的<font color="red">目的IP填写为本机IP、目的MAC填写为全0</font>，作为ARP请求报文广播出去：

- 若有回复，说明本地IP已经被其他主机占用；
- 若无回复，说明本机IP只有本机使用。

应用场景：

- 在路由器端口上配置好一个IP之后，该端口会自动发送一个免费ARP请求报文，确保IP地址不会冲突
- 在DHCP分配到一个IP之后，该主机会自动发送一个免费ARP请求报文，确保IP地址不会冲突

![image-20250226200415214](https://img.yatjay.top/md/20250226200415244.png)

### 代理ARP

PC1和PC2在同一网段，但网关设备却在不同网段，如果PC1 ping PC2，在网关设备上开启代理ARP功能，网关设备会以自己的MAC地址代为应答。代理ARP具备如下特点：

1. <font color="red">部署在网关上</font>，网络中的主机不必做任何改动。

2. <font color="red">可以隐藏物理网络细节</font>，使两个物理网络可以使用同一个网络号。
3. <font color="red">只影响主机的ARP表</font>，对网关的ARP表和路由表没有影响。

如下图所示PC1和PC2均在172.16.0.0/16网段，而作为网关地址的Switch，其一接口在172.16.1.0/24，另一接口在172.16.2.1/24网段。当PC1去ping PC2时，网关会认为PC1和PC2在同一网段，同一个网段通信不需要经过网关，于是不处理该请求。实际上，由于Switch两个接口分别在2个网段，相当于PC1和PC2中间隔了一个网络，因此需要Swich进行转发。此时，需要在网关设备上开启代理ARP功能，网关设备会以自己的MAC地址代为应答

![image-20250226200456382](https://img.yatjay.top/md/20250226200456412.png)

### ARP总结

ARP（AddressResolutionProtocol,地址解析协议）作用是<font color="red">根据IP地址查询MAC地址</font>。

- 类型中的动态表示是通过ARP协议学习到的，静态表示是手动绑定的。

- 通过命令``arp -a`或`arp -g`查看ARP缓存表，删除ARP表项命令是`arp -d`，静态绑定命令是`arp-s`。

RARP（ReverseAddressResolutionProtocol，反向地址转换协议）可以<font color="red">根据MAC地址查找IP地址</font>，常用于无盘工作站。由于设备没有硬盘，无法记录IP，刚启动时会发送一个广播报文，通过MAC去获取IP地址。

GratuitousARP：免费ARP（GratuitousARP）用于<font color="red">检测IP地址冲突</font>，通常发生在接口配置的时候
比如接口刚刚DHCP获取了IP地址，就会对外发送免费ARP。

- 原理：主机发送ARP查找自己IP地址对应的MAC地址，如果有终端回复表示发生了IP地址冲突。

代理ARP：用于**端口隔离、Aggragation-VLAN、单臂路由**。

### 例题

#### 例题1

![image-20250226205543253](https://img.yatjay.top/md/20250226205543296.png)

解析：

- ARP报文直接封装在以太网帧中
- ICMP报文封装在IP中
- OSPF报文封装在IP中
- RIP报文封装在UDP中
- BGP报文封装在TCP中

#### 例题2

![image-20250226205550303](https://img.yatjay.top/md/20250226205550337.png)

#### 例题3

![image-20250226205558127](https://img.yatjay.top/md/20250226205558165.png)

#### 例题4

![image-20250226205606430](https://img.yatjay.top/md/20250226205606463.png)

#### 例题5

![image-20250226205629295](https://img.yatjay.top/md/20250226205629335.png)

![image-20250226205640639](https://img.yatjay.top/md/20250226205640685.png)

解析：

- 注意区分以太网帧的目的MAC地址和ARP请求报文中的目的MAC地址
- 注意华为ensp中，ARP请求报文中目的MAC地址会显示为全F广播地址，与实际不符，这是ensp的一个bug

#### 例题6

![image-20250226205703207](https://img.yatjay.top/md/20250226205703239.png)

解析：ARP攻击主要有：ARPFlood和ARP欺骗，ARP欺骗即伪造ARP请求或应答来欺骗网络中的设备

#### 例题7

![image-20250226205725396](https://img.yatjay.top/md/20250226205725440.png)