## DHCP配置实验

本节为实验课程，在ENSP中操作

### 实验拓扑图

![image-20250324224146605](https://img.yatjay.top/md/20250324224146655.png)

### 前置实验：VLAN和VLAN间路由的配置

#### 配置思路

在交换机SW上

- 创建VLAN10、VLAN20并为其VLANIF10和VLANIF20
- 将GE0/0/1接口模式设置为access，并划入VLAN10；将GE0/0/2接口模式设置为access，并划入VLAN20

在终端上：模拟器上手动配置静态IP地址

#### 具体步骤

1. 首先，通过输入命令`undo info-center enable`来避免配置过程中出现的提示消息中断，确保命令输入流程顺畅。
2. 创建两个VLAN，即VLAN 10和VLAN 20，或者通过命令 `vlan batch 10 20` 实现批量创建VLAN，提高效率。
3. 配置VLAN的虚拟接口，分别为VLAN 10和VLAN 20设置IP地址`192.168.1.254`和`192.168.20.254`，作为PC1和PC2的默认网关。
4. 将物理接口`g0/0/1`和`g0/0/2`分别设置为接入模式（access），并分配给`VLAN 10`和`VLAN 20`，实现三层交换机上的VLAN配置。
5. 在两台电脑上配置静态IP地址，作为后续动态IP配置的基础步骤，确保网络连接的稳定性和可达性。

#### 配置命令

##### 交换机配置

```cmd
<Huawei>system-view
Enter system view, return user view with Ctrl+Z.
[Huawei]undo info-center enable
Info: Information center is disabled.
```

创建VLAN

```cmd
[Huawei]vlan batch 10 20  //批量创建VLAN
Info: This operation may take a few seconds. Please wait for a moment...done.
[Huawei]dis vlan ?
  INTEGER<1-4094>  VLAN ID
  summary          Summary information of vlans
  |                Matching output
  <cr>             

[Huawei]dis vlan summary  //显示VLAN摘要
static vlan:
Total 3 static vlan.
  1 10 20    //可以看到已经创建好了VLAN10和VLAN20

dynamic vlan:
Total 0 dynamic vlan.

reserved vlan:
Total 0 reserved vlan.

```

配置VLAN虚拟接口，分别作为PC1和PC2的默认网关

```cmd
[Huawei]interface vlanif10
[Huawei-Vlanif10]ip address 192.168.10.254 24
[Huawei-Vlanif10]interface vlan20
[Huawei-Vlanif20]ip address 192.168.20.254 24
[Huawei-Vlanif20]quit
[Huawei]
```

将物理接口上的VLAN配置

GE0/0/1配置

```cmd
[Huawei]interface GigabitEthernet 0/0/1
[Huawei-GigabitEthernet0/0/1]dis this  //显示接口当前配置，可以看到没有任何配置
#
interface GigabitEthernet0/0/1
#
return
[Huawei-GigabitEthernet0/0/1]port link-type access  //配置接口模式为access
[Huawei-GigabitEthernet0/0/1]port default vlan 10  //将接口放入指定VLAN10
[Huawei-GigabitEthernet0/0/1]dis this  //显示接口当前配置，可以看到已经有了VLAN配置
#
interface GigabitEthernet0/0/1
 port link-type access
 port default vlan 10
#
return
[Huawei-GigabitEthernet0/0/1]quit
```
GE0/0/2使用同样配置

```cmd
[Huawei]interface GigabitEthernet 0/0/2
[Huawei-GigabitEthernet0/0/2]dis this  //显示接口当前配置，可以看到没有任何配置
#
interface GigabitEthernet0/0/2
#
return
[Huawei-GigabitEthernet0/0/2]port link-type access  //配置接口模式为access
[Huawei-GigabitEthernet0/0/2]port default vlan 20  //将接口放入指定VLAN10
[Huawei-GigabitEthernet0/0/2]dis this  //显示接口当前配置，可以看到已经有了VLAN配置
#
interface GigabitEthernet0/0/2
 port link-type access
 port default vlan 20
#
return
[Huawei-GigabitEthernet0/0/2]quit
```

##### 终端配置

PC1手动配置静态IP

![image-20250324230911627](https://img.yatjay.top/md/20250324230911679.png)

PC2手动配置静态IP

![image-20250324231011951](https://img.yatjay.top/md/20250324231012003.png)

应用上述配置后，用PC1去pingPC2，理论上应该可以ping通，可以看到已经能够ping通

![image-20250324231209971](https://img.yatjay.top/md/20250324231210026.png)

具体ping过程如下

- PC1判断目的地址192.168.20.1是否跟自己在同一网段，发现不在同一网段，将其转发至默认网关192.168.10.254
- 192.168.10.254在交换机上，交换机基于目的地址192.168.20.1查找路由表，发现路由表中有匹配条目(因为目的网段是该交换机直连的网段)
  - ![](https://img.yatjay.top/md/20250324231826345.png)


- 于是交换机将该数包从VLANIF20转发出去，实际上转发到了VLAN20当中，VLAN20中的PC2就能收到该报文

### DHCP配置实验

配置要求：

- VLAN10使用基于接口的DHCP配置，PC1通过DHCP协议获取新的IP地址
- VLAN20使用基于全局地址池的DHCP配置，PC2通过DHCP协议获取新的IP地址

#### 基于接口的DHCP配置

VLAN10使用基于接口的DHCP配置

```cmd
[Huawei]dhcp enable  //全局使能DHCP
[Huawei]int Vlanif 10
[Huawei-Vlanif10]dhcp ?
  client  DHCP client 
  relay   DHCP relay 
  select  Select 
  server  DHCP server 

[Huawei-Vlanif10]dhcp select interface  //为当前接口使用基于接口的DHCP配置
[Huawei-Vlanif10]dhcp server dns-list 8.8.8.8   //配置DNS服务器列表(基于接口不用配置网关，接口地址自动作为网关)
[Huawei-Vlanif10]dhcp server excluded-ip-address 192.168.10.101 192.168.10.253   //需要排除不参与分配的地址
[Huawei-Vlanif10]dhcp server lease day 3   //配置地址租期时间

[Huawei-Vlanif10]dis this  //查看当前接口配置
#
interface Vlanif10
 ip address 192.168.10.254 255.255.255.0
 dhcp select interface
 dhcp server excluded-ip-address 192.168.10.101 192.168.10.253
 dhcp server lease day 3 hour 0 minute 0
 dhcp server dns-list 8.8.8.8
#
return
[Huawei-Vlanif10]
```

在PC1上开启DHCP获取新的地址

![image-20250325233456267](https://img.yatjay.top/md/20250325233456329.png)

此时`ipconfig`命令可以看到新获取的IP地址`192.168.10.100`，可以看到华为设备DHCP分配地址时，是从地址池中最大的地址开始分配的(我们DHCP服务排除了`192.168.10.101`到`192.168.10.253`地址)

![image-20250325233539630](https://img.yatjay.top/md/20250325233539683.png)

在PC1上使用`ipconfig /renew`可以重新获取IP地址`192.168.10.99`

![image-20250325233909176](https://img.yatjay.top/md/20250325233909242.png)

#### 基于全局地址池的DHCP配置

VLAN20使用基于全局地址池的DHCP配置

```cmd
[Huawei]dhcp enable  //全局使能DHCP
[Huawei]ip pool 20
Info:It's successful to create an IP address pool.
[Huawei-ip-pool-20]network 192.168.20.0 mask 24  //宣告地址池网络
[Huawei-ip-pool-20]gateway-list 192.168.20.254  //本地址池的网关地址
[Huawei-ip-pool-20]dns-list 9.9.9.9  //本地址池使用的DNS服务器地址
[Huawei-ip-pool-20]lease day 8  //本地址池地址租期
[Huawei-ip-pool-20]excluded-ip-address 192.168.20.150 192.168.20.253  //需要排除不参与分配的地址(网关地址默认不参与分配，自动排除掉)
[Huawei-ip-pool-20]q
[Huawei]int Vlanif 20
[Huawei-Vlanif20]dhcp select global   //在接口上选择全局地址池——为什么自动选择ip pool 2地址池而不选其他的？
[Huawei-Vlanif20]
[Huawei-Vlanif20]dis this
#
interface Vlanif20
 ip address 192.168.20.254 255.255.255.0
 dhcp select global
#
return
[Huawei-Vlanif20]
```

> 笔者注：
>
> 问题：此行`[Huawei-Vlanif20]dhcp select global`   //在接口上选择全局地址池——为什么自动选择`ip pool 2`地址池而不选其他的？
>
> 解答：PC2的DHCP请求通过`VLANIF20`发送给交换机，交换机默认从和接口相同网段的地址池当中选取地址进行分配。这里我们的`VLANIF20`位于`192.168.20.0/24`网段，在为`VLANIF20`执行`dhcp select global`时，为其绑定的地址池就是和`192.168.20.0/24`同网段的地址池即我们提前配置好的`ip pool 2`

使用display current-configuration可以看到ip pool 20已经配置好

```cmd
#
ip pool 20
 gateway-list 192.168.20.254
 network 192.168.20.0 mask 255.255.255.0
 excluded-ip-address 192.168.20.150 192.168.20.253
 lease day 8 hour 0 minute 0
 dns-list 9.9.9.9
#
```

在PC2上开启DHCP获取新的地址

![image-20250325235138997](https://img.yatjay.top/md/20250325235139053.png)

此时`ipconfig`命令可以看到新获取的IP地址`192.168.10.149`

![image-20250325235156631](https://img.yatjay.top/md/20250325235156680.png)

PC2去ping PC1，能够ping通

![image-20250325235252089](https://img.yatjay.top/md/20250325235252143.png)

### 三个扩展知识点

#### DHCP中继：dhcp relay

DHCP中继用来解决DHCP服务器和终端不在同一个网段的问题

DHCP请求是广播包，广播无法穿透三层设备，使用DHCP中继，可以将广播包转换为单播包，发给DHCP服务器

#### DHCP snoopying

### 防止DHCP Server仿冒者攻击导致用户获取到错误的IP地址和网络参数

**攻击原理**：

由于DHCP Server和DHCP Client之间没有认证机制，所以如果在网络上随意添加一台DHCP服务器，它就可以为客户端分配IP地址以及其他网络参数。如果该DHCP服务器为用户分配错误的IP地址和其他网络参数，将会对网络造成非常大的危害。

如下图所示，DHCP Discover报文是以广播形式发送，无论是合法的DHCP Server，还是非法的DHCP Server都可以接收到DHCP Client发送的DHCP Discover报文。

![img](https://download.huawei.com/mdl/image/download?uuid=3f003c9c6435482f97b2df18e95b97ad)

如果此时DHCP Server仿冒者回应给DHCP Client仿冒信息，如错误的网关地址、错误的DNS（Domain Name System）服务器、错误的IP等信息，如图2所示。DHCP Client将无法获取正确的IP地址和相关信息，导致合法客户无法正常访问网络或信息安全受到严重威胁。

**图1-3** DHCP Server仿冒者攻击示意图

![img](https://download.huawei.com/mdl/image/download?uuid=53d6643711884e2492ab595e1faa53f2)

**解决方法**：

为了防止DHCP Server仿冒者攻击，可配置设备接口的“信任（Trusted）/非信任（Untrusted）”工作模式。

将与合法DHCP服务器直接或间接连接的接口设置为信任接口，其他接口设置为非信任接口。此后，从“非信任（Untrusted）”接口上收到的DHCP回应报文将被直接丢弃，这样可以有效防止DHCP Server仿冒者的攻击。如下图所示。

 **图1-4** Trusted/Untrusted工作模式示意图

![img](https://download.huawei.com/mdl/image/download?uuid=60f58a237bb8433ab8b4c9bbd743b250)

#### DHCP option 43

`DHCP option 43`，给AP分配IP地址的同时，告诉AP，AC的地址是多少。
