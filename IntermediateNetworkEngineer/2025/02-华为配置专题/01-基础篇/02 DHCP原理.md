## 动态主机配置协议DHCP

### 手动配置网络参数的问题

- 参数多，小白不会配：普通用户对于网络参数不了解，经常配置错误，导致无法正常访问网络。随意配置IP地址导致地址冲突更是时常发生。
  - ![image-20250324201104372](https://img.yatjay.top/md/20250324201104410.png)

- 工作量大
  - 由网络管理员统一配置，工作量巨大，属于重复性劳动。
  - 网络管理员需要提前对IP地址进行规划、分配到个人。
  - ![image-20250324201117987](https://img.yatjay.top/md/20250324201118024.png)
- 利用率低：企业网中每个人固定使用月一个IP地址，IP地址利用率低，有些地址可能长期处于未使用状态。
  - ![image-20250324201129537](https://img.yatjay.top/md/20250324201129566.png)
- 灵活性差：WLAN（WirelessLocal Area Network，无线局域网）的出现使终端位置不再固定，当无线终端移动到另外一个无线覆盖区域时，可能需要再次配置IP地址。
  - ![image-20250324201151938](https://img.yatjay.top/md/20250324201151974.png)

### DHCP原理

#### DHCP概念

- **DHCP（Dynamic Host Configuration Protocol，动态主机配置协议）** 实现网络动态分配IP地址。
- **DHCP采用C/S构架**，主机无需配置，从服务器端获取地址，可实现接入网络后即插即用。

![image-20250324201231089](https://img.yatjay.top/md/20250324201231126.png)

#### DHCP工作原理

![image-20250324201311920](https://img.yatjay.top/md/20250324201311960.png)

- **DHCP Discover（广播）**：用于发现当前网络中的DHCP服务器端。
- **DHCP Offer（单播）**：携带分配给客户端的IP地址。
- **DHCP Request（广播）**：告知服务器端自己将使用该IP地址。
- **DHCP Ack （单播）**：最终确认，告知客户端可以使用该IP地址。

为什么客户端收到Offer之后不直接使用该IP地址，还需要发送一个Request告知服务器端？

**因为网络中可能存在多个DHCP服务器，客户端可能收到多个Offer，需要使用DHCP Request报文来告知DHCP服务端**

#### DHCP租期更新

![image-20250324201421461](https://img.yatjay.top/md/20250324201421516.png)

- **DHCP Request（单播）**：向服务器端请求继续使用该IP地址，延长使用期限。
- **DHCP Ack （单播）**：告知客户端可以继续使用该IP地址，可使用时间刷新到租期时长（Lease）。

在**50%**租期时客户端未得到原服务器端的回应，则客户端在**87.5%**租期时会广播发送DHCP Request，任意一台DHCP服务器端都可回应，该过程称为重绑定。请求失败时获得`169.254.x.x`这种特殊IP地址

如果租期已满时客户端未得到原服务器端的回应，则重新开始DHCP Discover过程申请新的IP地址

### DHCP配置

#### 基于接口的DHCP配置

基于接口即在某个接口上配置DHCP服务

|      | 步骤                                               | 命令                                                         |
| ---- | -------------------------------------------------- | ------------------------------------------------------------ |
| 1    | 开启DHCP功能                                       | `[Huawei] dhcp enable`                                       |
| 2    | 开启接口采用接口地址池的DHCP服务器端功能           | `[Huawei-GigabitEthernet0/0/0] dhcp select interface`        |
| 3    | 指定接口地址池下的DNS服务器地址                    | `[Huawei-GigabitEthernet0/0/0] dhcp server dns-list ip-address` |
| 4    | 配置接口地址池中不参与自动分配的IP地址范围         | `[Huawei-GigabitEthernet0/0/0] dhcp server excluded-ip-address start-ip-address [ end-ip-address ]` |
| 5    | 配置DHCP服务器接口地址池中IP地址的租用有效期限功能 | `[Huawei-GigabitEthernet0/0/0] dhcp server lease { day day [ hour hour [ minute minute ] ]` |

注意：基于接口的DHCP配置简单粗暴，不用配置DHCP地址池，参与分配的地址就是**和该接口同一个网段**的地址；不用配置网关地址，网关地址就是该接口的地址。也就是说：

- 接口地址池的网段由接口的IP地址和子网掩码决定。例如，若接口IP为 `192.168.1.1/24`，则DHCP地址池范围默认为 `192.168.1.0/24` （需排除手动配置的地址）

- 客户端获取的**网关地址** 自动设置为接口的IP地址，无需额外配置

#### 基于接口的DHCP配置案例

需求描述：配置一台路由器作为DHCP服务器端，使用接口`GE0/0/0`所属的网段作为DHCP客户端的地址池，同时将接口地址设为DNS Server地址，租期设置为3天。

![image-20250324202018032](https://img.yatjay.top/md/20250324202018064.png)

配置步骤：全局使能DHCP服务，进入接口视图下，关联当前接口到DHCP地址池，在接口视图下配置DNS地址、排除地址（将接口自身地址排除在外），同时配置给客户端分配IP地址的租期。

```cmd
[Huawei]dhcp enable 
[Huawei]interface GigabitEthernet0/0/0 
[Huawei-GigabitEthernet0/0/0]dhcp select interface 
[Huawei-GigabitEthernet0/0/0]dhcp server dns-list 10.1.1.2
[Huawei-GigabitEthernet0/0/0]dhcp server excluded-ip-address 10.1.1.2
[Huawei-GigabitEthernet0/0/0]dhcp server lease day 3
```

#### 基于全局地址池的DHCP配置

|      | 功能描述                                   | 命令                                                         |
| ---- | ------------------------------------------ | ------------------------------------------------------------ |
| 1    | 创建全局地址池                             | `[Huawei] ip pool ip-pool-name`                              |
| 2    | 配置全局地址池可动态分配的IP地址范围       | `[Huawei-ip-pool-2] network ip-address [ mask { mask | mask-length } ]` |
| 3    | 配置全局地址池中不参与自动分配的IP地址范围 | `[Huawei-ip-pool-2] excluded-ip-address start-ip-address [ end-ip-address ]` |
| 4    | 配置DHCP客户端的网关地址                   | `[Huawei-ip-pool-2] gateway-list ip-address`                 |
| 5    | 配置DHCP客户端使用的DNS服务器的IP地址      | `[Huawei-ip-pool-2] dns-list ip-address`                     |
| 6    | 配置IP地址租期                             | `[Huawei-ip-pool-2] lease { day day [ hour hour [ minute minute ] ] | unlimited }` |
| 7    | 使能接口的DHCP服务器功能                   | `[Huawei-GigabitEthernet0/0/0] dhcp select global`           |

#### 基于全局的DHCP配置案例

需求描述：配置一台路由器作为DHCP服务器端，配置全局地址池`ip pool 2`为DHCP客户端分配IP地址；分配地址为`1.1.1.0/24`网段，网关地址`1.1.1.1`，DNS地址同样也是`1.1.1.1`，租期10天，在`GE0/0/0`接口下调用全局地址池。

![image-20250324202018032](https://img.yatjay.top/md/20250324202018064.png)

配置步骤：

- 全局使能DHCP服务，配置全局地址池pool2。在pool2中配置地址池范围、网关地址、DNS地址、租期。

- 最后在具体的接口中配置选择全局地址池。当GE0/0/o收到DHCP请求就会从全局地址池中进行IP地址分配。

```cmd
[Huawei]dhcp enable 
[Huawei]ip pool pool2 
Info: It's successful to create an IP address pool.
[Huawei-ip-pool-pool2]network 1.1.1.0 mask 24 
[Huawei-ip-pool-pool2]gateway-list 1.1.1.1 
[Huawei-ip-pool-pool2]dns-list 1.1.1.1 
[Huawei-ip-pool-pool2]lease day 10 
[Huawei-ip-pool-pool2]quit 
[Huawei]interface GigabitEthernet0/0/0 
[Huawei-GigabitEthernet0/0/1]dhcp select global
```