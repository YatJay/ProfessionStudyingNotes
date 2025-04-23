## ARP和RARP

### 为何需要ARP

数据链路层在进行数据封装时，需要目的MAC地址。数据封装时，只知道本机IP地址、本机MAC地址、目的IP地址，不知道目的MAC地址。因此需要<font color="red">通过ARP协议根据IP地址获取MAC地址</font>

![image-20250226193511143](https://img.yatjay.top/md/20250226193511176.png)

### ARP协议工作过程(5步)

例如，主机A要向主机C发送数据

主机A发送数据包给主机C之前，首先通过<font color="red">ARP协议根据IP地址获取主机MAC地址</font>

![image-20250226193658223](https://img.yatjay.top/md/20250226193658254.png)

#### 查本地：查询本地ARP缓存

查看主机A的ARP缓存`arp -a`，发现主机A的ARP缓存中没有主机C的MAC地址

![image-20250226193720700](https://img.yatjay.top/md/20250226193720735.png)

可在本机查看到本地的ARP表，ARP表共有IP地址、MAC地址、类型这三个字段

类型字段为“动态”，说明该条目是通过ARP协议学习到的

类型字段为“静态”，说明该条目是手动绑定的

![image-20250226194512131](https://img.yatjay.top/md/20250226194512172.png)

#### 广播：ARP请求

若本地没有所需ARP缓存，需要广播发送ARP请求

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

#### 其他更新(学习)：其他主机更新ARP缓存

其他主机收到ARP请求报文后，首先更新自己的ARP缓存

即将ARP请求报文中的源IP、源MAC写入自己的ARP缓存表中

![image-20250226195441008](https://img.yatjay.top/md/20250226195441045.png)

#### 回复：其他主机ARP响应

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

#### 本机更新：本机更新ARP缓存

主机A收到ARP响应报文后即更新自己的ARP缓存，类型字段为动态Dynamic

![image-20250226195537477](https://img.yatjay.top/md/20250226195537517.png)

### ARP协议工作过程概述

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

### ARP泛洪攻击

#### 影响

![image-20250423223357345](https://img.yatjay.top/md/20250423223357381.png)

用户通过Switch A和Switch B接入到Gateway访问Internet。当网络中出现过多的ARP报文时:

1. 会导致<font color="red">网关设备CPU负载加重</font>，影响设备正常处理用户的其它业务。
2. 过多的ARP报文会<font color="red">占用大量的网络带宽</font>，引起网络堵塞，从而影响整个网络通信的正常运行。
3. <font color="red">耗尽网关设备ARP表项</font>，不能为正常用户提供服务。

#### 现象
1. 交换机CPU占有率较高，正常用户不能学习ARP甚至无法上网。
2. Ping不通。
3. 网络设备不能管理。

#### 攻击溯源分析

由于终端中毒频繁发送大量ARP报文——<font color="red">中毒</font>：特征是中毒设备伪造了大量随机MAC地址

下挂网络成环产生大量的ARP报文——<font color="red">环路</font>：特征是同一个MAC地址的数据帧引起二层环路，进而导致交换机MAC地址表震荡

#### 解决方法

1、<font color="red">在接口下配置ARP表项限制</font>。没备支持接口下的ARP表项限制，如果接口已经学习到的ARP表项数目超过限制值，系统不再学习新的ARP表项，但不会清除已经学习到的ARP表项，会提示用户删除超出的ARP表项。

```cmd
<HUAWEI> system-view
[HUAWEl] interface gigabitethernet 1/0/1
[HUAWEl-GigabitEthernet1/0/1] arp-limit vlan 10 maximum 20 //该接口下VLAN10只能学20个表项
```

2、<font color="red">配置针对源IP地址的ARP报文速率抑制的功能</font>。在一段时间内，如果设备收到某一源IP地址的ARP报文数目超过设定阈值，则不处理超出阈值部分的ARP请求报文。

```cmd
<HUAWEI> system-view
[HUAWEl] arp speed-limit source-ip 10.0.0.1 maximum 50  //对ARP报文进行时间戳抑制的抑制速率为
50pps，即每秒处理50个ARP报文。
```

3、根据排查出的攻击源MAC配置<font color="red">黑名单过滤掉攻击源发出的ARP报文</font>，注意配置的是二层ACL

```cmd
[HUAWEl] acl 4444
[HUAWEl-acl-L2-4444] rule permit I2-protocol arp source-mac 0-0-1 vlan-id 193
[HUAWEl-acl-L2-4444] quit
[HUAWEl] cpu-defend policy policy1
[HUAWEl-cpu-defend-policy-policy1] blacklist 1 acl 4444
[HUAWEl-cpu-defend-policy-policy1] quit
```

接下来应用防攻击策略policy1，关于防攻击策略的应用：

```cmd
<HUAWEI> system-view
[HUAWEl] cpu-defend-policy policy1
[HUAWEI] quit
```

4、如果上述配置无法解决，比如源MAC变化或源IP变化的ARP攻击源来自某台接入设备下挂用户，在接入侧交换机上配置流策略限速，不让该接入侧设备下的用户影响整个网络。

```cmd
[HUAWEl] acl 4445
[HUAWEl-acl-L2-4445] rule permit I2-protocol arp
[HUAWEI-acl-L2-4445] quit
[HUAWEl] traffic classifier policy1
[HUAWEl-classifier-policy1] if-match acl 4445
[HUAWEI-classifier-policy1] quit
[HUAWEl] traffic behavior policy1
[HUAWEl-behavior-policy1] car cir 32 //保证ARP平均速率只能32kbit/s
[HUAWEl-behavior-policy1] quit
[HUAWEl] traffic policy policy1 
[HUAWEl-trafficpolicy-policy1] classifier policy1 behavior policyl
[HUAWEl-trafficpolicy-policy1] quit
[HUAWEl] interface GigabitEthernet 1/0/1
[HUAWEl-GigabitEthernet1/0/1] traffic-policy policy1 inbound 
```

### ARP欺骗攻击

![image-20250423224306814](https://img.yatjay.top/md/20250423224306865.png)

UserA、UserB、UserC上线之后，通过相互之间交互ARP报文，User A、User B、User C和Gateway上都会创建相应的ARP表项。

此时，如果有攻击者Attacker通过在广播域内发送伪造的ARP报文，篡改Gateway或者User A、User B、UserC上的ARP表项，Attacker可以轻而易举地窃取User A、User B、User C的信息或者阻碍UserA、UserB、UserC正常访问网络。

比如：Attacker冒充网关给User A、User B、User C交互假的MAC地址

#### 现象描述

局域网内用户<font color="red">时通时断</font>，无法正常上网。网络设备会经常<font color="red">脱管</font>，网关设备会打印大量<font color="red">地址冲突</font>的告警。
#### 原因分析

1. 终端中毒。
2. 攻击者将主机地址设为网关地址。

#### 解决方案

| 方法                                              | 原理                                                         | 优点                                                         | 缺点                                                         |
| ------------------------------------------------- | ------------------------------------------------------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| IP-MAC地址绑定                                    | 将设备的IP地址与MAC地址进行静态绑定，形成一种固定的关联关系。这种绑定可以防止未经授权的设备使用特定的IP地址，并在网络中起到防范ARP欺骗攻击的作用 |                                                              | 适合小规模场景或安全性要求较高场景                           |
| <font color="red">配置DAI功能</font>即动态ARP检测 | 在DHCP分配IP地址是，记录分配的IP地址及关联的MAC地址，相当于动态的IP-MAC绑定 | 对ARP报文基于绑定表做合法性检查，非法的报文不会学习ARP，也会丢弃。建议在接入设备部署 | 需要将报文上送到CPU处理，会消耗一定的CPU资源，需要跟DHCP SNOOPING一起使用。 |
| <font color="red">配置网关保护功能</font>         |                                                              | 对仿冒网关的ARP报文直接丢弃，不会转发到其他设备或者网关      | 占用一定的ACL资源，需要在接入设备部署                        |
| 配置黑名单                                        |                                                              | 基于黑名单丢弃上送CPU的报文                                  | 需先查到攻击源                                               |
| 配置黑洞MAC                                       |                                                              | 丢弃该攻击源发的所有报文，包括转发的报文                     | 需先查到攻击源                                               |
| 配置防网关冲突攻击功能                            |                                                              | 收到具有相同源MAC地址的报文直接丢弃                          | 需本设备做网关                                               |

##### 解决方案1

方法一：在交换机上配置黑名单过滤攻击源的源MAC地址。

```cmd
[HUAWEIl] acl 4444
[HUAWEl-acl-L2-4444] rule permit I2-protocol arp source-mac 1-1-1
[HUAWEI-acl-L2-4444] quit
[HUAWEl] cpu-defend policy policy1
[HUAWEl-cpu-defend-policy-policy1] blacklist 1 acl 4444
```

接下来应用防攻击策略policy1，关于防攻击策略的应用。

##### 解决方案2

方法二：更严厉的惩罚措施可以将攻击者的MAC配置成黑洞MAC，彻底不让该攻击者上网。

```cmd
[HUAWEl] mac-address blackhole 1-1-1 vlan3
```

##### 解决方案3

方法三：在交换机上配置防网关冲突功能（该功能要求交换机必须做网关），当设备收到以下ARP报文，认为是攻击，会丢弃。

1. ARP报文的源IP地址与报文入接口对应的VLANIF接口的IP地址相同

2. ARP报文的源IP地址是入接口的虚拟IP地址，但ARP报文源MAC地址不是VRRP虚MAC

   ```cmd
   [HUAWEl]  arp anti-attack gateway-duplicate enable
   ```

##### 解决方案4

动态ARP检测，是线网中最常用的一种方法

方法四：在接入交换机上配置DAI功能（也可以在网关设备上做，但是越靠近用户侧防攻击的效果越好），<font color="red">DAI会将ARP报文上送CPU跟绑定表进行比较，非法的ARP报文直接丢弃</font>，合法的ARP报文才会转发。对于动态用户需要配置dhcp snooping功能，**对于静态用户，需要配置静态绑定表**。

```cmd
[HUAWEI] dhcp enable
[HUAWEl] dhcp snooping enable//全局使能dhcp snooping 功能
[HUAWEl] interface gigabitethernet 0/0/1
[HUAWEI-GigabitEthernet0/0/1] dhcp snooping enable
[HUAWEl-GigabitEthernet0/0/1] arp anti-attack check user-bind enable//用户侧接口使能dhcp snooping以及DAI功能
[HUAWEl] interface gigabitethernet 0/0/24
[HUAWEI-GigabitEthernet0/0/24]dhcp snooping trusted//网络侧端口配置成信任状态
```

##### 解决方案5

方法五：在接入交换机上<font color="red">配置网关保护功能</font>。需要注意的是在连网络侧的端口配置保护网关IP地址。类似DHCP Snoopying避免DHCP响应被伪造

```cmd
[HUAWEl]  interface gigabitethernet 0/0/24
[HUAWEI-GigabitEthernet0/0/24] arp trust source 10.10.10.1//网络侧端口保护网关地址10.10.10.1，老版本的命令行为arp filter source，意思是在只有从0/0/24接口回复的，源地址为10.10.10.1的报文才是可信的
```

#### 例题

![image-20250423230352594](https://img.yatjay.top/md/20250423230352651.png)

解析：两次查看ARP表，发现192.168.5.1的MAC地址变化，即网关的MAC地址在跳，可以判断是ARP欺骗攻击

8. ARP欺骗或ARP攻击
9. CORE2
10. 防ARP欺骗攻击

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