## 网络地址转换NAT

技术背景：随着Internet的发展和网络应用的增多，有限的IPv4公有地址已经成为制约网络发展的瓶颈。为解决这个问题，NAT（NetworkAddress Translation，网络地址转换）技术应需而生。

NAT技术主要用于实现内部网络的主机访问外部网络。

本章节我们将了解NAT的技术背景，学习不同类型NAT的技术原理、使用场景。

### NAT概述

#### NAT产生的技术背景

随着互联网用户的增多，IPv4的公有地址资源显得越发短缺

同时IPv4公有地址资源存在地址分配不均的问题，这导致部分地区的IPv4可用公有地址严重不足。

一方面<font color="red">NAT缓解了IPv4地址短缺的问题</font>，另一方面NAT技术让外网无法直接与使用私有地址的内网进
行通信，<font color="red">提升了内网的安全性</font>。

#### 私有IP地址

- **公有地址**：由专门的机构管理、分配，可以在Internet上直接通信的IP地址。
- **私有地址**：组织和个人可以任意使用，无法在Internet上直接通信，只能在内网使用的IP地址。

A、B、C类地址中各预留了一些地址专门作为私有IP地址（RFC1918）：

| **类别** | **IP地址范围**                | **子网掩码**      |
| -------- | ----------------------------- | ----------------- |
| A类      | 10.0.0.0 ~ 10.255.255.255     | 255.0.0.0 (/8)    |
| B类      | 172.16.0.0 ~ 172.31.255.255   | 255.240.0.0 (/12) |
| C类      | 192.168.0.0 ~ 192.168.255.255 | 255.255.0.0 (/16) |

说明：

1. **A类私有地址**：范围为 `10.0.0.0/8`，支持超大规模网络，主机位为24位，可提供约1677万台主机。
2. **B类私有地址**：范围为 `172.16.0.0/12`，适用于中型网络，主机位为16位，可容纳约6.5万台主机。
3. **C类私有地址**：范围为 `192.168.0.0/16`，适合小型网络，主机位为8位，提供254个可用地址。

所有私有地址均遵循RFC 1918标准，仅用于内部网络，不可在互联网中直接路由。

