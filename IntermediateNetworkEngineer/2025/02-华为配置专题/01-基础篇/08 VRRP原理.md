## 05 VRRP原理

### VRRP概述与原理

技术背景：单网关面临的问题

![image-20250402213813292](https://img.yatjay.top/md/20250402213813359.png)

 **问题描述**：当网关设备（如Router)发生故障时，所有以该设备为网关的主机将无法与Internet通信。

#### VRRP概述

通过把几台路由设备联合组成一台虚拟的“路由设备”，使用一定的机制保证当主机的下一跳路由设备出现故障时，及时将业务切换到备份路由设备，从而保持通讯的连续性和可靠性。只要两台路由器没有同时挂掉，用户都能够访问到互联网

![image-20250402214000216](https://img.yatjay.top/md/20250402214000272.png)

#### VRRP基本概念

**基本定义** ：  VRRP（Virtual Router Redundancy Protocol）通过将多台路由设备组合成虚拟路由设备，实现网关冗余，保障网络连续性。

![image-20250402214050948](https://img.yatjay.top/md/20250402214051002.png)

![image-20250402214326944](https://img.yatjay.top/md/20250402214326983.png)

| 术语                   | 说明                                                         |
| ---------------------- | ------------------------------------------------------------ |
| **VRRP路由器**         | 运行VRRP协议的路由器（如R1、R2）                             |
| VRRP组                 | VRRP组指一组协同工作的物理路由器通过VRRP协议虚拟成一个逻辑路由器 |
| **VRID**               | VRID：一个VRRP组（VRRP Group）由多台协同工作的路由器（的接口）组成，使用相同的VRID（Virtual Router Identifier，虚拟路由器标识符）进行标识。属于同一个VRRP组的路由器之间交互VRRP协议报文并产生台虚拟"路由器”。一个VRRP组中只能出现一台Master路由器。 |
| **虚拟路由器**         | VRRP为每一个组抽象出一台虚拟“路由器” （Virtual Router），该路由器并非真实存在的物理设备，而是由VRRP虚拟出来的逻辑设备。一个VRRP组只会产生一台虚拟路由器 |
| **虚拟IP/MAC地址**     | 虚拟路由器拥有自己的IP地址以及MAC地址，其中IP地址由网络管理员在配置VRRP时指定，一台虚拟路由器可以有一个或多个IP地址，通常情况下用户使用该地址作为网关地址。而虚拟MAC地址的格式是“0000-5e00-01xx”，其中xx为VRID |
| **Master路由器**       | “Master路由器”在一个VRRP组中承担报文转发任务。在每一个VRRP组中，只有Master路由器才会响应针对虚拟IP地址的ARP Request。Master路由器会以一定的时间间隔周期性地发送VRRP报文，以便通知同一个VRRP组中的Backup路由器关于自己的存活情况 |
| **Backup路由器**       | 也被称为备份路由器。Backup路由器将会实时侦听Master路由器发送出来的VRRP报文，它随时准备接替Master路由器的工作。 |
| **优先级（Priority）** | 优先级值是选举Master路由器和Backup路由器的依据，优先级取值范围0-255，**值越大越优先**，值相等则比较接口IP地址大小，大者优先。 |

### VRRP典型应用

####  VRRP负载分担

通过创建多个虚拟路由器，每个物理路由器在不同的VRRP组中扮演不同的角色，不同虚拟路由器的
VirtualIP作为不同的内网网关地址可以实现流量转发负载分担。

如下图所示通过R1和R2创建了**2个VRRP组即2个虚拟路由器**：

- 在Vrid1组中，R1为主路由器，R2为备份路由器；
- 在Vrid2组中，R2为主路由器，R1为备份路由器；

配置使得PC1、PC2的流量通过Vrid1组转发，PC3的流量通过Vrid2组转发，实现负载均衡

![image-20250402214811615](https://img.yatjay.top/md/20250402214811672.png)

#### VRRP与MSTP结合

**场景说明**：结合多生成树协议（MSTP）实现链路冗余与负载均衡。

通过VRRP和MSTP，VRRP实现三层的负载均衡，MSTP防止环路实现二层负载均衡，实际项目中往往结合使用，现在VRRP和MSTP结合技术已经被堆叠技术取代，因为堆叠之后就没有环路了

![image-20250402214556591](https://img.yatjay.top/md/20250402214556645.png)

如下图所示，SW1、SW2为接入交换机，SW3、SW4为汇聚交换机，为保障链路冗余，我们配置了如下逻辑拓扑，不可避免产生了环路，此时可以使用MSTP（STP的一种）通过阻塞接口的方式“破环”，避免广播风暴。

堆叠技术出现之后，SW1和SW2可以虚拟成1台交换机，SW3、SW4可以虚拟成1台交换机，逻辑上就不存在环路了

![image-20250408213002524](https://img.yatjay.top/md/20250408213002578.png)

#### VRRP监视上行端口

如下图所示，R1为主路由器，当R1的下行端口故障时，SW、PC由于发布出去，能够知道R1故障，但是当R1的出接口GE0/0/1挂掉之后，SW、PC就无从得知R1状态，因此VRRP协议可以监视上行端口状态，感知到上行端口或链路发生故障时，可以主动降低VRRP优先级，从而保证主备路由器的切换，从而保证上行链路正常的Backup设备能够通过选举切换为Master状态，指导报文转发。

实际原理就是GE0/0/1接口需要实时跟踪上面的R链路是否联通，一旦挂掉，立即降低自己VRRP优先级

![image-20250402214642473](https://img.yatjay.top/md/20250402214642526.png)

####  VRRP与BFD联动

BFD（Bidirectional Forwarding Detection，双向转发检测）是一种轻量级、通用的网络协议，用于快速检测网络设备之间的双向路径故障，通常以毫秒级速度实现链路或路由的连通性监测。如下图在R1的GE0/0/0接口和R2的GE0/0/0接口之间配置BFD，两个接口互发类似Hello报文，确保对方存活，能够实现毫秒级的状态存活检测

通过配置VRRP与BFD联动，当Backup设备通过BFD感知故障发生之后，不再等待Master_Down_Timer计
时器超时而会在BFD检测周期结束后立即切换VRRP状态，此时可以实现毫秒级的主备切换。

![image-20250402214716852](https://img.yatjay.top/md/20250402214716906.png)

### VRRP基本配置

#### 常用配置命令

#####  基础配置

1. 创建VRRP备份组并给备份组配置虚拟IP地址

```cmd
[interface-GigabitEtherneto/0/0] vrrp vrid virtual-router-id virtual-ip virtual-address
```

注意：各备份组之间的虚拟IP地址不能重复；同属一个备份组的设备接口需使用相同的VRID。

示例

```cmd
[interface-GigabitEtherneto/0/0] vrrp vrid 10 virtual-ip 192.168.1.254
```



2. 配置路由器在备份组中的优先级

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id priority priority-value
```

注意：通常情况下，Master设备的优先级应高于Backup设备。

示例

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id priority 120
```

3. 配置备份组中设备的抢占延迟时间：

- 在主设备上配置表示当主设备恢复之后，间隔多久后接管原来工作；
- 在从设备上配置表示当主设备挂掉后，从设备间隔多久接管

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id preempt-mode timer delay delay-value
```

4. 配置VRRP备份组中设备采用非抢占模式：当主设备恢复之后，是否主动抢占接管原来工作，默认要抢占

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id preempt-mode disable
```

缺省情况下，抢占模式已被激活。

5. 配置VRRP备份组监视接口：可配置设备当检测到上行接口或链路出现故障时，增加或者减少自身优先级，IP地址拥有者和Eth-trunk成员口不允许配置VRRP监视功能。

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id track interface interface-type interface-number [increased value-increased | reduced value-decreased ]
即
[interface-GigabitEthernet0/0/0] vrrp vrid [虚拟组ID] track interface [接口类型/编号] [increased 增量值 | reduced 减量值]
```

示例

```cmd
# 创建VRRP备份组10，配置虚拟IP为192.168.1.254
[Switch] interface GigabitEthernet0/0/0  
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 virtual-ip 192.168.1.254  
# 设置设备初始优先级为120（默认100）
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 priority 120  
# 跟踪上行接口GigabitEthernet0/0/1，当接口Down时优先级降低30
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 track interface GigabitEthernet0/0/1 reduced 30  
```

6. 配置VRRP备份组联动普通BFD会话

```cmd
[interface-GigabitEthernet0/0/0] vrrp vrid virtual-router-id track bfd-session  bfd-session-id | session-name bfd-configure-name }[ increased value-increased | reduced value-reduced ］
即
vrrp vrid [虚拟组ID] track bfd-session { bfd-session-id | session-name bfd-configure-name } [ increased 增量值 | reduced 减量值 ]
```

如果选择参数session-name bfd-configure-name，可以绑定静态BFD会话或者标识符自协商的静态BFD会话。如果选择参数bfd-session-id，只能绑定静态BFD会话。

示例

```cmd
# 创建VRRP备份组10，虚拟IP为192.168.1.254
[Switch] interface GigabitEthernet0/0/0  
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 virtual-ip 192.168.1.254  
# 配置初始优先级为120（默认100，需高于Backup设备）
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 priority 120  
# 创建静态BFD会话，检测上行链路（如接口GE0/0/1的对端IP）
[Switch] bfd  
[Switch-bfd] quit  
[Switch] bfd test bind peer-ip 10.1.1.2 interface GigabitEthernet0/0/1  
[Switch-bfd-session-test] discriminator local 10  
[Switch-bfd-session-test] discriminator remote 20  
[Switch-bfd-session-test] commit  
# 将VRRP组10与BFD会话绑定，当BFD检测到故障时优先级降低40
[Switch-GigabitEthernet0/0/0] vrrp vrid 10 track bfd-session session-name test reduced 40  
```

#### 配置实例

![image-20250408220607217](https://img.yatjay.top/md/20250408220607265.png)

配置要求：

- R1与R2组成一个VRRP备份组，其中R1为Master，R2为Backup；
- Master设备故障恢复时采用抢占模式，抢占延时10秒；
- Master设备监视上行接口状态实现VRRP主备自动切换。

R1配置如下：

```cmd
[R1] interface GigabitEthernet0/0/0
[R1-GigabitEthernet0/0/0] ip address 192.168.1.253 24
[R1-GigabitEthernet0/0/0] vrrp vrid 1 virtual-ip 192.168.1.254
[R1-GigabitEthernet0/0/0] vrrp vrid 1 priority 120
[R1-GigabitEthernet0/0/0] vrrp vrid 1 preempt-mode timer delay 10 # 主设备恢复时抢占延时为10秒即恢复后10秒接管工作
[R1-GigabitEthernet0/0/0] vrrp vrid 1 track interface GigabitEthernet0/0/1 reduced 30
```

R2配置如下：

```cmd
[R2] interface GigabitEthernet0/0/0
[R2-GigabitEthernet0/0/0] ip address 192.168.1.252 24
[R2-GigabitEthernet0/0/0] vrrp vrid 1 virtual-ip 192.168.1.254
[R2-GigabitEthernet0/0/0] vrrp vrid 1 priority 110
```

#### VRRP基础配置验证

主设备R1验证

```cmd
[R1]display vrrp
GigabitEtherneto/0/o|Virtual Router1  #VRRP组ID为1
State : Master   #本设备在组中状态为Master
VirtualIP :192.168.1.254
Master IP : 192.168.1.253
PriorityRun：120   #接口在本VRRP组中优先级为120
PriorityConfig : 120
MasterPriority : 120
Preempt:YES Delay Time:10s   #开启抢占模式，且延迟时间为10秒
TimerRun : 1 s
TimerConfig : 1 s
Auth type : NONE
Virtual MAC :0000-5e00-0101
Check TTL : YES
 Config type : normal-vrrp
Track IF : GigabitEthernet0/0/1 Priority reduced : 30
IF state :UP
```

备份设备R2验证

```cmd
[R2]display vrrp
GigabitEthernet0/0/o | Virtual Router 1
State:Backup   #本设备在组中状态为Backup
Virtual IP : 192.168.1.254
Master IP : 192.168.1.253
PriorityRun：110   #接口在本VRRP组中优先级为110
 PriorityConfig : 110
MasterPriority : 120
Preempt:YES Delay Time:0s   #开启抢占模式，延迟时间为0秒
TimerRun : 1 s
TimerConfig : 1 s
Auth type : NONE
Virtual MAC:0000-5e00-0101
Check TTL : YES
 Config type : normal-vrrp
```

