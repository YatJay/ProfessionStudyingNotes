## 9-3 SNMP

### SNMP－2个服务2个端口5个报文【225】

SNMP为应用层协议，通过<font color="red">UDP</font>承载端口**161和162**

<font color="red">不可靠，但效率高</font>，网络管理不会太多增加网络负载。

#### SNMP协议的操作－2个端口

![image-20250318221623894](https://img.yatjay.top/md/20250318221623933.png)

- 在<font color="red">客户机Agent</font>上安装<font color="red">SNMPService服务</font>，默认端口为<font color="red">UDP 161</font>，用来监听网管服务器的请求：网管服务器通过随机端口访问Agent的161端口，向其发送`get-request`等请求，获取Agent信息
- 在<font color="red">网管服务器NMS</font>上安装<font color="red">SNMPTrap服务</font>，默认端口为<font color="red">UDP 162</font>，用来监听客户机Agent发送的异常信息：客户机Agent如有异常，使用自己的随机端口访问网管服务器的162端口，反馈自己的信息

#### SNMP协议的操作－5个报文

- 为了简化书写，前三个可以简写为：<font color="red">get、get-next和set</font>
- SNMP双端口：客户端用端口**161来接收get/set**，服务器端用端口**162来接收trap**。

| 操作编号 | 分类                         | 名称             | 用途                         |
| -------- | ---------------------------- | ---------------- | ---------------------------- |
| 0        |                              | get-request      | 查询一个或多个变量的值       |
| 1        | 网管找客户端（领导找下属）   | get-next-request | 在MIB树上检索下一个变量      |
| 2        |                              | set-request      | 对一个或多个变量的值进行设置 |
| 3        | 客户端反馈（下属向领导汇报） | get-response     | 对get/set报文做出响应        |
| 4        |                              | trap             | 向管理进程报告代理发生的事件 |

### 例题

#### 例题1

![image-20250318221933563](https://img.yatjay.top/md/20250318221933603.png)

#### 例题2

![image-20250318221942598](https://img.yatjay.top/md/20250318221942636.png)

解析：管理端查询代理的状态用get报文，目标端口是161。

#### 例题3

![image-20250318221952188](https://img.yatjay.top/md/20250318221952230.png)

解析：SNMP Service使用端口161，SNMPTrap使用端口162。

#### 例题4

![image-20250318221959908](https://img.yatjay.top/md/20250318221959937.png)

解析：trap报文目的端口是162（网管服务器端)

#### 例题5

![image-20250318222008965](https://img.yatjay.top/md/20250318222009001.png)

解析：本题是客户端发给网管服务器的报文，排报告问题用trap。

#### 例题6

![image-20250318222021212](https://img.yatjay.top/md/20250318222021249.png)

解析：掌握SNMP的五个报文，trap用于上报异常事件（即主动通知管理者有关网络事件的发生)

### SNMP版本对比

SNMP 有三种版本：SNMPv1、SNMPv2c和 SNMPv3。

- SNMPv1：基于团体名认证，<font color="red">安全性较差</font>，且返回报文的错误码也较少。
- SNMPv2c：引入了<font color="red">GetBuIk和lnform操作</font>，支持更多的标准错误码信息，**支持更多的数据类型**。
- SNMPv3：在前两版的基础上重新定义了<font color="red">网络管理框架和安全机制</font>，提供了基于USM（UserSecurity Module)的<font color="red">认证加密</font>和基于VACM(View-basedAccessControl Model)的<font color="red">访问控制</font>(v1、v2都没有安全机制)

#### SNMPv1机制与问题

- SNMPv1网络管理中，管理站和代理站之间可以是<font color="red">一对多关系</font>，也可以是<font color="red">多对一关系</font>。

- RFC1157规定SNMP基本认证和控制机制，通过**团体名**（community）(可以理解为密码)验证实现。
- 团体名Community<font color="red">明文传输，不安全</font>。

![image-20250318222304415](https://img.yatjay.top/md/20250318222304453.png)

#### SNMPv2

SNMPv2增加定义了GetBuk和inform两个新协议操作。

- <font color="red">GetBulk</font>：快速获取大块数据(bulk，大部分)
- <font color="red">Inform</font>：允许一个NMS向另一个NMS发送Trap信息/接收响应消息即允许两个网管服务器之间通信，用来同步一些信息。

![image-20250318222404580](https://img.yatjay.top/md/20250318222404699.png)

#### SNMPv3

SNMPv3重新定义了网络管理框架和安全机制。

- 重新定义网络管理框架：将前两版中的管理站和代理统一叫做<font color="red">SNMP实体（entity）</font>
- 安全机制：<font color="red">认证和加密传输</font>。
  - **时间序列模块**：提供重放攻击防护。
  - **认证模块**：完整性和数据源认证，使用SHA或MD5。
  - **加密模块**：防止内容泄露，使用DES算法。
- 有两种威胁是SNMPv3没有防护的：<font color="red">拒绝服务和通信分析</font>。

#### 例题

##### 例题1

![image-20250318222557935](https://img.yatjay.top/md/20250318222557978.png)

解析：团体名相当于密码，相同才能实现管理

##### 例题2

![image-20250318222605196](https://img.yatjay.top/md/20250318222605239.png)

解析：SNMPv2特有的就是GetBulk和Inform消息：

- <font color="red">GetBulk</font>：快速获取大块数据(bulk，大部分)
- <font color="red">Inform</font>：允许一个NMS向另一个NMS发送Trap信息/接收响应消息即允许两个网管服务器之间通信，用来同步一些信息。

##### 例题3

![image-20250318222613222](https://img.yatjay.top/md/20250318222613262.png)

解析：A和D选项是SNMPv2的功能，B选项是SNMPv1的功能

##### 例题4

![image-20250318222622773](https://img.yatjay.top/md/20250318222622821.png)

解析：

- SNMP基于UDP，可靠性差，但效率高；SNMPv2c没有增加安全性，而SNMPv3主要增加了安全性。
- D选项：**OSPFv3专为管理IPv6设备而定制**

### 管理数据库MIB-2

被管理对象标识符OID

![image-20250318222647725](https://img.yatjay.top/md/20250318222647760.png)

#### 例题

![image-20250318222728767](https://img.yatjay.top/md/20250318222728818.png)