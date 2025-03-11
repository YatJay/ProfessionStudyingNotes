## 7-6 UOS Linux防火墙配置

本节不需要会写命令，但是题目中给出命令要能看得懂

### UOSLinux防火墙类型

#### Netfilter

Linux2.4内核开始提供防火墙软件框架**Netfilter**。

- 配置工具：<font color="red">firewalld、iptables、UFW (Uncomplicated Firewall)</font>

#### nftables

Linux3.13以上内核开始增加了新的数据包过滤框架**nftables**。

- 配置工具：<font color="red">nft</font>

- nftables在逐步取代Netfilter/iptables软件框架

### firewalld

firewalld（Dynamic Firewall Manager of Linux Systems, Linux,动态防火墙管理器）服务是UOS默认
的防火墙配置管理工具，配置命令为`firewall-cmd`。

1. 显示当前配置的规则。命令如下：`iptables-nL`
2. 配置本机<font color="red">开放`tcp 443`端口</font>，其中**`--permanent`表示永久生效**。命令如下：`firewall-cmd --permanent --add-port=443/tcp`
3. 配置允许<font color="red">源地址`10.0.30.5`访问本机的`tcp 3389`端口</font>。命令如下：`firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="10.0.30.5" port="3389" accept'`  
   - 永久允许来自IP地址 `10.0.30.5` 的IPv4流量访问本机3389端口。具体解析如下：
     1. **`firewall-cmd`**：firewalld的命令行工具，用于管理动态防火墙规则。
     2. **`--permanent`**：表示规则永久生效（重启后仍保留），需配合`--reload`重新加载配置。
     3. **`--add-rich-rule`**：添加一条“富规则”（Rich Rule），支持更复杂的条件组合（如IP、端口、协议等）。
     4. **规则内容解析**
        - `rule family="ipv4"` ：规则仅适用于IPv4协议
        - `source address="10.0.30.5"` ：匹配源IP地址为10.0.30.5的流量
        - `port="3389"` ：匹配目标端口为3389的流量（常用于Windows远程桌面）
        - `accept` ：接受符合条件的流量（放行）
4. 重载配置(即重启防火墙)，使之生效。命令如下：`firewall-cmd --reload`

### iptables

(iptables是重点考点，经常考察)

#### iptables的四表五链

##### 默认的4个规则表

| 规则表                            | 功能描述                                              |
| --------------------------------- | ----------------------------------------------------- |
| raw表                             | 确定是否对该数据包进行状态跟踪                        |
| mangle表                          | 为数据包设置标记                                      |
| nat表                             | 修改数据包中的源、目的IP地址或端口                    |
| <font color="red">filter表</font> | <font color="red">确定是否放行该数据包（过滤）</font> |

##### 默认的5种规则链

| 规则链                           | 功能描述                                |
| -------------------------------- | --------------------------------------- |
| <font color="red">INPUT</font>   | <font color="red">处理入站数据包</font> |
| <font color="red">OUTPUT</font>  | <font color="red">处理出站数据包</font> |
| <font color="red">FORWARD</font> | <font color="red">处理转发数据包</font> |
| PREROUTING                       | 在路由之前处理数据包                    |
| POSTROUTING                      | 在路由之后处理数据包                    |

##### 核心规则表－filter

以下是将图片中的信息转换为表格形式的结果：

| 规则表       | 定义                                                         | 包含的规则链                                                 |
| ------------ | ------------------------------------------------------------ | ------------------------------------------------------------ |
| raw表        | 确定是否对该数据包进行状态跟踪。                             | OUTPUT、PREROUTING                                           |
| mangle表     | 修改数据包内容，用来做流量整形的，给数据包设置标记。         | INPUT、OUTPUT、FORWARD、PREROUTING、POSTROUTING              |
| nat表        | 负责网络地址转换，用来修改数据包中的源目标IP地址或端口。     | OUTPUT、PREROUTING、POSTROUTING                              |
| **filter表** | **负责过滤数据包，确定是否放行该<font color="red">数据包（过滤）</font>。** | **<font color="red">- INPUT(控制入站数据包)<br/>- FORWARD(控制转发数据包，本机可能是一台中间设备如防火墙)<br/>- OUTPUT(控制出站数据包)</font>** |

