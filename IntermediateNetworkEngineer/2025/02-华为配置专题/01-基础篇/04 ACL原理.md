## 04 ACL原理

### ACL概述

#### ACL技术背景

技术背景：需要一个工具，实现流量过滤

某公司为保证财务数据安全，禁止研发部门访问财务服务器，但总裁办不受限制，

![image-20250326220648864](https://img.yatjay.top/md/20250326220648910.png)

#### ACL概述

ACL是由一系列`permit`或`deny`语句组成的、有序规则的列表。

ACL是一个匹配工具，能够对报文进行匹配和区分

![image-20250326220743455](https://img.yatjay.top/md/20250326220743478.png)

#### ACL应用

- 匹配IP流量
- 在Traffic-filter中被调用
- 在NAT （Network Address Translation ）中被调用
- 在路由策略中被调用
- 在防火墙的策略部署中被调用
- 在QoS中被调用
- 其他

### ACL工作原理

#### ACL的组成

- ACL由若干条`permit`或`deny`语句组成.

- 每条语句就是该ACL的一条规则，每条语句中的`permit`或`deny`就是与这条规则相对应的处理动作。

![image-20250326220936127](https://img.yatjay.top/md/20250326220936167.png)

在华为交换机上：

- 做流量过滤时最后一条默认隐藏的为`permit`
- 做路由时，最后一条默认隐藏的为`deny`

##### 规则编号

规则编号与步长

- 规则编号（RuleID）：一个ACL中的每一条规则都有一个相应的编号。
- 步长（Step）：步长是系统自动为ACL规则分配编号时，每个相邻规则编号之间的差值，缺省值为5。步长的作用是为了方便后续在旧规则之间，插入新的规则。
- Rule ID分配规则：系统为ACL中首条未手工指定编号的规则分配编号时，使用步长值（例如步长=5，首条规则编号为5）作为该规则的起始编号；为后续规则分配编号时，则使用大于当前ACL内最大规则编号且是步长整数倍的最小整数作为规则编号。

![image-20250326221018372](https://img.yatjay.top/md/20250326221018395.png)

如果希望增加1条规则，该如何处理？

```cmd
rule 11 deny source 10.1.1.3 0
```

![image-20250326221153261](https://img.yatjay.top/md/20250326221153297.png)

##### 通配符

通配符(Wildcard)

- 通配符是一个32比特长度的数值，用于指示IP地址中哪些比特位需要严格匹配，哪些比特位无需匹配
- 通配符通常采用类似网络掩码的点分十进制形式表示，但是含义却与网络掩码完全不同。

![image-20250326221217982](https://img.yatjay.top/md/20250326221218011.png)

匹配规则：<font color="red">"0"表示“匹配”；“1"表示“随机分配"</font>

###### 匹配网段

如何匹配192.168.1.1/24对应网段的地址?

![image-20250326221307740](https://img.yatjay.top/md/20250326221307784.png)

###### 匹配奇偶数IP地址

匹配192.168.1.0/24这个子网中的奇数IP地址，例如192.168.1.1、192.168.1.3、192.168.1.5等。

奇数IP地址的特点是其最后一个比特位为`1`，因此我们需要设计ACL规则来匹配这个特性。

**通配符掩码的设计** :

- 对于需要严格匹配的部分，通配符掩码的对应位为`0`。
- 对于不需要严格匹配的部分，通配符掩码的对应位为`1`。

因此，针对192.168.1.0/24子网中的奇数IP地址：

- IP地址部分：`192.168.1.1`
- 通配符掩码：`0.0.0.254`（最后一个字节为`254`，即二进制`11111110`，表示最低有效位必须为`1`，其余位可以任意变化。）

![image-20250326221351608](https://img.yatjay.top/md/20250326221351643.png)

###### 特殊的通配符

- 精确匹配`192.168.1.1`这个IP地址：`192.168.1.1 0.0.0.0 = 192.168.1.1 0`
- 匹配任意IP地址：`0.0.0.0 255.255.255 = any`

#### ACL分类

##### 基于ACL规则定义方式的分类

| 分类                             | 编号范围                           | 规则定义描述                                                 |
| -------------------------------- | ---------------------------------- | ------------------------------------------------------------ |
| <font color="red">基本ACL</font> | <font color="red">2000~2999</font> | <font color="red">仅使用报文的**源IP地址**、分片信息和生效时间段信息来定义规则。</font> |
| <font color="red">高级ACL</font> | <font color="red">3000~3999</font> | <font color="red">可使用IPv4报文的源IP地址、目的IP地址、IP协议类型、ICMP类型、TCP源/目的端口号、UDP源/目的端口号、生效时间段等来定义规则。</font> |
| 二层ACL                          | 4000~4999                          | 使用报文的以太网帧头信息来定义规则，如根据源MAC地址、目的MAC地址、二层协议类型等。 |
| 用户自定义ACL                    | 5000~5999                          | 使用报文头、偏移位置、字符串掩码和用户自定义字符串来定义规则。 |
| 用户ACL                          | 6000~6999                          | 既可使用IPv4报文的源IP地址或源UCL（User Control List）组，也可使用目的IP地址或目的UCL组、IP协议类型、ICMP类型、TCP源端口/目的端口、UDP源端口/目的端口号等来定义规则。 |

##### 基于ACL标识方法的分类

| 分类      | 规则定义描述                                                |
| --------- | ----------------------------------------------------------- |
| 数字型ACL | 传统的ACL标识方法。创建ACL时，指定一个唯一的数字标识该ACL。 |
| 命名型ACL | 通过名称代替编号来标识ACL。                                 |

##### 基本ACL和高级ACL

###### 基本ACL：编号范围2000-2999

![image-20250326221623698](https://img.yatjay.top/md/20250326221623734.png)

`source 10.1.1.0 0.0.0.255`匹配的是`10.0.0.0~10.1.1.255`

###### 高级ACL：编号范围3000-3999

![image-20250326221637174](https://img.yatjay.top/md/20250326221637211.png)

- rule 5：允许源IP地址为`10.1.1.0~10.1.1.255`，目的IP地址为`10.1.3.0~10.1.3.255`的报文
- rule 10：允许源IP地址为`10.1.2.0~10.1.2.255`，目的IP地址为`10.1.3.0~10.1.3.255`，且目的端口为TCP的`21`端口即FTP协议的报文通过

#### ACL的匹配机制

![image-20250326221713828](https://img.yatjay.top/md/20250326221713872.png)

##### ACL的匹配顺序及匹配结果

配置顺序（config模式）：系统按照ACL规则编号从小到大的顺序进行报文匹配，规则编号越小越容易被匹配。

![image-20250326221806132](https://img.yatjay.top/md/20250326221806172.png)

- rule 1：允许源IP地址为`192.168.1.1`的报文
- rule 2：允许源IP地址为`192.168.1.2`的报文
- rule 3：拒绝源IP地址为`192.168.1.3`的报文
- rule 4：允许其他所有IP地址即`0.0.0.0`的报文，`192.168.1.4`和`192.168.1.5`正是匹配此条规则通过

问题：“允许”是指允许流量通过吗？

回答：不是。ACL只是一种基础的匹配，匹配到之后具体要做什么操作要看其他工具怎么使用该条匹配到的报文，如用来控制报文优先级、是否允许该条流量是否通过

#### ACL的应用位置

![image-20250326221836000](https://img.yatjay.top/md/20250326221836040.png)

##### 入站 (Inbound)及出站 (Outbound)方向

**入站（Inbound）**：指数据流**进入设备接口** 的方向，即从外部网络进入设备时触发的过滤。

**应用场景** ：过滤从外部进入设备的流量（如限制外部用户访问内部服务器）。

**示例** ：

```cmd
interface GigabitEthernet0/0/1
traffic-filter inbound acl 2000  // 过滤进入接口的数据
```

**出站（Outbound）**：指数据流**离开设备接口** 的方向，即从设备发出到外部网络时触发的过滤。

**应用场景** ：控制设备对外部网络的访问（如限制内部用户访问特定外网）。

**示例** ：

```cmd
interface GigabitEthernet0/0/1
traffic-filter outbound acl 3000  // 过滤离开接口的数据
```

![image-20250326221917299](https://img.yatjay.top/md/20250326221917345.png)

### ACL配置及应用

#### ACL基础配置命令

##### 创建基本ACL

`acl 自定义编号`

```cmd
[Huawei] acl [number] acl-number [match-order config]
```

使用编号（2000～2999）创建一个数字型的基本ACL，并进入基本ACL视图。

```cmd
[Huawei] acl name acl-name{basic|acl-number}[match-order config]
```

使用名称创建一个命名型的基本ACL，并进入基本ACL视图。

##### 配置基本ACL的规则

```cmd
[Huawei-acl-basic-2ooo] rule [ rule-id ] { deny | permit }[ source { source-address source-wildcard | any }| time-range time-name ]
```

在基本ACL视图下，通过此命令来配置基本ACL的规则。

#### 基础案例：使用基本ACL过滤数据流量

配置需求：在Router上部署基本ACL后，ACL将试图穿越Router的源地址为192.168.1.0/24网段的数据包过滤掉，并放行其他流量，从而禁止192.168.1.0/24网段的用户访问Router右侧的服务器网络。

![image-20250326222500699](https://img.yatjay.top/md/20250326222500736.png)

1. Router已完成iP地址和路由的相关配置

2. 在Router上创建基本ACL，禁止`192.168.1.0/24`网段访问服务器网络:

   ```cmd
   [Router] acl 2000
   [Router-acl-basic-2000] rule deny source 192.168.1.0 0.0.0.255
   [Router-acl-basic-2000] rule permit source any
   ```

3. 由于从接口`GE0/0/1`进入Router，所以在接口`GE0/0/1`的入方向配置流量过滤：

   ```cmd
   [Router] interface GigabitEthernet 0/0/1
   [Router-GigabitEthernet0/0/1] traffic-filter inbound acl 2000  //为接口的traffic-filter流量过滤器入站时应用acl 2000规则
   [Router-GigabitEthernet0/0/1] quit
   ```

#### 高级ACL命令

##### 创建高级ACL

```cmd
[Huawei] acl [ number ] acl-number [ match-order config ]
```

使用编号（3000～3999）创建一个数字型的高级ACL，并进入高级ACL视图。

```cmd
[Huawei] acl name acl-name { advance | acl-number } [ match-order config ]
```

使用名称创建一个命名型的高级ACL，进入高级ACL视图。

##### 配置基本ACL的规则

根据IP承载的协议类型不同，在设备上配置不同的高级ACL规则。对于不同的协议类型，有不同的参数组合。

当参数protocol为IP时，高级ACL的命令格式为

```cmd
rule [ rule-id ]{ deny I permit }ip [ destination { destination-address destination-wildcard | any }| source { source-address source-wildcard | any }| time-range time-name I [ dscp dscp I [tos tos | precedence precedence ]]] 
```

在高级ACL视图下，通过此命令来配置高级ACL的规则。

当参数protocol为TCP时，高级ACL的命令格式为

```cmd
rule [ rule-id ]{ deny | permit } protocol-number | tcp }[ destination { destination-address destination-wildcard | any }| destination-port { eq port | gt port | It port | range port-start port-end }| source {source-address source-wildcard | any }| source-port {eq port | gt port | It port | range port-start port-end }| tcp-flag { ack | fin | syn }* | time-range time-name ］ *
```

在高级ACL视图下，通过此命令来配置高级ACL的规则。

#### 进阶案例：使用高级ACL限制不同网段的用户互访1

配置需求：某公司通过Router实现各部门之间的互连。为方便管理网络，管理员为公司的研发部和市场部规划了两个网段的IP地址。现要求Router能够限制两个网段之间互访(两个网段之间不能互访)，防止公司机密泄露。

![image-20250326222841634](https://img.yatjay.top/md/20250326222841682.png)

1. Router已完成IP地址和路由的相关配置

2. 创建高级ACL3001并配置ACL规则，拒绝研发部访问市场部的报文:

   ```cmd
   [Router] acl 3001
   [Router-acl-adv-3001] rule deny ip source 10.1.1.0 0.0.0.255 destination 10.1.2.0 0.0.0.255
   [Router-acl-adv-3001] quit
   ```

3. 创建高级ACL3002并配置ACL规则，拒绝市场部访问研发部的报文:

   ```cmd
   [Router] acl 3002
   [Router-acl-adv-3002] rule deny ip source 10.1.2.0 0.0.0.255 destination
   10.1.1.0 0.0.0.255
   [Router-acl-adv-3002] quit
   ```

4. 由于研发部和市场部互访的流量分别从接口`GE0/0/1`和`GE0/0/2`进入Router，所以在接口`GE0/0/1`和`GE0/0/2`的入方向配置流量过滤：

   ```cmd
   [Router] interface GigabitEthernet 0/0/1
   [Router-GigabitEthernet0/0/1] traffic-filter inbound acl 3001
   [Router-GigabitEthernet0/0/1] quit
   
   [Router] interface GigabitEthernet 0/0/2
   [Router-GigabitEthernet0/0/2] traffic-filter inbound acl 3002
   [Router-GigabitEthernet0/0/2] quit
   ```

### 思考题

（单选）下列选项中，哪一项才是一条合法的**基本ACL**的规则?基本ACL必有源IP地址，不能配置TCP，选择C

A. rule permit ip  

B. rule deny ip  

**C. rule permit source any**  

D. rule deny tcp source any  

高级ACL可以基于哪些条件来定义规则？

1. 源目IP
2. 源目端口号
3. 协议号
4. ……

### 章节总结

ACL是一种应用非常广泛的网络技术。它的基本原理是：配置了ACL的网络设备根据事先设定好的报文匹配规则对经过该设备的报文进行匹配，然后对匹配上的报文执行事先设定好的处理动作。这些匹配规则及相应的处理动作是根据具体的网络需求而设定的。处理动作的不同以及匹配规则的多样性，使得ACL可以发挥出各种各样的功效。

ACL技术总是与防火墙、路由策略、QoS、流量过滤等其他技术结合使用。

在本章节中，主要介绍了ACL的相关技术知识，包括：ACL的作用，ACL的组成、匹配和分类、通配符的使用方法，以及ACL的基本配置及应用。