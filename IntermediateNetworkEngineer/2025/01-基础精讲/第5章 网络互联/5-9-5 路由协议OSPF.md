## 5-9 路由协议

### 5-9-5 路由协议OSPF

#### OSPF简介

**OSPF（Open Shortest Path First，开放式最短路径优先协议）**是目前应用最广泛的路由协议。

- OSPF是一种<font color="red">内部网关协议IGP</font>，也是<font color="red">链路状态</font>路由协议，支持VLSM，通过带宽计算最佳路径，采用<font color="red">Dijkstra</font>算法（也叫SPF最短路径算法）。
- 华为设备OSPF协议优先级Internal 10，External 150（import-route）
- 支持在<font color="red">ABR/ASBR手工路由汇总</font>，不支持自动汇总。

#### OSPF特点

1. 采用触发式更新、<font color="red">分层路由</font>，支持大型网络。允许网络被划分成区域来管理，链路状态数据库仅需和区域内其他路由器保持一致。<font color="red">减小对路由器内存和CPU的消耗</font>。同时区域间传送的路由信息减小，<font color="red">降低网络带宽占用</font>。
2. 骨干区域采用Area 0.0.0.0或者Area 0来表示，<font color="red">区域1不是骨干区域</font>。
3. OSPF通过<font color="red">hello报文</font>发现邻居，维护邻居关系。在<font color="red">点对点和广播网络中每10秒</font>发送一次hello，在NBMA网络中每30秒发送一次hello，Deadtime为hello时间的4倍。
4. OSPF路由器间通过<font color="red">LSA</font>(LinkState Advertisement,链路状态公告)交换网络拓扑信息，每台运行OSPF协议的路由器通过收到的拓扑信息构建拓扑数据库，再以此为基础计算路由。路由器之间<font color="red">交互的是链路状态信息</font>，而不是直接交互路由。
5. OSPF系统内几个特殊组播地址:
   - 224.0.0.1：在本地子网的所有主机
   - 224.0.0.2：在本地子网的所有路由器
   - <font color="red">224.0.0.5：运行OSPF协议的路由器</font>
   - <font color="red">224.0.0.6：OSPF指定/备用指定路由器DR/BDR</font>

| 类型 | 报文名称                          | 报文功能                                                     |
| ---- | --------------------------------- | ------------------------------------------------------------ |
| 1    | Hello                             | 周期性发送，用来发现和<font color="red">维持OSPF邻居</font>关系。 |
| 2    | Database Description (DD)         | 描述本地LSDB的<font color="red">摘要信息</font>，进行数据库同步。 |
| 3    | Link State Request (LSR)          | 用于向对方<font color="red">请求</font>所需的LSA。成功交换DD报文后才会向对方发出LSR报文。 |
| 4    | Link State Update (LSU)           | 用于向对方<font color="red">发送</font>其所需要的LSA。       |
| 5    | Link State Acknowledgment (LSAck) | 用来对收到的LSA进行<font color="red">确认</font>。           |

#### Hello定时器

| 网络类型  | Hello时间（s）              | Dead时间（s） | 备注                                                         |
| --------- | --------------------------- | ------------- | ------------------------------------------------------------ |
| P2P       | <font color="red">10</font> | 40            | PPP/HDLC                                                     |
| Broadcast | 10                          | 40            | 典型的是以太网                                               |
| NBMA      | <font color="red">30</font> | 120           | 帧中继FR，不支持组播，<font color="red">手动指定</font>OSPF邻居 |
| P2MP      | 30                          | 120           | 手动配置为该类型                                             |

P2P和Broadcast的Hello/Dead time一致，<font color="red">可以建立邻居，但不能传递路由</font>

6. 每个MA网段选取一个DR和BDR，作为代表与其他路由器Dother建立邻居关系。
7. router-id在OSPF区域内唯一标识一台路由器的IP地址，**整个OSPF域内**<font color="red">不能设置为相同</font>。
8. OSPF的router-id选举规则如下: 
   1. 优选手工配置的router-id。
      - OSPF进程手工配置的router-id具有最高优先级。
      - 在全局模式下配置的公用router-id的优先级仅次于直接给OSPF进程手工配置router-id，即它具有第二优先级。
   2. 在没有手工配置的前提下，优选loopback接口地址中最大的地址作为router-id。
   3. 在没有配置loopback接口地址的前提下，优选普通接口的IP地址中选择最大的地址作为router-id（不考虑接口的Up/Down状态)。

#### DR与BDR的作用

MA网络中的问题

- n×(n-1)/2个邻接关系，管理复杂。
- 重复的LSA泛洪，造成资源浪费。

