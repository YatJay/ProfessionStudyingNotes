## 5-9 路由协议

### 5-9-6 路由协议BGP ISIS

#### BGP基础

BGP（Border GatewayProtocol,边界网关协议）<font color="red">外部网关协议</font>，用于<font color="red">不同自治系统(AS)之间</font>，寻找最佳
路由。BGP有如下特点:

1. BGP通过<font color="red">TCP 179</font>端口建立连接，支持<font color="red">VLSM和CIDR</font>。
2. 支持增量更新，支持<font color="red">认证，支持路由聚合</font>（优先级：手动聚合〉自动聚合>network>import）。
3. 是一种路径矢量协议，可以检测路由环路，支持大型网络。
4. 目前最新版本是BGP4，而<font color="red">BGP4+支持IPv6</font>。
5. 只传递路由信息，<font color="red">不会暴露AS内的拓扑</font>信息。
6. 使用<font color="red">触发式路由更新</font>，而不是周期性路由更新。
7. BGP能够承载大批量的路由信息，能够支撑<font color="red">大规模网络</font>。
8. BGP提供<font color="red">路由聚合和路由衰减功能</font>用于防止路由振荡，通过这两项功能有效地提高了网络稳定性。

#### BGP四个报文

| 报文类型                 | 功能描述                                                     | 备注（类比） |
| ------------------------ | ------------------------------------------------------------ | ------------ |
| 打开 (Open)              | 建立邻居关系                                                 | 建立外交     |
| 更新 (Update)            | 发送新的路由信息                                             | 更新外交信息 |
| 保持活动状态 (Keepalive) | 对Open的应答/<font color="red">周期性确认邻居关系</font><br><font color="red">60s keepalive，180s没收到认为邻居失效</font> | 保持外交活动 |
| 通告 (Notification)      | <font color="red">报告检测到的错误</font>                    | 发布外交通告 |

#### BGP选路规则

1. 丢弃下一跳不可达的路由。
2. 优选<font color="red">Preference_Value</font>最高的路由（私有属性，仅本地有效）。
3. 优选**Local_Preference**最高的路由
4. 优选<font color="red">手动聚合 > 自动聚合 > network > import > 从对等体学到的</font>
5. 优选**AS_Path**最短的路由。
6. 起源类型<font color="red">IGP > EGP > Incomplete</font>。
7. 对于来自同一AS的路由，优选**MED最小**的。
8. 优选从EBGP学来的路由(<font color="red">EBGP > IBGP</font>)
9. 优选AS内部**IGP的Metric最小**的路由。
10. 优选<font color="red">Cluster_List最短</font>的路由。
11. 优选**Orginator_ID最小**的路由。
12. 优选<font color="red">Router_ID最小</font>的路由器发布的路由。
13. 优选**IP地址最小**的邻居学来的路由。

![image-20250406224107942](https://img.yatjay.top/md/20250406224107965.png)

#### ISIS

- IS-IS（Intermediate system tointermediate system，中间系统到中间系统）是内部网关协议，是<font color="red">电信运营商</font>普遍采用的内部网关协议之一，也是一个<font color="red">分级的链路状态路由协议</font>。
- 与OSPF相似，它也使用Hello协议寻找毗邻节点。
- 与大多数路由协议不同，IS-IS直接运行于链路层之上。
- IS-IS具有层次性，分为两层Level-1和Level-2。
  - Level-1(L1）是普通区域(Area)，Level-2(L2）是骨干区(Backbone)。
  - <font color="red">骨干区Backbone是连续的Level-2路由器的集合，由所有的L2(含L1/L2）路由器组成</font>，L1和L2运行相同的SPF算法，一个路由器可能同时参与L1和L2。
- 不要将OSPF骨干和IS-IS骨干区域混淆，OSPF骨干区域是areaO，而IS-IS骨干区域是包含L2和L1/L2
  的区域。一个OSPF路由器可以属于多个区域，而<font color="red">一个IS-IS路由器只能属于一个区域</font>。

#### IS-IS区域结构图

![image-20250406224759494](https://img.yatjay.top/md/20250406224759546.png)

![image-20250406224810809](https://img.yatjay.top/md/20250406224810854.png)

#### 例题

##### 例题1

![image-20250406224906036](https://img.yatjay.top/md/20250406224906081.png)



##### 例题2

![image-20250406224913495](https://img.yatjay.top/md/20250406224913529.png)





##### 例题3

![image-20250406224925398](https://img.yatjay.top/md/20250406224925469.png)



##### 例题4

![image-20250406224933328](https://img.yatjay.top/md/20250406224933366.png)



##### 例题5

![image-20250406224949881](https://img.yatjay.top/md/20250406224949928.png)



##### 例题6

![image-20250406225006664](https://img.yatjay.top/md/20250406225006717.png)

