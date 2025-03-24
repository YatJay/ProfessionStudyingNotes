## DHCP配置实验

本节为实验课程，在ENSP中操作

### 实验拓扑图

![image-20250324224146605](https://img.yatjay.top/md/20250324224146655.png)

### 前置实验：VLAN和VLAN间路由的配置

#### 配置思路

在交换机SW上

- 创建VLAN10、VLAN20并为其VLANIF10和VLANIF20
- 将GE0/0/1接口模式设置为access，并划入VLAN10；将GE0/0/2接口模式设置为access，并划入VLAN20

在终端上：模拟器上手动配置静态IP地址

#### 具体步骤

1. 首先，通过输入命令`undo info-center enable`来避免配置过程中出现的提示消息中断，确保命令输入流程顺畅。
2. 创建两个VLAN，即VLAN 10和VLAN 20，或者通过命令 `vlan batch 10 20` 实现批量创建VLAN，提高效率。
3. 配置VLAN的虚拟接口，分别为VLAN 10和VLAN 20设置IP地址`192.168.1.254`和`192.168.20.254`，作为PC1和PC2的默认网关。
4. 将物理接口`g0/0/1`和`g0/0/2`分别设置为接入模式（access），并分配给`VLAN 10`和`VLAN 20`，实现三层交换机上的VLAN配置。
5. 在两台电脑上配置静态IP地址，作为后续动态IP配置的基础步骤，确保网络连接的稳定性和可达性。

#### 配置命令

##### 交换机配置

```cmd
<Huawei>system-view
Enter system view, return user view with Ctrl+Z.
[Huawei]undo info-center enable
Info: Information center is disabled.
```

创建VLAN

```cmd
[Huawei]vlan batch 10 20  //批量创建VLAN
Info: This operation may take a few seconds. Please wait for a moment...done.
[Huawei]dis vlan ?
  INTEGER<1-4094>  VLAN ID
  summary          Summary information of vlans
  |                Matching output
  <cr>             

[Huawei]dis vlan summary  //显示VLAN摘要
static vlan:
Total 3 static vlan.
  1 10 20    //可以看到已经创建好了VLAN10和VLAN20

dynamic vlan:
Total 0 dynamic vlan.

reserved vlan:
Total 0 reserved vlan.

```

配置VLAN虚拟接口，分别作为PC1和PC2的默认网关

```cmd
[Huawei]interface vlanif10
[Huawei-Vlanif10]ip address 192.168.10.254 24
[Huawei-Vlanif10]interface vlan20
[Huawei-Vlanif20]ip address 192.168.20.254 24
[Huawei-Vlanif20]quit
[Huawei]
```

将物理接口上的VLAN配置

GE0/0/1配置

```cmd
[Huawei]interface GigabitEthernet 0/0/1
[Huawei-GigabitEthernet0/0/1]dis this  //显示接口当前配置，可以看到没有任何配置
#
interface GigabitEthernet0/0/1
#
return
[Huawei-GigabitEthernet0/0/1]port link-type access  //配置接口模式为access
[Huawei-GigabitEthernet0/0/1]port default vlan 10  //将接口放入指定VLAN10
[Huawei-GigabitEthernet0/0/1]dis this  //显示接口当前配置，可以看到已经有了VLAN配置
#
interface GigabitEthernet0/0/1
 port link-type access
 port default vlan 10
#
return
[Huawei-GigabitEthernet0/0/1]quit
```
GE0/0/2使用同样配置

```cmd
[Huawei]interface GigabitEthernet 0/0/2
[Huawei-GigabitEthernet0/0/2]dis this  //显示接口当前配置，可以看到没有任何配置
#
interface GigabitEthernet0/0/2
#
return
[Huawei-GigabitEthernet0/0/2]port link-type access  //配置接口模式为access
[Huawei-GigabitEthernet0/0/2]port default vlan 20  //将接口放入指定VLAN10
[Huawei-GigabitEthernet0/0/2]dis this  //显示接口当前配置，可以看到已经有了VLAN配置
#
interface GigabitEthernet0/0/2
 port link-type access
 port default vlan 20
#
return
[Huawei-GigabitEthernet0/0/2]quit
```

##### 终端配置

PC1手动配置静态IP

![image-20250324230911627](https://img.yatjay.top/md/20250324230911679.png)

PC2手动配置静态IP

![image-20250324231011951](https://img.yatjay.top/md/20250324231012003.png)

应用上述配置后，用PC1去pingPC2，理论上应该可以ping通，可以看到已经能够ping通

![image-20250324231209971](https://img.yatjay.top/md/20250324231210026.png)

具体ping过程如下

- PC1判断目的地址192.168.20.1是否跟自己在同一网段，发现不在同一网段，将其转发至默认网关192.168.10.254
- 192.168.10.254在交换机上，交换机基于目的地址192.168.20.1查找路由表，发现路由表中有匹配条目(因为目的网段是该交换机直连的网段)
  - ![](https://img.yatjay.top/md/20250324231826345.png)


- 于是交换机将该数包从VLANIF20转发出去，实际上转发到了VLAN20当中，VLAN20中的PC2就能收到该报文

### DHCP配置实验