![image-20250406222637801](https://img.yatjay.top/md/20250406222637845.png)

在MA网络中选举DR:

- DR（Designated Router，指定路由器）负责在MA网络建立和维护邻接关系并负责LSA的同步。
- DR与其他所有路由器形成邻接关系并交换链路状态信息，其他路由器之间不直接交换链路状态信息。
- 为了规避单点故障风险，通过选举BDR（Backup Designated Router，备份指定路由器）W：在DR失效时快速接管DR的工作。

![image-20250406222651836](https://img.yatjay.top/md/20250406222651884.png)

#### DR与BDR的选举规则

DR/BDR的选举是<font color="red">非抢占式</font>的。

DR/BDR的选举是基于接口的

- 接口的DR优先级越大越优先
- 接口的DR优先级相等时，RouterID越大越优先。

![image-20250406222741458](https://img.yatjay.top/md/20250406222741493.png)

思考：

- 如果将上图中4台路由器的优先级全部设置为O，OSPF是否可以正常工作?
- 缺省情况下，哪些链路类型组成的网络是MA网络呢?

#### OSPFDR/BDR优先级

DR选举规则：最高OSPF接口优先级拥有者被选为DR，如果优先级相等等<font color="red">（默认为1）</font>，具有最高OSPF Router ID的路由器被选举为DR，并且DR具有非抢占性。<font color="red">【优先级0不参与选举】</font>

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

| 类型 | 名称                              | 描述                                                         |
| ---- | --------------------------------- | ------------------------------------------------------------ |
| 1    | 路由器LSA (Router LSA)            | 每个设备都会产生，描述了设备的链路状态和开销，该LSA只能在接口所属的区域内泛洪。 |
| 2    | 网络LSA (Network LSA)             | 由DR产生，描述该DR所接入的MA网络中所有与之形成邻接关系的路由器，以及DR自己。该LSA只能在接口所属区域内泛洪。 |
| 3    | 网络汇总LSA (Network Summary LSA) | 由ABR产生，描述区域内某个网段的路由，该类LSA主要用于区域间路由的传递。 |
| 4    | ASBR汇总LSA (ASBR Summary LSA)    | 由ABR产生，描述到ASBR的路由，通告给除ASBR所在区域的其他相关区域。 |
| 5    | AS外部LSA (AS External LSA)       | 由ASBR产生，用于描述到达OSPF域外的路由。                     |
| 7    | 非完全末梢区域LSA (NSSA LSA)      | 由ASBR产生，用于描述到达OSPF域外的路由。NSSA LSA与AS外部LSA功能类似，但是泛洪范围不同。NSSA LSA只能在始发的NSSA内泛洪，并且不能直接进入Area0。NSSA的ABR会将7类LSA转换成5类LSA注入到Area0。 |

#### OSPF cost

- OSPF使用Cost"开销”作为路由度量值。
- OSPF接口<font color="red">cost=100M/接口带宽</font>，其中10oM为OSPF参考带宽（reference-bandwidth），可修改。
- 每一个激活OSPF的接口都有一个cost值。
- 一条OSPF路由的cost由该路由从起源一路到达本地的所有<font color="red">入接口cost值的总和</font>。

![image-20250406223413187](https://img.yatjay.top/md/20250406223413226.png)

#### OSPF区域

所有非骨干区域<font color="red">必须与骨干区域直连</font>

如果有区域没有与AreaO相联，可以通过<font color="red">虚连接</font>临时解决，只能横穿一个非骨干区域

![image-20250406223501955](https://img.yatjay.top/md/20250406223502003.png)

#### OSPF路由器角色

- 区域内路由器：Internal Router
- 区域边界路由器ABR：Area Border Router
- 骨干路由器：Backbone Router
- AS边界路由器ASBR：AS Boundary Router

![image-20250406223549251](https://img.yatjay.top/md/20250406223549299.png)

#### 例题

##### 例题1

![image-20250406223235664](https://img.yatjay.top/md/20250406223235719.png)



##### 例题2

![image-20250406223248932](https://img.yatjay.top/md/20250406223248989.png)



##### 例题3

![image-20250406223641705](https://img.yatjay.top/md/20250406223641744.png)





##### 例题4

![image-20250406223657492](https://img.yatjay.top/md/20250406223657544.png)



##### 例题5

![image-20250406223713439](https://img.yatjay.top/md/20250406223713508.png)



##### 例题6

![image-20250406223728157](https://img.yatjay.top/md/20250406223728210.png)

![image-20250406223736068](https://img.yatjay.top/md/20250406223736119.png)

##### 例题7

![image-20250406223745427](https://img.yatjay.top/md/20250406223745481.png)



##### 例题8

![image-20250406223801838](https://img.yatjay.top/md/20250406223801899.png)

##### 例题9

![image-20250406223809331](https://img.yatjay.top/md/20250406223809378.png)



##### 例题10

![image-20250406223819825](https://img.yatjay.top/md/20250406223819874.png)