#### iptables语法格式

**<font color="red">iptables【-t 表名】管理选项【链名】条件匹配 -j 执行动作</font>**

示例：`iptables -t filter -A INPUT -s 192.168.184.20 -p tcp --dport 22 -j DROP`，用来阻止（DROP）来自IP地址 `192.168.184.20` 的TCP协议访问本机的22端口（SSH服务）

通用参数：

- `-p`：协议 例：`iptables -A INPUT -p tcp`
- `-s`：源地址 例：`iptables -A INPUT -s 192.168.1.1`
- `-d`：目的地址 例：`iptables -A INPUT -d 192.168.12.1`
- `--sport`：源端口 例：` iptables -A INPUT -p tcp--sport 22`
- `--dport`：目的端口 例：`iptables -A INPUT -p tcp --dport 22`

- `-j`：处理动作：
  - DROP：丢弃(直接丢弃，不回显信息)
  - REJECT：丢弃(丢弃并回显ICMP不可达消息)
  - LOG：将数据包信息记录到syslog
  - ACCEPT：允许数据包通过

因此，`iptables -t filter -A INPUT -s 192.168.184.20 -p tcp --dport 22 -j DROP`具体解析如下：

| 参数              | 含义                                                        |
| ----------------- | ----------------------------------------------------------- |
| iptables          | Linux防火墙工具，用于配置内核的Netfilter框架规则            |
| -t filter         | 指定操作的规则表为filter表（默认表，用于过滤数据包）        |
| -A INPUT          | 追加（Append）**一条规则到**INPUT链（处理进入本机的数据包） |
| -s 192.168.184.20 | 匹配源IP地址为192.168.184.20的数据包                        |
| -p tcp            | 指定协议类型为TCP（传输控制协议）                           |
| --dport 22        | 匹配目标端口为22的数据包（SSH服务默认端口）                 |
| -j DROP           | 对匹配的数据包执行**丢弃（DROP）**动作（不响应、直接丢弃）  |

#### iptables配置案例

1. 显示当前配置的iptables规则：**`iptables -nL`**
2. 添加一条规则，所有源地址为10.0.80.10的数据包拒绝通过：`iptables -A INPUT -s 10.0.80.10 -j DROP`
3. 添加一条规则，允许外部访问本机的tcp80端口。命令如下：`iptables -A INPUT -p tcp --dport 80 -j ACCEPT`(经常考查此种类型)
4. 添加规则，允许源地址为10.0.30.5的主机访问本机的tcp3389端口：`iptables -A INPUT -s 10.0.30.5/32 -p tcp -m tcp --dport 3389 -j ACCEPT`(经常考查此种类型)
5. 配置保存生效：`service iptables save`

### nftables

nftables是一个新的数据包分类框架，新的Linux防火墙管理程序在Linux内核版本高于3.13时可用。

nftables的命令行工具为`nft`，命令格式与iptables命令不同。默认没有安装，在使用nft之前需要使用`yum install nftables`命令安装 nftables 服务。

- ```bash
  root@admin-PC:~# yum install nftables     #UOS服务器版用该命令
  ```

- ```bash
  root@admin-PC:~# apt-get install nftables     #UOS桌面版用该命令
  ```

  

#### nftables配置

##### 查看表

查看表命令示例：

- `nft list tables` ：查看所有表
- `nft list table ip filter`：查看ip簇filter表的所有规则
- `nft list chain filter INPUT`：查看filter表INPUT链的规则，默认为ip簇
- `nft list ruleset`：查看所有规则

```bash
root@admin-PC:~# nft list ruleset
table ip filter {
	chain INPUT {
	type filter hook input priority O; policy accept; #这是INPUT链的规则，可以看到最后一条是policy accept默认允许																					(即黑名单技术，默认全部允许，只有加入到黑名单中的规则才拒绝)
						}
	chain FORWARD {
	type filter hook forward priority O; policy accept; #这是FORWARD链的规则
								}
	chain OUTPUT {
	type filter hook output priority O; policy accept; #这是OUTPUT链的规则
							}
	}
```

