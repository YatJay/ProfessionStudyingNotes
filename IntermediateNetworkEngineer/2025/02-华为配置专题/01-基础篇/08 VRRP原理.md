## 05 VRRP原理

### VRRP概述与原理

技术背景：单网关面临的问题

![image-20250402213813292](https://img.yatjay.top/md/20250402213813359.png)

 **问题描述**：当网关设备（如Router)发生故障时，所有以该设备为网关的主机将无法与Internet通信。

#### VRRP概述

通过把几台路由设备联合组成一台虚拟的“路由设备”，使用一定的机制保证当主机的下一跳路由设备出现故障时，及时将业务切换到备份路由设备，从而保持通讯的连续性和可靠性。

![image-20250402214000216](https://img.yatjay.top/md/20250402214000272.png)

#### VRRP基本概念

**基本定义** ：  VRRP（Virtual Router Redundancy Protocol）通过将多台路由设备组合成虚拟路由设备，实现网关冗余，保障网络连续性。

![image-20250402214050948](https://img.yatjay.top/md/20250402214051002.png)

![image-20250402214326944](https://img.yatjay.top/md/20250402214326983.png)

| 术语                   | 说明                                                         |
| ---------------------- | ------------------------------------------------------------ |
| **VRRP路由器**         | 运行VRRP协议的路由器（如R1、R2）                             |
| **VRID**               | VRID：一个VRRP组（VRRPGroup）由多台协同工作的路由器（的接口）组成，使用相同的VRID（Virtual RouterIdentifier，虚拟路由器标识符）进行标识。属于同一个VRRP组的路由器之间交互VRRP协议报文并产生台虚拟"路由器”。一个VRRP组中只能出现一台Master路由器。 |
| **虚拟路由器**         | VRRP为每一个组抽象出一台虚拟“路由器” （Virtual Router），该路由器并非真实存在的物理设备，而是由VRRP虚拟出来的逻辑设备。一个VRRP组只会产生一台虚拟路由器 |
| **虚拟IP/MAC地址**     | 虚拟路由器拥有自己的IP地址以及MAC地址，其中IP地址由网络管理员在配置VRRP时指定，一台虚拟路由器可以有一个或多个IP地址，通常情况下用户使用该地址作为网关地址。而虚拟MAC地址的格式是“0000-5e00-01xx”，其中xx为VRID |
| **Master路由器**       | “Master路由器”在一个VRRP组中承担报文转发任务。在每一个VRRP组中，只有Master路由器才会响应针对虚拟IP地址的ARP Request。Master路由器会以一定的时间间隔周期性地发送VRRP报文，以便通知同一个VRRP组中的Backup路由器关于自己的存活情况 |
| **Backup路由器**       | 也被称为备份路由器。Backup路由器将会实时侦听Master路由器发送出来的VRRP报文，它随时准备接替Master路由器的工作。 |
| **优先级（Priority）** | 优先级值是选举Master路由器和Backup路由器的依据，优先级取值范围0-255，值越大越优先，值相等则比较接口IP地址大小，大者优先。 |



### VRRP典型应用

####  VRRP负载分担

通过创建多个虚拟路由器，每个物理路由器在不同的VRRP组中扮演不同的角色，不同虚拟路由器的
VirtualIP作为不同的内网网关地址可以实现流量转发负载分担。

![image-20250402214811615](https://img.yatjay.top/md/20250402214811672.png)

#### VRRP与MSTP结合

**场景说明**：结合多生成树协议（MSTP）实现链路冗余与负载均衡。

![image-20250402214556591](https://img.yatjay.top/md/20250402214556645.png)

#### VRRP监视上行端口

![image-20250402214642473](https://img.yatjay.top/md/20250402214642526.png)

####  VRRP与BFD联动

通过配置VRRP与BFD联动，当Backup设备通过BFD感知故障发生之后，不再等待Master_Down_Timer计
时器超时而会在BFD检测周期结束后立即切换VRRP状态，此时可以实现毫秒级的主备切换。

![image-20250402214716852](https://img.yatjay.top/md/20250402214716906.png)

### VRRP基本配置

#### 常用配置命令

##### 1. 基础配置

```bash
# 创建VRRP组并指定虚拟IP
interface GigabitEthernet0/0/0
  vrrp vrid 1 virtual-ip 192.168.1.254

# 设置优先级（默认100）
  vrrp vrid 1 priority 120

# 配置抢占延迟
  vrrp vrid 1 preempt-mode timer delay 10

# 禁用抢占模式
  vrrp vrid 1 preempt-mode disable
```

##### 2. 高级功能

```bash
# 监视上行接口（接口故障时优先级降低30）
  vrrp vrid 1 track interface GE0/0/1 reduced 30

# 联动BFD会话
  vrrp vrid 1 track bfd-session 10 reduced 40
```

#### 配置实例

##### 需求描述

• R1为Master（优先级120），R2为Backup（优先级110）  
• Master故障恢复后延迟10秒抢占  
• R1监控上行接口GE0/0/1状态  

##### R1配置

```bash
interface GigabitEthernet0/0/0
  ip address 192.168.1.253 255.255.255.0
  vrrp vrid 1 virtual-ip 192.168.1.254
  vrrp vrid 1 priority 120
  vrrp vrid 1 preempt-mode timer delay 10
  vrrp vrid 1 track interface GigabitEthernet0/0/1 reduced 30
```

##### R2配置

```bash
interface GigabitEthernet0/0/0
  ip address 192.168.1.252 255.255.255.0
  vrrp vrid 1 virtual-ip 192.168.1.254
  vrrp vrid 1 priority 110
```

#### 配置验证

##### Master设备状态（R1）

```bash
<R1> display vrrp
GigabitEthernet0/0/0 | Virtual Router 1
  State            : Master
  Virtual IP       : 192.168.1.254
  Master IP        : 192.168.1.253
  PriorityRun      : 120
  Preempt          : YES   Delay Time: 10s
  Track IF         : GigabitEthernet0/0/1 Priority reduced: 30
```

##### Backup设备状态（R2）

```bash
<R2> display vrrp
GigabitEthernet0/0/0 | Virtual Router 1
  State            : Backup
  Virtual IP       : 192.168.1.254
  Master IP        : 192.168.1.253
  PriorityRun      : 110
```

---

**说明**  
本文档基于华为设备整理，实际配置需结合网络拓扑调整参数。  
关注公众号「summer课堂」或B站/抖音同名账号获取更多技术资料。