![image-20250327210315141](https://img.yatjay.top/md/20250327210315184.png)

#### NAT技术原理

NAT：对IP数据报文中的IP地址进行转换，是一种在现网中被广泛部署的技术，一般部署在网络出口
设备，例如路由器或防火墙上。

NAT的典型应用场景：在私有网络内部（园区、家庭）使用私有地址，出口设备部署NAT

- 对于“从内到外”的流量，网络设备通过NAT将数据包的源地址进行转换（转换成特定的公有地址）
- 对于“从外到内”的流量，则对数据包的目的地址进行转换，

通过私有地址的使用结合NAT技术，可以有效节约公网IPv4地址。

![image-20250327214425858](https://img.yatjay.top/md/20250327214425902.png)

### 静态NAT

#### 静态NAT原理

静态NAT为每个私有地址分配一个固定的公有地址，形成一对一映射。[很少使用，实际上不能节省IP地址]

支持双向互访，私有地址访问Internet时，源地址转换为对应的公有地址，外部网络访问时，报文中的公有地址（目的地址）会被转换成对应的私有地址。

![image-20250327212419024](https://img.yatjay.top/md/20250327212419065.png)

#### 静态NAT转换示例

![image-20250327212457809](https://img.yatjay.top/md/20250327212457862.png)

#### 静态NAT配置

##### 方式一：接口视图下配置静态NAT

```cmd
[Huawei-GigabitEthernet0/0/0] nat static global { global-address) inside {(host-address }
```

global参数用于配置外部公有地址，inside参数用于配置内部私有地址。

##### 方式二：系统视图下配置静态NAT

```cmd
[Huawei] nat static global { global-address} inside {host-address }
```

配置命令相同，视图为系统视图，之后在具体的接口下开启静态NAT。

```cmd
[Huawei-GigabitEthernet0/0/0] nat static enable
```

在接口下使能nat static功能。

#### 静态NAT配置示例

![image-20250327212644712](https://img.yatjay.top/md/20250327212644764.png)

在R1上配置静态NAT将内网主机的私有地址一对一映射到公有地址。

```cmd
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]ip address 122.1.2.1 24
[R1-GigabitEthernet0/0/1]nat static global 122.1.2.1 inside 192.168.1.1  //这里接口地址和公网地址重合了，严格来说不能重合
[R1-GigabitEthernet0/0/1]nat static global 122.1.2.2 inside 192.168.1.2
[R1-GigabitEthernet0/0/1]nat static global 122.1.2.3 inside 192.168.1.3
```

### 动态NAT

#### 动态NAT原理

- 动态NAT：静态NAT严格地一对一进行地址映射，这就导致即便内网主机长时间离线或者不发送数据时，与之对应的公有地址也处于使用状态。为了避免地址浪费，动态NAT提出了地址池的概念：所有可用的公有地址组成地址池
- 当内部主机访问外部网络时临时分配一个地址池中未使用的地址，并将该地址标记为“In Use”。当该主机不再访问外部网络时回收分配的地址，重新标记为“Not Use"

![image-20250327212814525](https://img.yatjay.top/md/20250327212814570.png)

#### 动态NAT转换示例

##### 出去流量

![image-20250327212841427](https://img.yatjay.top/md/20250327212841482.png)

##### 回程流量

![image-20250327212920431](https://img.yatjay.top/md/20250327212920484.png)

#### 动态NAT配置介绍

1. 创建地址池

```cmd
[Huawei] nat address-group group-index start-address end-address 
```

配置公有地址范围，其中group-index为地址池编号，start-address、end-address分别为地址池起始地址、结束地址.

2. 配置地址转换的ACL规则

```cmd
[Huawei] acl number
[Huawei-acl-basic-number] rule permit source  source-address source-wildcard 
```

配置基础ACL，匹配需要进行动态转换的源地址范围。

3. 接口视图下配置带地址池的NAT out bound

```cmd
[Huawei-GigabitEthernet0/0/0] nat outbound acl-numberaddress-group group-index [ no-pat ] 
```

接口下关联ACL与地址池进行动态地址转换，`no-pat`参数指定不进行端口转换。

#### 动态NAT配置示例

![image-20250327213046122](https://img.yatjay.top/md/20250327213046176.png)

在R1上配置动态NAT将内网主机的私有地址动态映射到公有地址.

```cmd
[R1]nat address-group 1 122.1.2.1 122.1.2.3  //配置公有地址池
[R1]acl 2000
[R1-acl-basic-2000]rule 5 permit source 192.168.1.0 0.0.0.255  //ACL用于匹配私网地址
[R1-acl-basic-2000]quit
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]nat outbound acl 2000 address-group 1 no-pat
```

### NAPT

#### NAPT原理

- 动态NAT选择地址池中的地址进行地址转换时不会转换端口号，即No-PAT（No-Port Address Translation，非端口地址转换），公有地址与私有地址还是1:1的映射关系，无法提高公有地址利用率。

- NAPT（NetworkAddressandPortTranslation，网络地址端口转换）：从地址池中选择地址进行地址转换时不仅转换IP地址，同时也会对端口号进行转换，从而实现公有地址与私有地址的1:n映射，可以有效提高公有地址利用率。

![image-20250327213150634](https://img.yatjay.top/md/20250327213150682.png)

注：ping命令的ICMP报文是三层报文，没有传输层的端口号，也可以做NAPT，NAPT设备会为其生成伪端口号，在进行NAPT转换

#### NAPT转换示例

##### 出去流量

![image-20250327213227216](https://img.yatjay.top/md/20250327213227278.png)

##### 回程流量

![image-20250327213244868](https://img.yatjay.top/md/20250327213244927.png)

#### NAPT配置示例

![image-20250327213309730](https://img.yatjay.top/md/20250327213309781.png)

在R1上配置NAPT让内网所有私有地址通过122.1.2.1访问公网。

```cmd
[R1] nat address-group 1 122.1.2.1 122.1.2.1
[R1] acl 2000
[R1-acl-basic-2000] rule 5 permit source 192.168.1.0 0.0.0.255
[R1-acl-basic-2000] quit
[R1] interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1] nat outbound 2000 address-group 1  //接口下关联ACL与地址池进行动态地址转换，没有no-pat参数，说明需要进行端口转换。
```

### Easy IP

EasyIP：实现原理和NAPT相同，同时转换IP地址、传输层端口，区别在于EasyIP没有地址池的概念使用接口地址作为NAT转换的公有地址。

<font color="red">EaSyIP适用于不具备固定公网IP地址的场景</font>：如通过DHCP、PPPoE拨号获取地址的私有网络出口，可以直接使用获取到的动态地址进行转换。

![image-20250327213408931](https://img.yatjay.top/md/20250327213408982.png)

#### EasyIP配置示例

![image-20250327213440147](https://img.yatjay.top/md/20250327213440196.png)

在R1上配置EaSy-IP让内网所有私有地址通过122.1.2.1访问公网。

```cmd
[R1] acl 2000
[R1-acl-basic-2000] rule 5 permit source 192.168.1.0 0.0.0.255
[R1-acl-basic-2000] quit
[R1] interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1] nat outbound 2000
```

### NAT Server/端口映射

#### NAT Server使用场景

- NAT Server：指定`[公有地址:端口]`与`[私有地址:端口]`的一对一映射关系，<font color="red">将内网服务器映射到公网</font>，
  当私有网络中的服务器需要对公网提供服务时使用。
- 外网主机主动访问`[公有地址：端口]`实现对内网服务器的访问。

![image-20250327213536425](https://img.yatjay.top/md/20250327213536487.png)



![image-20250327213605876](https://img.yatjay.top/md/20250327213605921.png)

#### NAT Server转换示例

![image-20250327213624529](https://img.yatjay.top/md/20250327213624594.png)



#### NAT Server配置示例

![image-20250327213652773](https://img.yatjay.top/md/20250327213652825.png)

在R1上配置NAT Server将内网服务器192.168.1.10的80端口映射到公有地址122.1.2.1的80端口。

```cmd
[R1] interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1] ip address 122.1.2.1 24
[R1-GigabitEthernet0/0/1] nat server protocol tcp global 122.1.2.1 www inside 192.168.1.1 80
```

### 思考题

问：何种NAT转换可以让外部网络主动访问内网服务器？

答：NAT Server或静态NAT

问：NAPT相比较于No-PAT有哪些优点?

答：极大增加地址利用率

### 章节总结

- 在私有网络内使用私有地址，并在网络出口使用NAT技术，可以有效减少网络所需的IPv4公有地址数目，NAT技术有效地缓解了IPv4公有地址短缺的问题。
- 静态NAT提供了一对一映射，支持双向互访。
- 动态NAT、NAPT、EaSy IP为私网主机访问公网提供源地址转换。
- NATServer实现了内网主机对公网提供服务。
