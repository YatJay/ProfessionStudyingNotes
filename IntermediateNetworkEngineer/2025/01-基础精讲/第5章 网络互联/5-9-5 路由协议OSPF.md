## 5-9 路由协议

### 5-9-5 路由协议OSPF

#### OSPF原理简介

##### RIP协议的问题

- 只传递路由信息（如网络号、掩码、Cost）
- 信息量少，可能导致错误传播 
  - 例如：R1收到R2的路由信息后，若R2的Cost有误，R1也会沿用错误信息


##### OSPF协议的改进

- **拓扑信息 + 路由信息 → 链路状态（Link State）**
  - 每个路由器通过发送链路状态通告（LSA）描述自身连接情况
  - 收集全网拓扑信息后，使用SPF算法计算最优路径

- **路由信息与拓扑信息的区别**  

  - **路由信息**：仅包含目的网络、下一跳、Cost（如R1到3.0的Cost=3）  

  - **拓扑信息**：包含邻居关系（如R1的邻居是R2，R2的邻居是R1和R3）

- **OSPF工作流程**  

  - 各路由器发送LSA，收集全网拓扑  

  - 使用SPF算法计算最短路径  

  - 动态更新LSA以应对网络变化

##### OSPF开销计算

- 公式：`Cost = 10^8 / 带宽（BW）`  
  - 例如：百兆链路的Cost为10^8 / 10^8 = 1
  

##### OSPF的优势

- 自主计算最优路径，无需依赖其他设备通知  
- 支持快速收敛和故障恢复  
- 类似协议：IS-IS（后续课程讲解）

#### OSPF简介

**OSPF（Open Shortest Path First，开放式最短路径优先协议）**是目前应用最广泛的路由协议。

- OSPF是一种<font color="red">内部网关协议IGP</font>，也是<font color="red">链路状态</font>路由协议，支持VLSM，通过带宽计算最佳路径，采用<font color="red">Dijkstra</font>算法（也叫SPF最短路径优先算法）。[链路状态路由协议有2个：OSPF和IS-IS]
- 华为设备OSPF协议优先级Internal 10，External 150（import-route）[自己宣告的路由优先级为10，外部引入的路由优先级为150]
- 支持在<font color="red">ABR/ASBR手工路由汇总</font>，不支持自动汇总。
  - [ABR：区域边界路由器，ASBR：自治系统边界路由器]    
  - [支持自动汇总的路由协议有：RIP、BGP，都是距离矢量路由协议]


#### OSPF特点

1. 采用触发式更新、<font color="red">分层路由</font>，支持大型网络。允许网络被**划分成区域**来管理，分区域管理的优点如下所示(可能考简答)：
   - 链路状态数据库仅需和区域内其他路由器保持一致，这样可以<font color="red">减小对路由器内存和CPU的消耗</font>。
   - 同时区域间传送的路由信息减小，<font color="red">降低网络带宽占用</font>。

2. 骨干区域采用Area 0.0.0.0或者Area 0来表示，<font color="red">区域1不是骨干区域</font>。

3. OSPF通过<font color="red">hello报文</font>发现邻居，维护邻居关系。
   - 在<font color="red">点对点和广播网络中每10秒</font>发送一次hello(以太网就是一种广播网络，每10秒发送一次hello)
   - 在NBMA网络中每30秒发送一次hello
   - Deadtime为hello时间的4倍，如果4*hello时间之后仍然没有收到hello报文，就认为该邻居路由器挂掉了。
   - Hello定时器时间表格：

     | 网络类型  | Hello时间（s）              | Dead时间（s） | 备注                                                         |
     | --------- | --------------------------- | ------------- | ------------------------------------------------------------ |
     | P2P       | <font color="red">10</font> | 40            | PPP/HDLC                                                     |
     | Broadcast | <font color="red">10</font> | 40            | 典型的是以太网                                               |
     | NBMA      | <font color="red">30</font> | 120           | 帧中继FR，不支持组播，<font color="red">手动指定</font>OSPF邻居 |
     | P2MP      | 30                          | 120           | 手动配置为该类型                                             |

     注意事项：

     - P2P和Broadcast的Hello/Dead time一致，<font color="red">可以建立邻居，但不能传递路由</font>即同样Hello时间的不同网络类型可以建立邻居关系，但不能传递路由
     - 接口上的网络类型可以手动修改，比如将一个NBMA网络类型的接口修改为广播网络类型
     - NBMA和P2MP都是低速网络，其Hello时间间隔较长