##### 表操作

表操作。表的操作符包括：

- `add`：添加表。
- `delete`：删除表。
- `list`：显示一个表中的所有规则链和规则。
- `flush`：清除一个表中的所有规则链和规则。

命令示例：

- `nft list tablee ip filter`：查看ip簇filter表的所有规则
- `nft add tablee ip filter`：创建ip簇的filter表
- `nft add tablee ipv6 filter`：创建ipv6簇的filter表

##### 链操作

链操作。链的操作符包括：

- **`add`：将一条规则链添加到表**
- **`create`：在表中创建规则链**
- **`delete`：删除一条规则链**
- `flush`：清除一条规则链里的所有规则
- `list`：显示一条规则链里的所有规则

- `rename`：修改一条规则链的名称

基本规则链类型包括：

- **`filter`：数据包过滤**
- **`route`：用于数据包路由**
- **`nat`：用于网络地址转换**

命令示例：`nft add chain ip filter test { type filter hook input priority 0\; }`

命令执行后效果：

```bash
table ip filter{
	chain test { type filter hook input priority filter; policy accept; }
}
```

这条命令的作用是创建一个名为test的链，它:

- 属于 filter 表，用于过滤流量
- 挂载在input钩子上，处理所有进入本机的数据包
- 具有默认的优先级0

##### 规则配置

规则配置。规则配置的操作符包括：

- `add`：添加一条规则
- `insert`：在规则链中加入一个规则，可以添加在规则链开头或指定的地方
- `delete`：删除一条规则

示例：`nft add rule filter test tcp dport 22 accept`：filter表的INPUT链增加规则，开放tcp 22端口，命令执行后效果：

```
table ip filter{
	chain test { type filter hook input priority filter; policy accept; tcp dport 22 accept }
}
```

### 例题

#### 例题1

![image-20250311201712711](https://img.yatjay.top/md/20250311201712749.png)



#### 例题2

![image-20250311201719388](https://img.yatjay.top/md/20250311201719427.png)



#### 例题3

![image-20250311201727394](https://img.yatjay.top/md/20250311201727440.png)

解析：

- iptables是linux下的包过滤防火墙，根据题意源地址是192.168.0.2，排除B和D
- 目标端口应该是22，排除A。
- （d是destination缩写，s是source缩写）

#### 例题4

![image-20250311201736291](https://img.yatjay.top/md/20250311201736336.png)

解析：-P：设置默认策略，设定默认门是关着的还是开着的？默认策略一般只有两种

`iptables -P INPUT(DROP|ACCEPT)`，默认是关的/默认是开的，比如：`iptables-P INPUT DROP`。

小写的p才是协议，后面可以跟TCP、UDP或ICMP等。

#### 例题5

![image-20250311201744021](https://img.yatjay.top/md/20250311201744058.png)

解析：送分题，按着题意理解即可，相当于在服务器Linux系统上做访问控制，故目的端口是22。

#### 例题6

![image-20250311201753139](https://img.yatjay.top/md/20250311201753191.png)

解析：

- Linux中的防火墙工具是iptables，DROP表示拒绝，且不做响应，REJECT是拒绝，要返回报错信息

- /24表示一个网段；/32表示一个主机

#### 例题7

![image-20250311201803618](https://img.yatjay.top/md/20250311201803655.png)

![image-20250311201810836](https://img.yatjay.top/md/20250311201810881.png)

解析：翻译如下：

1. 允许所有主机访问本地443端口（HTTPS服务）
2. 允许源IP地址为192.168.1.254的主机**通过 ICMP type8 访问本机，即放行Ping流量**
3. 丢弃源IP地址为192.168.2.0/24的流量
4. 允许源IP地址为192.168.3.0/24的主机通过SSH登录本机
5. 允许本机的回环网络接口（lo，即loopback接口）的所有入站流量
6. 允许本机发出ICMP type 0的报错信息
