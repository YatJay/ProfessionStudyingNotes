# 08 华为设备配置2 ACL和DHCP

### 重点内容

重点1：ACL原理与配置

重点2：动态主机配置协议DHCP

重点3：网络地址转换NAT

补充1：无线WLAN技术与配置.PDF

补充2：以太网链路聚合与交换机堆叠、集群.PDF

## ACL原理与配置

### ACL概述

#### ACL的引入

技术背景：需要一个工具实现流量过滤

如下图所示，某公司为保证财务数据安全，禁止研发部门访问财务服务器，但总裁办公室不受限制。

![image-20240508200607078](https://img.yatjay.top/md/image-20240508200607078.png)

ACL最重要的2个功能：过滤和匹配

1. 流量过滤
2. 匹配流量交给相应的应用

#### ACL定义

- ACL是由一系列permit或deny语句组成的、有序规则的列表

- ACL是一个匹配工具能够对报文进行匹配和区分

![image-20240508200752769](https://img.yatjay.top/md/image-20240508200752769.png)

#### ACL的应用

- 匹配IP流量
- 在Traffic-filter中被调用
- 在NAT(NetworkAddressTranslation)中被调用
- 在路由策略中被调用
- 在防火墙的策略部署中被调用
- 在QoS中被调用
- 其他

### ACL工作原理

#### ACL的组成

ACL由若干条permit或deny语句组成。每条语句就是该ACL的一条规则，每条语句中的permit或
deny就是与这条规则相对应的处理动作。

![image-20240508200951906](https://img.yatjay.top/md/image-20240508200951906.png)

注：早期华为设备最后一条是permit，华为设备现在的最后一条也是deny

#### 规则编号

**规则编号(Rule ID)**：一个ACL中的每一条规则都有一个相应的编号

**步长（Step)**：步长是系统自动为ACL规则分配编号时，每个相邻规则编号之间的差值，缺省值为5。步长的作用是为了方便
后续在旧规则之间，插入新的规则。

**RuleID分配规则**：系统为ACL中首条未手工指定编号的规则分配编号时使用步长值（例如步长=5，首条规则编号为5）作为该规则的起始编号；为后续规则分配编号时，则使用大于当前ACL内最大规则编号且是步长整数倍的最小整数作为规则编号如5、10、15

![image-20240508201209039](https://img.yatjay.top/md/image-20240508201209039.png)

如果希望增加1条规则，如何处理？

![image-20240508201404891](https://img.yatjay.top/md/image-20240508201404891.png)

#### 通配符

**通配符(Wildcard)**：通配符是一个32比特长度的数值，用于指示IP地址中哪些比特位需要严格匹配，
哪些比特位无需匹配通配符通常采用类似网络掩码的点分十进制形式表示，但是含义却与网络掩码完全不同。

![image-20240508201615454](https://img.yatjay.top/md/image-20240508201615454.png)

匹配规则：**"0"表示“匹配”;"1"表示“随机分配”**

如何匹配192.168.1.1/24对应网段地址：192.168.1.1 0.0.0.255

![image-20240508201825388](https://img.yatjay.top/md/image-20240508201825388.png)

匹配192.168.1.0/24这个子网中的奇数IP地址，例如192.168.1.1、192.168.1.3、192.168.1.5等？

对于二进制数，最后一位决定是奇数还是偶数，最后一位为1则为奇数，最后一位为0则为偶数

因此需要最后一个八位二进制数的通配符为 `1111 1110`即254

![image-20240508202019886](https://img.yatjay.top/md/image-20240508202019886.png)

##### 特殊通配符(重要)

| 匹配要求                      | 通配符                                                    |
| ----------------------------- | --------------------------------------------------------- |
| 精确匹配192.168.1.1这个IP地址 | **192.168.1.1 0.0.0.00   =   192.168.1.1 0**(可写成一个0) |
| 匹配所有IP地址                | **0.0.0.0 255.255.255 = any**                             |

### ACL分类(重点)

#### 基于ACL规则定义方式的分类

| 分类          | 编号范围      | 规则定义描述                                                 |
| ------------- | ------------- | ------------------------------------------------------------ |
| **基本ACL**   | **2000~2999** | **仅使用报文的<font color = red>源IP地址</font>、分片信息和生效时间段信息来定义规则。** |
| **高级ACL**   | **3000~3999** | **可使用IPv4报文的源IP地址、目IP地址、IP协议类型、ICMP类型、TCP源/目的端口号、UDP源/目的端口号、生效时间段等来定义规则。** |
| 二层ACL       | 4000~4999     | 使用报文的以太网帧头信息来定义规则，如根据源MAC地址、目MAC地址、二层协议类型等。 |
| 用户自定义ACL | 5000~5999     | 使用报文头、偏移位置、字符串掩码和用户自定义字符串来定义规则。 |
| 用户ACL       | 6000~6999     | 既可使用IPv4报文的源IP地址或源UCL（User Control List）组，也可使用目的IP地址或目的UCL组、IP协议类型、ICMP类型、TCP源端口/目的端口、UDP源端口/目的端口号等来定义规则。 |

#### 基于ACL标识方法的分类

| 分类      | 规则定义描述                                                |
| --------- | ----------------------------------------------------------- |
| 数字型ACL | 传统的ACL标识方法。创建ACL时，指定一个唯一的数字标识该ACL。 |
| 命名型ACL | 通过名称代替编号来标识ACL。                                 |

#### 基本ACL和高级ACL

基本ACL

![image-20240508203113410](https://img.yatjay.top/md/image-20240508203113410.png)

高级ACL

![image-20240508203123294](https://img.yatjay.top/md/image-20240508203123294.png)

### ACL的匹配机制

**匹配原则：一旦命中即停止匹配**

![](https://img.yatjay.top/md/PixPin_2024-05-08_20-32-04.png)

### ACL的匹配顺序及匹配结果

#### 匹配顺序(config模式)

系统按照ACL规则编号从小到大的顺序进行报文匹配，规则编号越小越容易被匹配

![image-20240508203435449](https://img.yatjay.top/md/image-20240508203435449.png)

### ACL的应用位置

应当根据数据报文的流转方向，来设置ACL的应用位置

![image-20240508203646492](https://img.yatjay.top/md/image-20240508203646492.png)

### 入站(inbound)及出站(outbound)方向

- 入站流量先进行ACL匹配，再路由

- 出站流量先路由，后进行ACL匹配

![image-20240508203816182](https://img.yatjay.top/md/image-20240508203816182.png)

### ACL配置及应用

#### 基本ACL

1. 创建基本ACL

使用编号（2000～2999）创建一个数字型的基本ACL，并进入基本ACL视图。

```cmd
[Huawei] acl [ number ] acl-number [ match-order config ] 
```

使用名称创建一个命名型的基本ACL，并进入基本ACL视图。

```
[Huawei] acl name acl-name { basic | acl-number } [ match-order config ] 
```

2. 配置基本ACL的规则

在基本ACL视图下，通过此命令来配置基本ACL的规则。

```
[Huawei-acl-basic-2000] rule [ rule-id ]{ deny | permit }[ source { source-address source-wildcard | any }| time-range time-name ］
```

##### 基础案例：使用基本ACL过滤数据流量

![image-20240508204158027](https://img.yatjay.top/md/image-20240508204158027.png)

配置需求：在Router上部署基本ACL后，ACL将试图穿越Router的源地址为192.168.1.0/24网段的数据包过滤掉，并放行其他流量，从而禁止192.168.1.0/24网段的用户访问Router右侧的服务器网络

1、Router已完成iP地址和路由的相关配置

2、在Router上创建基本ACL，禁止192.168.1.0/24网段访问服务器网络：

```cmd
[Router] acl 2000

[Router-acl-basic-2000] rule deny source 192.168.1.0 0.0.0.255

[Router-acl-basic-2000]rule permit source any
```

3、由于从接口GE0/0/1进入Router，所以在接口GE0/0/1的入方向配置流量过滤：

```cmd
[Router] interface GigabitEthernet 0/0/1

[Router-GigabitEthernet0/0/1]traffic-filter inbound acl 2000

[Router-GigabitEthernet0/0/1] quit
```

#### 高级ACL

创建高级ACL

使用编号（3000～3999）创建一个数字型的高级ACL，并进入高级ACL视图。

```
[Huawei] acl [ number ] acl-number [ match-order config]
```

使用名称创建一个命名型的高级ACL，进入高级ACL视图。

```
[Huawei] acl name acl-name { advance | acl-number } [ match-order config]
```

##### 高级ACL案例：使用高级ACL限制不同网段用户互访

![image-20240508204626161](https://img.yatjay.top/md/image-20240508204626161.png)

配置需求：某公司通过Router实现各部门之间的互连。为方便管理网络，管理员为公司的研发部和市场部规划了两个网段的IP地址。现要求Router能够限制两个网段之间互访，防止公司机密泄露。

1、Router已完成ip地址和路由的相关配置

2、创建高级ACL3001并配置ACL规则，拒绝研发部访问市场部的报文:

```cmd
[Router]acl 3001

[Router-acl-adv-3001] rule deny ip source 10.1.1.0 0.0.0.255 destination 10.1.2.0 0.0.0.255

[Router-acl-adv-3001] quit
```

3、创建高级ACL3002并配置ACL规则，拒绝市场部访问研发部的报文：

```cmd
[Router]acl 3002

[Router-acl-adv-3002] rule deny ip source 10.1.2.0 0.0.0.255 destination 10.1.1.0 0.0.0.255

[Router-acl-adv-3002] quit
```

4、由于研发部和市场部互访的流量分别从接口GE0/0/1和GE0/0/2进入Router，所以在接口GE0/0/1和GE0/0/2的入方向配置流量过滤：

```cmd
[Router] interface GigabitEthernet 0/0/1
[Router-GigabitEthernet0/0/1] traffic-filter inbound acl 3001
[Router-GigabitEthernet0/0/1] quit

[Router] interface GigabitEthernet 0/0/2
[Router-GigabitEthernet0/0/2] traffic-filter inbound acl 3002
[Router-GigabitEthernet0/o/2] quit
```

### ACL综合实验

某公司为保证财务数据安全，禁止研发部门访问财务服务器，但总裁办公室不受限制。

![image-20240508210120484](https://img.yatjay.top/md/image-20240508210120484.png)

路由器ACL核心配置：

```cmd
[Huawei]acl3000  //启动ACL3000，高级ACL
[Huavei-acl-adv-3000] rule 10 deny ip source 192.168.2.0 0.0.0.255 destination 192.168.4.4 0.0.0.0  //禁止研发部门访问财务服务器
[Huawei-acl-adv-3000]rule 20 permit ip source 192.168.3.0 0.0.0.255 destination 192.168.4.4 0  //允许总裁访问财务服务器
[Huawei-acl-adv-3000]rule 30 deny ip source any destination 192.168.4.4 0  //禁止其他任何IP访问财务服务器
[Huawei]int g0/0/2
[Huawei-GigabitEthernet0/0/2]traffic-filter outboumd acl 3000 //将上述ACL应用于GE0/0/2接口的出口方向
```

### 重点考察关键字

- deny/permit
- source/destination
- inbound/outbound
- traffic-filter

## 动态主机配置协议DHCP

大题要求掌握在WinServer和华为设备上配置DHCP

### DHCP的引入：手动配置网络参数的问题

**利用率低**：企业网中每个人固定使用一个IP地址，IP地址利用率低，有些地址可能长期处于未使用状态。

![image-20240508211207008](https://img.yatjay.top/md/image-20240508211207008.png)

**灵活性差**：WLAN（WirelessLocalArea Network，无线局域网）的出现使终端位置不再固定，当无线终端移动到另外一个无线覆盖区域时，可能需要再次配置IP地址

![image-20240508211219382](https://img.yatjay.top/md/image-20240508211219382.png)

### DHCP基本概念

为解决传统的静态手工配置方式的不足DHCP(DynamicHostConfigurationProtocol，动态主机配置协议）
应运而生，其可以实现网络动态合理地分配IP地址给主机使用。DHCP采用C/S构架，主机无需配置，从服务器端获取地址可实现接入网络后即插即用。

DHCP工作示意图如下

![image-20240508211324127](https://img.yatjay.top/md/image-20240508211324127.png)

#### DHCP优点

1. 统一管理：IP地址由从服务器端的地址池中获取，服务器端会记录维护IP地址的使用状态，做到IP地址统一分配、管理。

![image-20240508211430941](https://img.yatjay.top/md/image-20240508211430941.png)

2. 地址租期：DHCP提出了租期的概念，可有效提高地址利用率。

![image-20240508211455913](https://img.yatjay.top/md/image-20240508211455913.png)

### DHCP工作原理

注意下图中的4个报文，考试经常考察

| Protocol      | 广/单播                           | Description                          |
| ------------- | --------------------------------- | ------------------------------------ |
| DHCP Discover | 广播                              | 用于发现当前网络中的DHCP服务器端     |
| DHCP Offer    | **单播**                          | 携带分配给客户端的IP地址             |
| DHCP Request  | <font color = red>**广播**</font> | 告知服务器端自己将使用该IP地址       |
| DHCP Ack      | 单播                              | 最终确认，告知客户端可以使用该IP地址 |

![image-20240508211516190](https://img.yatjay.top/md/image-20240508211516190.png)

思考：为什么DHCP客户端收到offer之后不直接使用该IP地址，还需要发送一个Request告知服务器端？

解答：网络中可能有多台DHCP服务器，收到多个DHCP Offer应答，客户端应当广播告知自己使用了哪一个IP地址

#### DHCP租期更新

当租期过去**50%**之后，应当向DHCP服务器请求继续使用该IP地址，得到回应后刷新租期时长；

如果在50%租期时客户端**未得到原服务器端的回应**，则客户端在**87.5%**租期时会广播发送DHCP Request，**任意台DHCP服务器**端都可回应，该过程称为**重绑定**。

![image-20240508212014458](https://img.yatjay.top/md/image-20240508212014458.png)

### DHCP配置命令介绍

1. 开启DHCP功能

```cmd
[Huawei]dhcp enable
```

2. 开启接口采用接口地址池的DHCP服务器端功能。仅使用客户端直连的某个接口作为DHCP服务器，不需要配置DHCP客户端网关地址，用户DHCP客户端直接指向此接口地址。这个意思是使用接口的ip作为网关，例如你Gigabitthernet0/0/0的ip是192.168.100.1，那么下面的pc获取的地址就是192.168.100.254，网关就是192.168.100.1（一般配合AC使用）

```cmd
[Huawei-Gigabitthernet0/0/0]dhcp select interface
```

3. 指定接口地址池下的DNS服务器地址

```cmd
[Huawei-Gigabittherneto/0/0]dhcp server dns-list ip-address
```

4. 配置接口地址池中不参与自动分配的IP地址范围

```cmd
[Huawei-Gigabitthernet0/0/0]dhcp server excluded-ip-address start-ip-address [ end-ip-address ]
```

5. 配置DHCP服务器接口地址池中IP地址的租用有效期限功能。**缺省情况下，IP地址的租期为1天**。

```cmd
[Huawei-Gigabitthernet0/0/0]dhcp server lease { day day [ hour hour [ minute minute ]] | unlimited }
```

6. 创建全局地址池

```cmd
[Huawei]ip pool ip-pool-name
```

7. 配置全局地址池可动态分配的IP地址范围

```cmd
[Huawei-ip-pool-2]network ip-address [ mask { mask | mask-length }] 
```

8. 配置DHCP客户端的网关地址

```cmd
[Huawei-ip-pool-2]gateway-list ip-address
```

9. 配置DHCP客户端使用的DNS服务器的IP地址

```cmd
[Huawei-ip-pool-2]dns-list ip-address
```

10. 配置IP地址租期

```cmd
[Huawei-ip-pool-2] lease { day day [ hour hour [ minute minute ]] | unlimited }
```

11. 使能接口的DHCP服务器功能，使用全局地址池。实际和考试使用较多，这个是dhcp选择全局地址池，这个你要定义dhcp pool，具体的网关啥的需要你自己先配置好。

```cmd
[Huawei-Gigabitthernet0/0/0]dhcp select global
```

> `dhcp select interface` 和 `dhcp select global` 是网络设备（如路由器或交换机）配置DHCP服务时使用的两个不同命令，它们分别**指定了DHCP服务器分配IP地址的方式**。下面是两者之间的主要区别：
>
> 1. **dhcp select interface**:
>    - 当使用 `dhcp select interface` 命令时，设备会从与客户端连接的接口（即该命令配置的接口）的特定地址池中为客户端分配IP地址。这意味着每个接口可以有独立的地址池，为连接到该接口的客户端提供IP地址、子网掩码、默认网关等网络配置信息。这种方式更灵活，适合需要对不同VLAN或接口应用不同IP地址范围的网络环境。
>    - 接口地址池的配置通常涉及到在接口下直接定义DHCP地址池，包括IP地址范围、租期时间、DNS服务器等信息。
>
> 2. **dhcp select global**:
>    - 使用 `dhcp select global` 命令，则设备会从一个全局地址池中为客户端分配IP地址，不论客户端连接到哪个接口。全局地址池对于整个设备是统一的，适用于所有接口，除非某个接口特别配置了接口地址池（在这种情况下，接口地址池优先）。
>    - 全局地址池的配置通常在设备的全局配置模式下完成，设定一个或多个地址池，这些池包含可供整个网络使用的IP地址资源。
>
> **总结**：
> - **Interface**: 提供基于接口的灵活性和控制，适用于网络分段或VLAN具有不同IP规划的情况。
> - **Global**: 更简化管理，适用于整个网络使用同一地址范围的情况。
>
> 在实际应用中，选择哪种方式取决于网络的具体需求和设计。如果网络环境需要对不同部分实施精细的地址控制，则可能倾向于使用接口地址池；若整体网络结构较为单一，全局地址池则提供了更为简便的管理方式。

### DHCP接口地址池配置

需求描述：配置一台路由器作为DHCP服务器端，使用接口GE0/0/0所属的网段作为DHCP客户端的地址池，同时将接口地址设为DNS Server地址，租期设置为3天。

![image-20240508213245142](https://img.yatjay.top/md/image-20240508213245142.png)

DHCP服务器端配置如下：

```cmd
[Huawei]dhcp enable  //全局使能DHCP服务
[Huawei]interface GigabitEtherneto/0/0
[Huawei-GigabitEtherneto/o/o]dhcp select interface  //关联当前接口到DHCP地址池？
[Huawei-GigabitEthernet0/0/0]dhcp server dns-list 10.1.1.2  //DNS地址
[Huawei-GigabitEthernet0/0/0]dhcp server excluded-ip-address 10.1.1.2  //排除接口自身地址
[Huawei-GigabitEthernet0/o/o]dhcp server lease day 3 //租期
```

### DHCP全局地址池配置

需求描述：配置一台路由器作为DHCP服务器端，配置全局地址池ip pool 2，为DHCP客户端分配IP地址；分配地址为1.1.1.0/24网段，网关地址1.1.1.1，DNS地址同样也是1.1.1.1，租期10天，在GE0/0/0接口下调用全局地址池



![image-20240508213719659](https://img.yatjay.top/md/image-20240508213719659.png)

DHCP服务器端配置如下：

```cmd
[Huawei]dhcp enable  //全局使能DHCP服务
[Huawei]ip pool pool2 //配置全局地址池
Info: It's successful to create an IP address pool.
[Huawei-ip-pool-pool2]network 1.1.1.0 mask 24  //配置地址池范围
[Huawei-ip-pool-pool2]gateway-list 1.1.1.1  //配置网关地址
[Huawei-ip-pool-pool2]dns-list 1.1.1.1  //DNS地址
[Huawei-ip-pool-pool2]lease day 10   //租期
[Huawei-ip-pool-pool2]quit
[Huawei]interface GigabitEthernet0/0/0
[Huawei-GigabitEthernet0/0/0]dhcp select global  //最后在具体的接口中配置选择全局地址池。当GE0/0/0收到DHCP请求就会从全局地址池中进行IP地址分配
```

### DHCP综合实验

此实验涉及VLAN划分，DHCP服务端、DHCP客户端配置

在核心交换机上配置DHCP服务，使得分布在2个VLAN中的PC1和PC2，能够通过核心交换机获得IP地址

实验拓扑如下

![image-20240508232205627](https://img.yatjay.top/md/image-20240508232205627.png)

1. 接入交换机划分VLAN及配置交换机接口模式

```cmd
<Huawei>system-view 
[Huawei]sysname acsw
[acsw]undo info-center enable  //不要返回日志、调试、警告等信息，这些信息容易打断命令的输入
[acsw]vlan batch 10 20  //批量创建VLAN
Info: This operation may take a few seconds. Please wait for a moment...done.

//配置接口g0/0/1
[acsw]int g0/0/1
[acsw-GigabitEthernet0/0/1]port link-type access
[acsw-GigabitEthernet0/0/1]port default vlan 10

//配置接口g0/0/2
[acsw-GigabitEthernet0/0/1]int g0/0/2
[acsw-GigabitEthernet0/0/2]port link-type access
[acsw-GigabitEthernet0/0/2]port default vlan 20

//配置接口g0/0/3
[acsw-GigabitEthernet0/0/2]int g0/0/3
[acsw-GigabitEthernet0/0/3]port link-type trunk  //上行端口G0/0/3应当配置成Trunk口
```

2. PC配置：IP获取方式配置为DHCP

![image-20240508234319847](https://img.yatjay.top/md/image-20240508234319847.png)

3. 在核心交换机配置DHCP服务

```cmd
<Huawei>system-view
[Huawei]sysname coresw
[coresw]undo info-center enable
Info: Information center is disabled.
[coresw]int g0/0/1
[coresw-GigabitEthernet0/0/1]port link-type trunk //核心交换机下连接口配置为Trunk口

//配置DHCP服务器
[coresw-GigabitEthernet0/0/1]q
[coresw]dhcp enable  //启用DHCP服务
Info: The operation may take a few seconds. Please wait for a moment.done.
[coresw]ip pool vlan10  //为vlan10配置IP地址池，这里vlan10只是地址池的名字，和vlan10无直接联系
Info:It's successful to create an IP address pool.
[coresw-ip-pool-vlan10]network 192.168.10.0 mask 24  //vlan10的IP地址池的范围
[coresw-ip-pool-vlan10]gateway-list 192.168.10.254   //vlan10获取IP地址之后应当使用的网关地址，该地址将不会参与分配
[coresw-ip-pool-vlan10]dns-list 8.8.8.8  //vlan10获取IP地址之后应当使用的DNS服务器地址
[coresw-ip-pool-vlan10]excluded-ip-address 192.168.10.101 192.168.10.253  //保留不分配的IP地址，这里假设不分配十进制为三位数的IP地址，实际分配10.1~10.100
[coresw-ip-pool-vlan10]lease day 3  //IP地址租期为3天

[coresw]ip pool vlan20  //为vlan20配置IP地址池，这里vlan20只是地址池的名字，和vlan20无直接联系
Info:It's successful to create an IP address pool.
[coresw-ip-pool-vlan20]network 192.168.20.0 mask 255.255.255.0
[coresw-ip-pool-vlan20]gateway-list 192.168.20.254
[coresw-ip-pool-vlan20]dns-list 114.114.114.114
[coresw-ip-pool-vlan20]excluded-ip-address 192.168.20.2 192.168.20.253  //这里假设仅分配20.1这一个IP地址
[coresw-ip-pool-vlan20]lease day 10
```

在核心交换机配置VLAN 10 20

```cmd
[coresw-ip-pool-vlan10]q
[coresw]vlan batch 10 20
Info: This operation may take a few seconds. Please wait for a moment...done.
[coresw]int vlan 10
[coresw-Vlanif10]ip address 192.168.10.254 24  //配置VLAN10的网关地址
[coresw-Vlanif10]int vlan 20
[coresw-Vlanif20]ip address 192.168.20.254 24  //配置VLAN20的网关地址
```

在VLANIF 10接口应用全局地址池

```cmd
[coresw-Vlanif20]int vlan10
[coresw-Vlanif10]dhcp select global  //？这里如何实现Vlanif10就是从ip pool vlan10地址池取地址
```

在VLANIF 10接口应用全局地址池

```cmd
[coresw-ip-pool-vlan20]int vlan20
[coresw-Vlanif20]dhcp select global  //？这里如何实现Vlanif20就是从ip pool vlan20地址池取地址
```

查看PC1是否分配到了IP地址：没有

![image-20240508235952268](https://img.yatjay.top/md/image-20240508235952268.png)

这是因为：**接入交换机和核心交换机之间接口配置为Trunk，思科的Trunk口默认放行所有VLAN，华为的Trunk口默认阻止所有VLAN，所以两个Trunk口之间不通，需要在两个Trunk口放行VLAN 10和VLAN 20**

核心交换机Trunk口放行VLAN

```cmd
[coresw-Vlanif10]int g0/0/1
[coresw-GigabitEthernet0/0/1]port trunk allow-pass vlan all  //此处all表示放行所有VLAN可以写成10/20，表示放行指定VLAN
```

接入交换机Trunk口放行VLAN

```cmd
[acsw]int g0/0/3
[acsw-GigabitEthernet0/0/3]port trunk allow-pass vlan all  //此Trunk口放行所有VLAN
```

之后再次查看PC1是否获得IP：已经获得IP地址192.168.10.100，华为默认从地址池最大地址开始分配，思科默认从地址池最小地址开始分配

![image-20240509000913796](https://img.yatjay.top/md/image-20240509000913796.png)

同理可见PC2也分配到IP地址

![image-20240509001635720](https://img.yatjay.top/md/image-20240509001635720.png)

PC1和PC2互相ping查看连通情况。

![image-20240509002516225](https://img.yatjay.top/md/image-20240509002516225.png)

第一个包不通是因为进行了arp解析，获取到了网关的MAC

![image-20240509002656944](https://img.yatjay.top/md/image-20240509002656944.png)