4. OSPF路由器间通过<font color="red">LSA</font>(LinkState Advertisement,链路状态公告)交换<font color="red">网络拓扑信息</font>，每台运行OSPF协议的路由器通过收到的拓扑信息构建LSDB链路状态数据库即拓扑数据库，再以此为基础计算路由。路由器之间<font color="red">交互的是链路状态信息</font>，而不是直接交互路由。OSPF互相交互的五种报文如下：

   - | 类型 | 报文名称                          | 报文功能                                                     | 理解               |
| ---- | --------------------------------- | ------------------------------------------------------------ | ------------------ |
| 1    | Hello                             | 周期性发送，用来发现和<font color="red">维持OSPF邻居</font>关系。 | 哈喽               |
| 2    | Database Description (DD)         | 描述本地LSDB的<font color="red">摘要信息</font>，进行数据库同步。 | 目录/摘要          |
| 3    | Link State Request (LSR)          | 用于向对方<font color="red">请求</font>所需的LSA。成功交换DD报文后才会向对方发出LSR报文。 | 根据目录，请求所需 |
| 4    | Link State Update (LSU)           | 用于向对方<font color="red">发送</font>其所需要的LSA。       | 根据请求，发送所需 |
| 5    | Link State Acknowledgment (LSAck) | 用来对收到的LSA进行<font color="red">确认</font>。           | 确认               |

5. OSPF系统内几个特殊组播地址:
   - 224.0.0.1：在本地子网的所有主机
   - 224.0.0.2：在本地子网的所有路由器
   - <font color="red">224.0.0.5：运行OSPF协议的路由器</font>
   - <font color="red">224.0.0.6：OSPF指定/备用指定路由器DR/BDR</font>

6. 每个MA网段选取一个DR和BDR，作为代表与其他路由器Dother建立邻居关系。
7. router-id在OSPF**区域内**唯一标识一台路由器的IP地址，**整个OSPF域内**<font color="red">不能设置为相同</font>。
8. **OSPF的router-id选举规则如下:** (记忆)
   1. **优选手工配置的router-id。**
      - **OSPF进程手工配置的router-id具有最高优先级。**
      - **在全局模式下配置的公用router-id的优先级仅次于直接给OSPF进程手工配置router-id，即它具有第二优先级。**
   2. **在没有手工配置的前提下，优选loopback接口地址中最大的地址作为router-id。**
   3. **在没有配置loopback接口地址的前提下，优选普通接口的IP地址中选择最大的地址作为router-id（不考虑接口的Up/Down状态)。**

#### DR与BDR的作用

MA网络，即多路访问（Multiple Access）网络，是指一条链路上允许多个设备同时存在的网络类型

##### MA网络中的问题

如下图所示，如果5台路由器之间互相交互LSA链路状态信息，有如下缺陷：

- n×(n-1)/2个邻接关系，管理复杂
- 重复的LSA泛洪，造成资源浪费

