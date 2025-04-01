## 10 浮动路由和BFD配置

浮动路由（Floating Static Route）是一种网络冗余技术，通过配置多条通往相同目的地的路由路径，并设置不同的优先级（管理距离），实现主备链路的自动切换。

实验拓扑：

![image-20250330194204827](https://img.yatjay.top/md/20250330194204893.png)

实验目的：通过配置浮动路由，使得流量正常时都走电信路由器，当电信路由器故障时，自动切换至联通路由器

### 基本配置

#### 设备及接口配置IP地址

```bash
interface GigabitEthernet 0/0/1
ip address 192.168.1.1 24
undo shutdown  # 一般配置IP地址后接口会自动启用
```

验证命令：

- `display interface brief`：查看接口状态。
- `display ip interface brief`：确认IP地址配置。

#### OSPF配置：运营商与互联网路由器运行OSPF协议

dianxin、yidong、Internet三个路由器之间运行OSPF协议，以电信路由器为例，配置如下

```cmd
[dianxin]ospf 1
[dianxin-ospf-1]area 0.0.0.0
[dianxin-ospf-1-area-0.0.0.0]network 0.0.0.0 0.0.0.0  # 直接宣告8个0，将自己直连的所有网段宣告出去
```

##### 基础配置步骤

1. **登录设备并进入系统视图**  

   ```bash
   <Huawei> system-view
   ```

2. **创建OSPF进程**  
   ```bash
   [Huawei] ospf [process-id]  # 进程ID范围1-65535，默认为1
   ```

3. **配置路由器ID（可选但推荐）**  

   ```bash
   [Huawei-ospf-1] router-id x.x.x.x  # 建议使用Loopback接口IP
   ```

4. **配置区域并宣告网络**  

   直接宣告8个0，可以将自己直连的所有网段宣告出去

   ```bash
   [Huawei-ospf-1] area 0  # 骨干区域通常为0
   [Huawei-ospf-1-area-0.0.0.0] network [IP网段] [反掩码]  
   # 示例：network 192.168.1.0 0.0.0.255
   # network 0.0.0.0 0.0.0.0 直接宣告8个0，将自己直连的所有网段宣告出去
   ```

5. **在接口启用OSPF**  
   ```bash
   [Huawei] interface GigabitEthernet0/0/1
   [Huawei-GigabitEthernet0/0/1] ospf enable 1 area 0  # 指定进程和区域
   ```

##### 验证与排错

- **查看邻居状态**  

```bash
display ospf peer
```
- **检查LSDB**  

```bash
display ospf lsdb
```
- **验证接口配置**  

```bash
display ospf interface GigabitEthernet0/0/1
```

- **验证是否学习到路由表**

```bash
display ip routing-table
```

#### NAT配置：出口路由器运行NAT进行地址转换

192.168.x.x私网地址无法在互联网使用，需要进行NAT

##### 配置ACL





##### 出口NAT绑定ACL



#### 默认路由：出口路由器配置默认路由指向运营商路由器





查看路由表，验证默认路由配置





#### 验证配置：PC去ping互联网路由器

PC1去ping 22.22.22.22，通





PC2去ping 22.22.22.22，通



tracert跟踪，发现PC1和PC2都是使用xx运营商的路由器到达互联网





#### 运行负载均衡：基于源IP地址的负载均衡







再次tracert跟踪，发现PC1和PC2还是都使用xx运营商的路由器到达互联网





原因是：华为的AR系列低端路由器，无法基于单个数据包做负载均衡，基于单个包做负载均衡，对CPU和缓存的消耗较大。高端的NE系列可以，有兴趣可以验证





下面，我们为出口路由器配置浮动路由



### 浮动路由配置：设置路由条目preference字段调整优先级

路由表中有一个优先级字段`preference`，越小越优先。

通过调整路由表中条目的优先级字段，可以使得电信所在路由更优先

具体做法是：

1. 增大联通路由条目的优先级字段值，使其优先级降低
2. 减小电信路由条目的优先级字段值，使其优先级增高

下面我们通过增大联通路由条目的优先级字段值，实现实验目的

```bash
[router]ip route-static 0.0.0.0 13.1.1.2 preference 100
```

查看路由表，可以看到电信路由条目的优先级字段值为60，优先级更高









测试PC访问互联网服务器并使用`tracert`跟踪，发现都是通过电信路由器到达互联网







手动挂掉电信出接口：可以挂出口路由器的出电信接口，也可以挂电信路由器的入接口

```

```



再次测试PC访问互联网服务器并使用`tracert`跟踪，发现都是通过联通路由器到达互联网，浮动路由生效









我们尝试改变网络结构，在出口路由器和电信路由器之间加入一台二层交换机，然后挂掉电信路由器的入接口







再次测试PC访问互联网服务器并使用`tracert`跟踪，发现无法访问互联网。这是因为此时出口路由器只知道直连到交换机这段链路正常，仍然优先使用电信路由条目进行转发。

实际项目中，出口路由器到运营商之间往往不是直连，中间要经过许多跳，因此仅仅通过浮动路由配置无法实现路由自动切换，应当使用BFD技术

### BFD配置

BFD（BidrectionalForwardingDetection,双向转发检测）用于快速检测系统设备之间的**发送和接受两个方向的通信故障**，并在出现故障时通知生成应用。BFD广泛用于链路故障检测，并能实现与接口、静态路由、动态路由等联动检测。

![image-20250330191224944](https://img.yatjay.top/md/20250330191224991.png)

