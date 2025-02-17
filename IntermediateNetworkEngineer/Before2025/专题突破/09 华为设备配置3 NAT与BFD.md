# 09 华为设备配置3 NAT与BFD

## 网络地址转换NAT

### NAT概述

#### NAT的产生背景

- 随着互联网用户的增多，IPv4的公有地址资源显得越发短缺。
- 同时IPv4公有地址资源存在地址分配不均的问题，这导致部分地区的IPv4可用公有地址严重不足。
- 为解决该问题，使用过渡技术解决IPv4公有地址短缺就显得尤为必要。

#### 私网IP地址

| 地址类型 | 说明                                                         |
| -------- | ------------------------------------------------------------ |
| 公有地址 | 由专门的机构管理、分配，可以在Internet上直接通信的iP地址。   |
| 私有地址 | 组织和个人可以任意使用，无法在Internet上直接通信，只能在内网使用的iP地址。 |

A、B、C类地址中各预留了一些地址专门作为私有IP地址：

| 类型 | 起始地址    | 结束地址        |
| ---- | ----------- | --------------- |
| A类  | 10.0.0.0    | 10.255.255.255  |
| B类  | 172.16.0.0  | 172.31.255.255  |
| C类  | 192.168.0.0 | 192.168.255.255 |

这个表格简洁明了地列出了A类、B类和C类私有IP地址的范围。私网地址在公网上无法被路由，具体来说是能够路由至目的地址，但返回数据无法找到原来的源IP地址，因而双方通信无法实现。

### NAT技术原理

NAT：对IP数据报文中的IP地址进行转换，是一种在现网中被广泛部署的技术，一般部署在网络出口设备，例
如路由器或防火墙上。

NAT的典型应用场景：在私有网络内部（园区、家庭）使用私有地址，出口设备部署NAT，对于"从内到外"
的流量，网络设备通过NAT将数据包的源地址进行转换（转换成特定的公有地址），而对于“从外到内的"流量，则对数据包的目的地址进行转换。

通过私有地址的使用结合NAT技术，可以有效节约公网IPv4地址。