![image-20250406222637801](https://img.yatjay.top/md/20250406222637845.png)

解决方法是在网络中选举出老大DR(制定路由器)、老二BDR(备份指定路由器)，用来交互LSA链路状态信息，其他的路由器称为Dother

##### 在MA网络中选举DR

如下图所示，选举出了DR和BDR

- DR（Designated Router，指定路由器）负责在MA网络建立和维护邻接关系并负责LSA的同步。
- DR与其他所有路由器形成邻接关系并交换链路状态信息，其他路由器之间不直接交换链路状态信息。
- 为了规避单点故障风险，通过选举BDR（Backup Designated Router，备份指定路由器），负责在DR失效时快速接管DR的工作。

![image-20250406222651836](https://img.yatjay.top/md/20250406222651884.png)

##### DR与BDR的选举规则

优先级范围为0~255，且优先级为0的不参与选举

DR/BDR的选举是<font color="red">非抢占式</font>的。

DR/BDR的选举是基于接口的

- 接口的DR优先级越大越优先
- 接口的DR优先级相等时，RouterID越大越优先。

如下图所示，R1、R2、R3优先级分别为100、0、95，其中优先级为0的R2默认不参与选举，在R1和R3中，选举优先级更高的R1作为DR。此时拓扑中新增路由器R4，优先级为200，优先级更高，但此时R4作为新加入设备，不能直接成为DR或BDR，因为<font color="red">BR、BDR的选举是非抢占式的</font>。

如果作为DR的R1挂了，则作为BDR的R3成为新的DR，而后才能选举优先级较高的R4作为BDR

![image-20250406222741458](https://img.yatjay.top/md/20250406222741493.png)

思考：

- 如果将上图中4台路由器的优先级全部设置为0，OSPF是否可以正常工作?
  - 不可以，因为无法选举出DR，无法建立邻居关系，至少要有1台的优先级不为0，至少要保证能够选举出DR

- 缺省情况下，哪些链路类型组成的网络是MA网络呢?
  - 以太网、FDDI令牌环


#### OSPF的DR/BDR优先级

DR选举规则：最高OSPF接口优先级拥有者被选为DR，如果优先级相等<font color="red">（默认为1）</font>，具有最高OSPF Router ID的路由器被选举为DR，并且DR具有非抢占性。<font color="red">【优先级0不参与选举】</font>

- 备用指定路由器（BDR）：监控DR状态，并在当前DR发生故障后接替其角色。

  ```bash
  [AR1-GigabitEthernet0/0/0] ospf dr-priority ?
  INTEGER<0-255> Router priority value
  ```

  ![image-20250406222918642](https://img.yatjay.top/md/20250406222918679.png)

#### 不同网络类型中DR与BDR的选举操作

<font color="red">一边broadcast，一边P2P，可以FULL，不能传路由</font>

| OSPF网络类型                       | 常见链路层协议                      | 是否选举DR | 是否和邻居建立邻接关系                                       |
| ---------------------------------- | ----------------------------------- | ---------- | ------------------------------------------------------------ |
| Point-to-point                     | PPP链路；HDLC链路                   | 否         | 是                                                           |
| <font color="red">Broadcast</font> | <font color="red">以太网链路</font> | 是         | DR与BDR、DRother建立邻接关系（FULL）<br>BDR与DR、DRother建立邻接关系（FULL）<br>DRother之间只建立<font color="red">邻居关系（2-way）</font> |
| NBMA                               | 帧中继链路                          | 是         | DR与BDR、DRother建立邻接关系（FULL）<br>BDR与DR、DRother建立邻接关系（FULL）<br>DRother之间只建立<font color="red">邻居关系（2-way）</font> |
| P2MP                               | 需手工指定                          | 否         | 是                                                           |

#### OSPF LSA

OSPF使用LSA（LinkStateAdvertisement，链路状态通告）传递链路状态信息。

LSA需要描述<font color="red">邻接路由器信息、直连链路信息、跨区域信息</font>等，所以定义了多种类型的LSA。

- 类型1、类型2：用于<font color="red">**区域内**</font>路由信息交互
- 类型3：用于<font color="red">**区域间**</font>路由信息交互
- 类型4、类型5：用于有<font color="red">**外部引入**</font>的情况，如将RIP路由信息引入到OSPF中时
- 类型7：特殊类型，NSSA使用

| 类型 | 名称                              | 描述                                                         |
| ---- | --------------------------------- | ------------------------------------------------------------ |
| 1    | 路由器LSA (Router LSA)            | **每个设备都会产生**，描述了设备的链路状态和开销，该LSA只能在接口所属的区域内泛洪。 |
| 2    | 网络LSA (Network LSA)             | 由**DR产生**，描述该DR所接入的MA网络中所有与之形成邻接关系的路由器，以及DR自己。该LSA只能在接口所属区域内泛洪。 |
| 3    | 网络汇总LSA (Network Summary LSA) | 由**ABR产生**，描述区域内某个网段的路由，该类LSA主要用于区域间路由的传递。[ABR：区域边界路由器] |
| 4    | ASBR汇总LSA (ASBR Summary LSA)    | 由**ABR产生**，描述到ASBR的路由，通告给除ASBR所在区域的其他相关区域。[ABR：区域边界路由器，ASBR：自治系统边界路由器] |
| 5    | AS外部LSA (AS External LSA)       | 由**ASBR产生**，用于描述到达OSPF域外的路由。[ASBR：自治系统边界路由器] |
| 7    | 非完全末梢区域LSA (NSSA LSA)      | 由**ASBR产生**，用于描述到达OSPF域外的路由。NSSA LSA与AS外部LSA功能类似，但是泛洪范围不同。NSSA LSA只能在始发的NSSA内泛洪，并且不能直接进入Area0。NSSA的ABR会将7类LSA转换成5类LSA注入到Area0。[ASBR：自治系统边界路由器] |

#### OSPF cost

- OSPF使用Cost"开销”作为路由度量值。
- OSPF接口<font color="red">cost=100M/接口带宽</font>，其中100M为OSPF参考带宽（reference-bandwidth），可修改；并且<font color="red">结果<1时，取1</font>
- 每一个激活OSPF的接口都有一个cost值。
- 一条OSPF路由的cost由该路由从起源一路到达本地的所有<font color="red">入接口cost值的总和</font>。如下图所示，从1.1.1.0/24到达R3的路由开销值为1 + 48 + 1 = 50

![image-20250406223413187](https://img.yatjay.top/md/20250406223413226.png)

#### OSPF区域

所有非骨干区域<font color="red">必须与骨干区域直连</font>

如果有区域没有与Area 0相联，可以通过<font color="red">虚连接</font>临时解决，只能横穿一个非骨干区域

![image-20250406223501955](https://img.yatjay.top/md/20250406223502003.png)

#### OSPF路由器角色

- 区域内路由器IR（InternalRouters）：该类路由器的所有接口都属于同一个OSPF区域
- 区域边界路由器ABR（AreaBorderRouters）：该类路由器可以同时属于两个以上的区域，ABR用来连接骨干区域和非骨干区域
- 骨干路由器BR（BackboneRouters）：该类路由器至少有一个接口属于骨干区域。因此，所有的ABR和位于Area0的内部路由器都是骨干路由器。只要直连到骨干区域的都是骨干路由器
- 自治系统边界路由器ASBR（ASBoundaryRouters）：与其他AS交换路由信息的路由器称为ASBR。

![image-20250406223549251](https://img.yatjay.top/md/20250406223549299.png)

#### 例题

##### 例题1

![image-20250406223235664](https://img.yatjay.top/md/20250406223235719.png)

解析：路由器优先级取值是0-255，如果路由器优先级为0，则代表它不具备DR和BDR的选举资格。

- 已知RouterD的优先级是0，那么他肯定不是DR或者BDR。
- Router A优先级最高，他会成为DR
- Router B和Router C的优先级都是1，接着看IP地址，大的胜出成为BDR，则Router C成为BDR。

##### 例题2

![image-20250406223248932](https://img.yatjay.top/md/20250406223248989.png)

解析：

- DR选举规则：最高OSPF接口优先级拥有者被选为DR，如果优先级相等（默认为1），最高OSPFRouterID的路由器被选举为DR，并且DR具有非抢占性。【优先级0不参与选举】

- Dother之间是2-way状态（稳定状态）

##### 例题3

![image-20250406223641705](https://img.yatjay.top/md/20250406223641744.png)

解析：OSPF使用Hello报文维护邻居关系。

##### 例题4

![image-20250406223657492](https://img.yatjay.top/md/20250406223657544.png)

解析：

- 广播网络和点对点网络hello是10s，NBMA网络hello是30s，默认都是广播型网络（如以太网）。
- 同步数据库采用LSA报文。

##### 例题5

![image-20250406223713439](https://img.yatjay.top/md/20250406223713508.png)

解析：

- 区域内路由器IR（InternalRouters）：该类路由器的所有接口都属于同一个OSPF区域
- 区域边界路由器ABR（AreaBorderRouters）：该类路由器可以同时属于两个以上的区域，ABR用来连接骨干区域和非骨干区域
- 骨干路由器BR（BackboneRouters）：该类路由器至少有一个接口属于骨干区域。因此，所有的ABR和位于Area0的内部路由器都是骨干路由器。只要直连到骨干区域的都是骨干路由器
- 自治系统边界路由器ASBR（ASBoundaryRouters）：与其他AS交换路由信息的路由器称为ASBR。

##### 例题6

![image-20250406223728157](https://img.yatjay.top/md/20250406223728210.png)

![image-20250406223736068](https://img.yatjay.top/md/20250406223736119.png)

解析：

- 区域内路由器IR（InternalRouters）：该类路由器的所有接口都属于同一个OSPF区域
- 区域边界路由器ABR（AreaBorderRouters）：该类路由器可以同时属于两个以上的区域，ABR用来连接骨干区域和非骨干区域
- 骨干路由器BR（BackboneRouters）：该类路由器至少有一个接口属于骨干区域。因此，所有的ABR和位于Area0的内部路由器都是骨干路由器。只要直连到骨干区域的都是骨干路由器
- 自治系统边界路由器ASBR（ASBoundaryRouters）：与其他AS交换路由信息的路由器称为ASBR。

##### 例题7

![image-20250406223745427](https://img.yatjay.top/md/20250406223745481.png)

解析：

- OSPF不会自动汇总，需要手工配置，故A选项错误。
- 在ABR和ASBR上都能配置路由聚合，故B选项正确。
- 一台路由器同时做ABR和ASBR，并不影响各自汇聚路由。作为ABR仍然能聚合区域间路由，作为ASBR仍然能聚合外部路由，这两个功能是分开的，故C选项错误。
- ASBR上只能聚合“由自己引入的”外部路由，如果ASBR从别的ASBR学习到一条外部路由，它是聚合不了的。只能聚合活跃的外部路由，什么是活跃的呢，比如同时从ip和eigrp到两条相同的路由，根据管理距离不同，eigrp会优选，就是活跃的，rip的那条路由就不活跃了，如果这时候引入rip到ospf的话，是不能聚合的，故D选项错误

##### 例题8

![image-20250406223801838](https://img.yatjay.top/md/20250406223801899.png)

解析：OSPF区域O中的路由器分为两类

- 一类是内部路由器IR，只有区域0的LSDB
- 还有一类是区域边界路由器ABR，可能包含多个区域的LSDB

因此OSPF区域0中的路由器LSDB不一定相同，故D选项错误。

##### 例题9

![image-20250406223809331](https://img.yatjay.top/md/20250406223809378.png)

解析：**OSPF的router-id选举规则如下:** (记忆)

1. **优选手工配置的router-id。**
   - **OSPF进程手工配置的router-id具有最高优先级。**题目中`[RA]ospf 1 router-id 1.1.1.1`即为手动配置Router ID
   - **在全局模式下配置的公用router-id的优先级仅次于直接给OSPF进程手工配置router-id，即它具有第二优先级。**即题目中`[RA] router id 2.2.2.2`这是全局Router ID
2. **在没有手工配置的前提下，优选loopback接口地址中最大的地址作为router-id。**即题目中`[RA-LoopBack0] ip address 3.3.3.3 32`
3. **在没有配置loopback接口地址的前提下，优选普通接口的IP地址中选择最大的地址作为router-id（不考虑接口的Up/Down状态)。**

##### 例题10

![image-20250406223819825](https://img.yatjay.top/md/20250406223819874.png)

