## 09 VRRP配置

> 笔者对网络设备的物理层、数据链路层、网络层的思考：
>
> - 交换机的电口、网口属于物理层，并和IP地址无直接关系。当接口被划入VLAN时，和数据链路层建立联系
> - 交换机的MAC地址、VLAN属于数据链路层，并和IP地址无直接关系。当VLAN配置VLANIF时，和网络层建立联系
> - 交换机的IP地址、子网(IP地址+子网掩码可以得到子网信息)，和IP地址有直接关系

### 交换机实现三层通信的方式

华为交换机的物理接口如果工作在二层（数据链路层），无法直接配置IP地址。若需实现三层通信，需通过以下两种方法配置IP地址：

#### 方法1：通过VLAN虚拟接口（Vlanif）配置IP

一些三层交换机无法直接为接口配置IP地址(华为模拟器中一些交换机无法直接给接口配IP地址)，我们将其接口划入指定VLAN，三层通信就能通过VLANIF来实现了

1. **创建VLAN并绑定物理接口**  
   
   ```bash
   <Huawei> system-view
   [Huawei] vlan 10               # 创建VLAN 10
   [Huawei-vlan10] quit
   [Huawei] interface gigabitethernet 0/0/1   # 进入物理接口
   [Huawei-GigabitEthernet0/0/1] port link-type access  # 设置为access模式
   [Huawei-GigabitEthernet0/0/1] port default vlan 10   # 绑定到VLAN 10
   ```
   
2. **为VLAN配置IP地址**  
   
   ```bash
   [Huawei] interface vlanif 10    # 进入VLAN 10的虚拟接口
   [Huawei-Vlanif10] ip address 192.168.1.1 24  # 配置IP及掩码
   ```
   **原理**：通过Vlanif接口实现三层转发，物理接口仅处理二层流量。
   
#### 方法2：将物理接口切换为三层模式（需支持）

三层交换机：为三层交换机接口配置IP地址，直接实现三层通信(物理接口上直接配置IP地址)

二层交换机：部分华为交换机支持将端口从二层切换为三层模式：

```bash
[Huawei] interface gigabitethernet 0/0/1
[Huawei-GigabitEthernet0/0/1] undo portswitch  # 关闭二层交换功能
[Huawei-GigabitEthernet0/0/1] ip address 192.168.1.1 24  # 直接配置IP
```
注意： 

- 执行`undo portswitch`后需等待30秒以上再配置IP。
- 若命令报错，可能设备型号不支持此功能，需改用Vlanif方案。

#### 常见问题

1. **无法配置IP** 
   - 确认接口已绑定VLAN（方法1）或成功切换为三层模式（方法2）。
   - 检查子网掩码格式（如`255.255.255.0`或简写`24`）。

2. **DHCP自动分配IP**：若需通过DHCP分配IP，需在Vlanif接口启用DHCP服务或中继功能。

### 搭建拓扑

![image-20250410215016909](https://img.yatjay.top/md/20250410215016987.png)

### 基础配置

为PC和Server配置IP地址



在接入交换机上划分VLAN，并将接口划入指定VLAN
