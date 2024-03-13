# 6-28 VLAN及Trunk配置演示

## VLAN配置基础命令（背下来）

在交换机上创建VLAN，并进入VLAN视图

```bash
[Quidway] vlan 10 或 vlan batch 10 20 30  # batch关键字可批量创建VLAN
```

(可选）在特定VLAN视图下修改VLAN的名字

```bash
[Quidway-vlan10] name TechVLAN # 即命名为TechVLAN
```

为特定的vlan添加接口：在VLAN视图下，将相应的端口放到相应VLAN当中

```bash
[Quidway-vlan10] port GigabitEthernet0/0/1  # 在VLAN视图下，将相应的端口放到相应VLAN当中
```

将特定的接口配置为access类型，并加入VLAN：在接口视图下，将某一接口配置端口类型并放到相应VLAN中（接口放到VLAN，这种方法使用更多）

```bash
[Quidway] interface gigabitEthernet0/0/1

[Quidway-gigabitEthernet0/0/1] port link-type access  # 在接口视图下，将某一接口配置端口类型

[Quidway-gigabitEthernet0/0/1] port default vlan 10  # 在接口视图下，将某一接口放到相应VLAN中
```

## VLAN基础实验1：VLAN基本配置

### 实验目的

![image-20231005170813944](https://img.yatjay.top/md/image-20231005170813944.png)

### 实验过程

按照上述拓扑搭建ENSP拓扑，交换机G0/0/1端口连接PC1，交换机G0/0/2端口连接PC2

![image-20231005173358313](https://img.yatjay.top/md/image-20231005173358313.png)

配置三层IP地址、掩码、网关等内容如下图所示

PC1

![image-20231005172520615](https://img.yatjay.top/md/image-20231005172520615.png)

PC2

![image-20231005173741108](https://img.yatjay.top/md/image-20231005173741108.png)

测试PC1和PC2能否ping通：不在同一个网段（不在一个子网下），必定无法ping通

在交换机上创建VLAN10及VLAN20

```bash
[SW1] vlan batch 10 20  #batch关键字可批量创建VLAN
```

![image-20231005173013195](https://img.yatjay.top/md/image-20231005173013195.png)

将GE0/0/1配置为access类型，并加入vlan10

```bash
[SW1] interface gigabitEthernet0/0/1

[SW1-gigabitEthernet0/0/1] port link-type access

[SW1-gigabitEthernet0/0/1] port default vlan 10
```

![image-20231005173601954](https://img.yatjay.top/md/image-20231005173601954.png)

将GE0/0/2配置为access类型，并加入vlan20

```bash
[SW1] interface gigabitEthernet0/0/2

[SW1-gigabitEthernet0/0/2] port link-type access

[SW1-gigabitEthernet0/0/2] port default vlan 20
```

![image-20231005175339485](https://img.yatjay.top/md/image-20231005175339485.png)

## Trunk基本配置命令

将特定接口配置为Trunk类型

```bash
[Quidway] interface gigabitEthernet0/0/24  # 一般将G0/0/24这种序号最大或序号最小的接口配置为Trunk口

[Quidway-gigabitEthernet0/0/24] port link-type trunk
```

在该Trunk接口上放行特定VLAN

```bash
[Quidway-gigabitEthernet0/0/24] port trunk allow-pass vlan 10 20 99  # 配置该Trunk口允许哪些VLAN的帧通过
```

配置Trunk接口的pvid，这个VLAN的流量从Trunk接口转发不会打标签，即**去掉Access标签后转发**，默认是vlan 1

```bash
[Quidway-gigabitEthernet0/0/24] port trunk pvid vlan 99
```

### VLAN基础实验2：Trunk的配置

### 实验目的

![image-20231005171030008](https://img.yatjay.top/md/image-20231005171030008.png)

### 实验过程

按照题目拓扑图搭建ensp拓扑



按照题目IP、掩码配置各个主机IP、掩码，不需要配置网关，因为所有设备不经过三层设备



测试连通性：不配置VLAN即都在默认的VLAN1之下，同一个网段，不同交换机下的设备可以通信；不同网段的设备不能通信

如PC1——PC3连通、PC1——PC2、PC4不通；PC2——PC4连通、PC2——PC1、PC3不通。测试ping命令结果

PC1 ping PC3可以ping通，如下图所示



PC2 ping PC4可以ping通，如下图所示





为交换机创建VLAN：VLAN 10和VLAN 20



交换机1的G0/0/1接口：PC1直连交换机1的G0/0/1接口，将G0/0/1接口：端口类型为Access，VLAN设置为VLAN10



交换机1的G0/0/2接口：PC1直连交换机1的G0/0/2接口，将G0/0/2接口：端口类型为Access，VLAN设置为VLAN20



交换机1的G0/0/24接口：交换机2连接交换机1的G0/0/24接口，因此端口类型为Trunk，allow-pass 放行VLAN 10 20 和 1（即允许VLAN 1、VLAN 10、VLAN 20的数据帧通过此Trunk口）



交换机2的G0/0/1接口：PC3直连交换机2的G0/0/1接口，将G0/0/1接口：端口类型为Access，VLAN设置为VLAN10



交换机2的G0/0/2接口：PC4直连交换机2的G0/0/2接口，将G0/0/2接口：端口类型为Access，VLAN设置为VLAN20



交换机2的G0/0/24接口：交换机1连接交换机2的G0/0/24接口，因此端口类型为Trunk，allow-pass 放行VLAN 10 20 和 1（即允许VLAN 1、VLAN 10、VLAN 20的数据帧通过此Trunk口）



VLAN及Trunk配置结束，测试连通性

PC1 ping PC3连通



PC2 ping PC4连通



实验结束