![image-20240509204958154](https://img.yatjay.top/md/image-20240509204958154.png)

### 静态NAT

#### 静态NAT原理

静态NAT：每个私有地址都有一个与之对应并且固定的公有地址，即私有地址和公有地址之间的关系是一对一映射。

支持双向互访：私有地址访问Internet经过出口设备NAT转换时，会被转换成对应的公有地址。同时，外部网
络访问内部网络时，其报文中携带的公有地址（目的地址）也会被NAT设备转换成对应的私有地址。

![image-20240509205214977](https://img.yatjay.top/md/image-20240509205214977.png)

#### 静态NAT转换示例

![image-20240509205447154](https://img.yatjay.top/md/image-20240509205447154.png)

#### 静态NAT配置示例

如下图所示，在R1上配置静态NAT将内网主机的私有IP地址一对一映射到公有地址

![image-20240509205517248](https://img.yatjay.top/md/image-20240509205517248.png)

配置命令如下：(注意关键字，会考察)

```cmd
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]ip address 122.1.2.1 24
[R1-GigabitEthernet0/0/1]nat static global 122.1.2.1 inside 192.168.1.1
[R1-GigabitEthernet0/0/1]nat static global 122.1.2.2 inside 192.168.1.2
[R1-GigabitEthernet0/0/1]nat static global122.1.2.3 inside 192.168.1.3
```

### 动态NAT

#### 动态NAT原理

动态NAT：静态NAT严格地一对一进行地址映射，这就导致即便内网主机长时间离线或者不发送数据时，与之对应的公有地址也处于使用状态。为了避免地址浪费，动态NAT提出了地址池的概念：所有可用的公有地址组成地址池。

当内部主机访问外部网络时临时分配一个地址池中未使用的地址，并将该地址标记为"In Use"。当该主机不再访问外部网络时回收分配的地址，重新标记为“Not Use"。

![image-20240509205809771](https://img.yatjay.top/md/image-20240509205809771.png)

#### 动态NAT转换示例

去程如下图所示：

![image-20240509205852649](https://img.yatjay.top/md/image-20240509205852649.png)

回程如下图所示：

![image-20240509210035715](https://img.yatjay.top/md/image-20240509210035715.png)

#### 动态NAT配置介绍

1. 创建地址池：配置公有地址范围，其中group-index为地址池编号，start-address、end-address分别为地址池起始地址、结束地址。

```cmd
[Huawei] nat address-group group-index start-address end-address
```

2. 配置地址转换的ACL规则：配置基础ACL，匹配需要进行动态转换的源地址范围。

```cmd
[Huawei] acl number
[Huawei-acl-basic-number ] rule permit source source-address source-wildcard 
```

3. 接口视图下配置带地址池的**NAT Outbound**：接口下关联ACL与地址池进行动态地址转换，**no-pat参数指定不进行端口转换。**

```cmd
[Huawei-GigabitEthernet0/0/0] nat outbound acl-number address-group group-index [ no-pat ] 
```

#### 动态NAT配置示例

如下图所示，在R1上配置动态NAT将内网主机的私有地址动态映射到公有地址

![image-20240509210303146](https://img.yatjay.top/md/image-20240509210303146.png)

配置命令如下：

```cmd
[R1]nat address-group 1 122.1.2.1 122.1.2.3
[R1]acl 2000
[R1-acl-basic-2000]rule 5 permit source 192.168.1.0  0.0.0.255
[R1-acl-basic-2000]quit
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]nat outbound 2000 address-group 1 no-pat //意思是将匹配acl 2000的IP地址转换为nat地址池1中的公网地址，且只进行一对一的IP地址转换，不进行端口转换NAPT
```

### NAPT和Easy-IP

#### NAPT

##### NAPT原理

动态NAT选择地址池中的地址进行地址转换时不会转换端口号，即No-PAT（No-Port Address Translation，非端口地址转换）公有地址与私有地址还是1:1的映射关系，无法提高公有地址利用率。

NAPT（Network Address and Port Translation，网络地址端口转换）：从地址池中选择地址进行地址转换时不仅转换IP地址，同时也会对端口号进行转换，从而实现公有地址与私有地址的1:n映射，可以有效提高公有地址利用率。即一个公网IP地址可以供多个私网IP地址使用，具体方式是将不同私网IP地址映射到该公网IP的不同端口，这是实际项目使用最多的技术

![image-20240509210829744](https://img.yatjay.top/md/image-20240509210829744.png)

##### NAPT转换示例

去程如下图所示：

![image-20240509211003120](https://img.yatjay.top/md/image-20240509211003120.png)

回程如下图所示：

![image-20240509211041897](https://img.yatjay.top/md/image-20240509211041897.png)

##### NAPT配置示例

如下图所示，在R1上配置NAPT让内网所有私有地址通过122.1.2.1访问公网。

![image-20240509211105053](https://img.yatjay.top/md/image-20240509211105053.png)

配置命令如下：

```cmd
[R1]nat address-group 1 122.1.2.1 122.1.2.1    //NAT地址池仅定义了1个公网IP
[R1]acl 2000
[R1-acl-basic-2000]rule 5 permit source 192.168.1.0 0.0.0.255
[R1-acl-basic-2000]quit
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]nat outbound 2000 address-group 1   //此条命令最后不加no-pat，表示使用NAPT转换
```

#### Easy IP

##### Easy IP原理

Easy IP：实现原理和NAPT相同，同时转换IP地址、传输层端口，区别在于**EasyIP没有地址池的概念，使用接口地址作为NAT转换的公有地址。**即将所有私有IP地址：端口号转换为出口IP：端口号，即出接口的NAT

EasyIP适用于不具备固定公网IP地址的场景：如通过DHCP、PPPoE拨号获取地址的私有网络出口，可以直接使用获取到的动态地址进行转换。

![image-20240509211410535](https://img.yatjay.top/md/image-20240509211410535.png)

##### Easy IP配置示例

如下图所示，在R1上配置EaSy-IP让内网所有私有地址通过122.1.2.1访问公网

![image-20240509211616934](https://img.yatjay.top/md/image-20240509211616934.png)

配置命令如下：

```cmd
//无需定义NAT地址池
[R1]acl 2000
[R1-acl-basic-2000]rule 5 permit source 192.168.1.0 0.0.0.255 
[R1-acl-basic-2000]quit
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]nat outbound 2000  //直接匹配ACL，无需指定NAT地址池
```

### NAT Server(端口映射)

##### NAT Server使用场景

NATServer：指定**[公有地址：端口]**与**[私有地址：端口]**的一对一映射关系，**将内网服务器映射到公网**，当私有网络中的服务器需要对公网提供服务时使用。

外网主机主动访问实现对内网服务器的访问。当外网用户访问该**[公有地址：端口]**时，配置了端口映射的端口会将此请求映射至内网的**[私有地址：端口]**，访问其他端口时则不进行转换，无法访问到

![image-20240509211903810](https://img.yatjay.top/md/image-20240509211903810.png)

##### NAT Server端口映射转换示例

![image-20240509211941879](https://img.yatjay.top/md/image-20240509211941879.png)

##### NAT Server端口映射配置示例

如下图所示，在R1上配置NATServer将内网服务器192.168.1.10的80端口映射到公有地址122.1.2.1的8080端口。

![image-20240509212043147](https://img.yatjay.top/md/image-20240509212043147.png)

配置命令如下：

```cmd
[R1]interface GigabitEthernet0/0/1
[R1-GigabitEthernet0/0/1]ip address 122.1.2.1 24
[R1-GigabitEthernet0/0/1]nat server protocol tcp global 202.10.10.1 www inside 192.168.1.1 8080  //含义：使用nat server端口映射功能，协议类型TCP，指定公网IP：端口和私网IP：端口。记住这句关键命令
```

### NAT配置命令小结



| 技术       | 关键命令                                                     | 含义                                                        | 备注                 |
| ---------- | ------------------------------------------------------------ | ----------------------------------------------------------- | -------------------- |
| 静态NAT    | `[R1-GigabitEthernet0/0/1]nat static global122.1.2.3 inside 192.168.1.3` | 配置静态NAT表项                                             | 静态<br/>一对一      |
| 动态NAT    | `[R1-GigabitEthernet0/0/1]nat outbound 2000 address-group 1 no-pat ` | 配置动态NAT，且只进行一对一的IP地址转换，不进行端口转换NAPT | 动态<br/>一对一      |
| 动态NAPT   | `[R1-GigabitEthernet0/0/1]nat outbound 2000 address-group 1   ` | 配置动态NAPT，IP和端口都参与转换                            | 动态<br/>二对二      |
| Easy IP    | `[R1-GigabitEthernet0/0/1]nat outbound 2000`                 | 私网IP都转换为出口IP地址。直接匹配ACL，无需指定NAT地址池    | 动态<br/>多对一 : 多 |
| NAT Server | `[R1-GigabitEthernet0/0/1]nat server protocol tcp global 202.10.10.1 www inside 192.168.1.1 8080  ` | 端口映射，将私网服务映射至公网                              | 端口映射提供服务     |

### NAPT配置实验

![image-20240509213338512](https://img.yatjay.top/md/image-20240509213338512.png)

1. 为各个设备以及接口配置如图中所示的IP地址，







2. 假设AR2和AR3之间执行OSPF动态路由协议，为二者配置OSPF

​	AR2上配置OSPF

```cmd
[AR2]ospf 1   //进入OSPF 1
[AR2-ospf-1]area 0  //进入骨干区域area 0
[AR2-ospf-1-area-0.0.0.0]network 12.1.1.0 0.0.0.255  //宣告网络
[AR2-ospf-1-area-0.0.0.0]network 23.1.1.0 0.0.0.255  //宣告网络
```

> 命令含义解释：
>
> 这些命令是在华为路由器上配置OSPF（Open Shortest Path First，开放式最短路径优先）协议的示例。下面是对每条命令含义的解释：
>
> 1. `[AR2]ospf 1`：这条命令用来**启用OSPF进程，并指定进程号为1**。OSPF进程号是一个本地标识符，用于区分同一设备上的多个OSPF实例，进程号可以是1到65535之间的任意整数。
>
> 2. `[AR2-ospf-1]area 0`：**在OSPF进程中创建一个区域，区域ID为0**。**区域0通常被称为骨干区域（backbone area），是所有其他区域必须连接的区域，用以交换外部路由信息**。区域ID用点分十进制表示，区域0写作`0.0.0.0`。
>
> 3. `[AR2-ospf-1-area-0.0.0.0]network 12.1.1.0 0.0.0.255`：**在区域0中宣告一个网络。**这条命令指定了网络地址`12.1.1.0`，**掩码`0.0.0.255`（等同于`255.255.255.0`）**，意味着宣告了从`12.1.1.1`到`12.1.1.254`的整个网段。这意味着路由器接口配置在这个网段的IP地址会参与OSPF的路由计算。
>
> 4. `[AR2-ospf-1-area-0.0.0.0]network 23.1.1.0 0.0.0.255`：同上，这是在区域0中额外宣告的一个网络。网络地址为`23.1.1.0`，同样使用`255.255.255.0`作为子网掩码，宣告了从`23.1.1.1`到`23.1.1.254`的网段参与OSPF。
>
> 综上所述，这些命令完成了在路由器AR2上配置OSPF协议，定义了一个OSPF进程，声明了骨干区域，并在该区域内宣告了两个网络，以便这些网络的路由信息能够通过OSPF协议在OSPF域内传播。

​	AR3上配置OSPF

```cmd
[AR3]ospf 1   //进入OSPF 1
[AR3-ospf-1]area 0  //进入骨干区域area 0
[AR3-ospf-1-area-0.0.0.0]network 23.1.1.0 0.0.0.255  //宣告网络
```

​	在AR3上查看路由表，确认OSPF是否成功运行

```
[AR3]diaplay ip routing-table
```

​	显示出了AR2左侧直连网络说明OSPF配通

图

3. 在出口路由器配置NAT地址转换

​	未配置NAT，测试使用PC去ping AR3可见ping不通，因为私网地址无法在出口路由器以外进行路由。

图



因此我们需要在AR1上配置NAPT

第一步：配置公网IP地址池12.1.1.2-12.1.1.5

```cmd
[AR1]nat address-group 1 12.1.1.2 12.1.1.5
```

第二步：配置ACL规则匹配需要转换的私有地址

```cmd
[AR1]acl 2000
[AR1-acl-basic-2000]rule 10 permit source 192.168.1.0 0.0.0.255
[AR1-acl-basic-2000]q
[AR1]
```

第三步：在路由器出接口上配置NAPT规则(含ACL匹配)

```cmd
[AR1]int g0/0/1
[AR1-GigabitEthernet0/0/1]nat outbound acl 2000 address-group 1  //outbound后跟的是源IP(私网IP)和目的IP(公网IP)，且不加no-pat，表示IP和端口都要参与映射
```

在AR1上查看nat配置，如下图所示可见接口G0/0/1上的NAT已经生效

```cmd
[AR1]display nat outbound
```

```cmd
[AR1]display nat session  //该命令查看NAT映射表，华为模拟器无法执行该命令，机器上可以
```

再次测试使用PC去ping AR3可见仍然不能ping通，因为AR1的出接口并没有到达AR3的路由，

图

我们在AR1上配置一条默认路由如下，使得AR1能够知道到达AR3的下一跳地址

```cmd
[AR1]ip route-static 0.0.0.0 12.1.1.2  //配置一条默认路由，指向12.1.1.2
```

再次测试使用PC去ping AR3可见能够ping通

图



`ping -t`持续ping，在AR2上抓包查看数据包的源IP地址，可见ping来的数据包源IP已经转换为了公网IP地址

图；另外还有OSPF报文，其目的地址为特殊的`240.0.0.5`



## 网络质量探测技术

BFD和NQA网络质量探测技术，常用来作为备份链路的执行条件，如默认走电信出口链路，如果探测到电信出口故障，切换至联通出口；如默认走有限链路，如果探测到有限链路故障，切换至无线访问

### BFD监测网络状态

#### BFD概述

BFD（Bidrectional Forwarding Detection双向转发检测）用于快速检测系统设备之间的发送和接受两个方向的通信故障，并在出现故障时通知生成应用。BFD广泛用于链路故障检测，并能实现与接口、静态路由、动态路由等联动检测。bfd使用的默认组播地址（默认就是224.0.0.184）。

#### BFD配置示例

如下图所示，假设R1上配置了默认路由

```
[R1]ip route-static 2.2.2.0 24 10.1.12.2
```

![image-20240509222456578](https://img.yatjay.top/md/image-20240509222456578.png)

某一时刻，R2的GE0/0/0端口损坏不通，希望R1能在不通时删掉上述静态路由。此时可在R1上配置BFD检测对端状态。即在R1-R2之间部署BFD来检测对端的状态。

```cmd
[R1]ip route-static 2.2.2.0 24 10.1.12.2 track bfd-session bfd12
```

![image-20240509222621890](https://img.yatjay.top/md/image-20240509222621890.png)

**若对端不支持BFD，也可以通过BFD单臂回声监测对端状态**，模拟器无法实现单臂回声

BFD详细配置项如下所示：

![image-20240509224633683](https://img.yatjay.top/md/image-20240509224633683.png)

#### 实验解析BFD工作

实验拓扑如下图所示

![image-20240509223000393](https://img.yatjay.top/md/image-20240509223000393.png)

1. 配置各接口IP地址，注意为R2的LoopBack0接口配置IP地址，模拟其直连网络

   环回地址配置如下所示：

```cmd
[AR2]int lo0
[AR2-LoopBack0]ip address 2.2.2.2 24
```

2. 配置路由：现在AR1无法ping通2.2.2.2，因为没有路由，我们手动为AR1配置一条到达2.2.2.2的静态路由。配置之后即可ping通2.2.2.2

```
[AR1]ip route-static 2.2.2.0 255.255.255.0 12.1.1.1.2
```

3. 将AR2的GE0/0/0接口shutdown，则AR1无法ping通2.2.2.2

```cmd
[AR2]int g0/0/0
[AR1-GigabitEthernet0/0/0]shutdown
```

4. 此时查看AR1的路由表`diaplay ip routing-table`，前面配置的静态路由仍然存在

```cmd
[AR1]display ip routing-table
```

5. 将AR2的GE0/0/0接口恢复，则AR1可以再次ping通2.2.2.2



6. 为了实现自动监测对端状态，可以配置BFD探测对端状态，一旦对端不通，则删除相应路由

   R1上配置如下

```cmd
[AR1]bfd  R1R2 bind peer-ip 12.1.1.2 source-ip 12.1.1.1 auto
//开启bfd名为R1R2，绑定对端IP为12.1.1.2，源IP为12.1.1.1，auto表示通信中间的本地标识符等参数自动协商，不用再手动配置
[AR1-bfd-session-r1r2]commit  //使用commit关键字使得bfd生效
```

R2上配置如下

```cmd
[AR2]bfd  R1R2 bind peer-ip 12.1.1.1 source-ip 12.1.1.2 auto  //即将上面的对端IP和源IP对调
[AR2-bfd-session-r1r2]commit  //使用commit关键字使得bfd生效
```

`diaplay bfd session all`查看bfd状态，up意为已经启动bfd

```cmd
[AR2]diaplay bfd session all
```

**现在R1和R2在互相通信各自BFD状态，一旦一方不通，BFD状态即变为down**

7. 在AR1静态路由中配置BFD跟踪：如果bfd检测到对端已经不通，就删除对应路由表项


```cmd
[AR1]undo ip route-static 2.2.2.0 255.255.255.0 12.1.1.1.2  
//删除当前静态路由配置，否则执行下面命令会因路由表项冲突而报错
[AR1]ip route-static 2.2.2.0 255.255.255.0 12.1.1.1.2 track bfd-session  r1r2
 //为该条路由跟踪BFD状态，一旦对端不通，R1的BFD就为down状态，该条静态路由就失效了，就删除对应路由表项
```

这时查看路由表，可见静态路由已经在路由表中





如果此时挂掉R2的GE0/0/0口，`display bfd session all`查看R1的BFD状态，可见其已经为down状态





再`display ip routing-table`查看R1路由表，可见上述静态路由表项已被删除



### NQA监测网络状态

#### NQA概述

NQA（NetworkQualityAnalysis，网络质量分析）是系统提供的一个特性，位于链路层之上，覆盖网络层、传输层、应用层，独立于底层硬件，可实现实时监视网络性能状况，在网络发生故事进行故障诊断和定位。

NQA通过发送测试报文，对网络性能或服务质量进行分析，NQA支持的测试包括多种协议，例如http的延迟、TCP延迟、DNS错误、ICMP消息等，下面以ICMP为例简单

#### NQA配置示例

![image-20240509230355790](https://img.yatjay.top/md/image-20240509230355790.png)

AR1配置如下：

![image-20240509230422818](https://img.yatjay.top/md/image-20240509230422818.png)

AR2配置如下：

![image-20240509230446053](https://img.yatjay.top/md/image-20240509230446053.png)

在AR1上使用检测命令`display nqa results`，可以看到测试结果，看到网络性能

![image-20240509230609031](https://img.yatjay.top/md/image-20240509230609031.png)

![image-20240509230620351](https://img.yatjay.top/md/image-20240509230620351.png)

![image-20240509230646095](https://img.yatjay.top/md/image-20240509230646095.png)

静态路由或默认路由后面可以trackBFD，也可以track NQA。

`ip route-static 0.0.0.0 0.0.0.0 12.1.1.2 track nqa root icmp` //在主默认路由上挂接NQA

##  掩码和反掩码使用总结

| 掩码/反掩码        | 使用场景          | 示例                                                         |
| ------------------ | ----------------- | ------------------------------------------------------------ |
| 使用掩码的场景     | IP地址强相关      | - 场景—：IP地址配置 <br/>`ip address 192.168.1.1 255.255.255.0` 或`ip address 192.168.1.1 24`<br/>- 场景二：DHCP配置 <br/>`network 192.168.1.0 mask 255.255.255.0`或`network 192.168.1.0 mask 24` |
| 使用反掩码的场景   | ACL、OSPF路由宣告 | - 场景—: ACL<br/>`rule 10 permit source 192.168.1.1 0` 或 `rule 10 permit source 192.168.1.1 0.0.0.0` 或`rule 10 permit source 192.168.1.0 0.0.0.255`<br/>- 场景二：OSPF路由宣告<br/>`network 192.168.1.0 0.0.0.255`  //宣告192.168.1.0网段 |
| 不需要掩码或反掩码 | RIP路由宣告       | RIP路由宣告不需要掩码或反掩码，只需要宣告主类网络（ABC类主类IP地址掩码分别为/8/16/24）：<br/>- `network 10.0.0.0`<br/>- `network 172.16.0.0`<br/>- `network 192.168.1.0` |

