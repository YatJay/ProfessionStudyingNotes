## 5-10 Internet应用

### 远程登录

|  协议  |              端口               |             传输方式              | 安全性 |                             备注                             |
| :----: | :-----------------------------: | :-------------------------------: | :----: | :----------------------------------------------------------: |
|  SSH   | <font color="red">TCP 22</font> | <font color="red">加密传输</font> |  安全  |    用于远程登录管理设备<br/>Windows 10 后**默认启用 SSH**    |
| Telnet | <font color="red">TCP 23</font> | <font color="red">明文传输</font> | 不安全 | 用于远程登录管理设备<br>Windows 10 及更新版本**默认关闭 Telnet** |

#### 例题

##### 例题1

![image-20250305200858597](https://img.yatjay.top/md/20250305200858636.png)

解析：SFTP = SSH + FTP

##### 例题2

![image-20250305200908426](https://img.yatjay.top/md/20250305200908460.png)

### 文件传输协议

| 协议 | 端口                               | 控制端口                    | 数据端口                    | 描述                                                         |
| ---- | ---------------------------------- | --------------------------- | --------------------------- | ------------------------------------------------------------ |
| FTP  | <font color="red">TCP 21/22</font> | <font color="red">21</font> | <font color="red">20</font> | 文件传输协议，用于在客户端和服务器之间进行文件传输。控制连接用于发送指令，数据连接用于传输文件内容。 |
| TFTP | <font color="red">UDP 69</font>    | <font color="red">69</font> | 无需固定数据端口            | 简单文件传输协议，通常用于局域网内传输小文件，配置简单但不支持复杂操作如身份验证等。 |

客户端访问：浏览器或Windows搜索ftp://192.168.1.10

命令行访问：DOS下ftp命令访问

- dir：展示目录下的文件
- <font color="red">get：从服务器端下载文件</font>
- <font color="red">put：向FTP服务器端上传文件</font>
- lcd：设置客户端当前的目录
- bye：退出FTP连接

#### 例题

##### 例题1

![image-20250305200921643](https://img.yatjay.top/md/20250305200921674.png)

解析：建立连接使用21端口即控制端口

##### 例题2

![image-20250305200932522](https://img.yatjay.top/md/20250305200932568.png)

解析：FTP控制端口是21，数据端口是20，基于TCP

### 电子邮件协议

| 协议 | 默认端口    | 服务     | 描述                                               |
| ---- | ----------- | -------- | -------------------------------------------------- |
| SMTP | **TCP 25**  | 发送邮件 | 使用ASCII编码的文本文件进行邮件发送的基础协议。    |
| POP3 | **TCP 110** | 接收邮件 | 邮件接收协议，通常用于从邮件服务器下载邮件到本地。 |
| IMAP | **TCP 143** | 同步邮件 | 提供邮件同步功能，允许在多个设备间同步邮件状态。   |

注意：SMTP邮件采用<font color="red">ASCII编码的文本(Text)文件</font>，多用途互联网邮件扩展<font color="red">MIME</font>，能让邮件扩展支持多媒
体，保证安全的是<font color="red">S/MIME</font>。

下图解释了发送方和接收方处于同一个邮件服务器下，发送方通过SMTP协议发送邮件到邮件服务器，接收方通过POP3协议从邮件服务器接收邮件的过程

![image-20250305202228917](https://img.yatjay.top/md/20250305202228966.png)

若发送方和接收方处于不同的邮件服务器之下，那么中间还存在一步邮件服务器之间的交换过程

![image-20250305205947564](https://img.yatjay.top/md/20250305205947611.png)

详见下例题1

#### 例题

##### 例题1

![image-20250305200957783](https://img.yatjay.top/md/20250305200957822.png)

##### 例题2

![image-20250305201010317](https://img.yatjay.top/md/20250305201010368.png)

##### 例题3

![image-20250305201017942](https://img.yatjay.top/md/20250305201017982.png)

##### 例题4

![image-20250305201029263](https://img.yatjay.top/md/20250305201029312.png)

### 超文本传输协议HTTP

| 协议  | 默认端口    | 安全性 | 描述                                                         |
| ----- | ----------- | ------ | ------------------------------------------------------------ |
| HTTP  | **TCP 80**  | 不加密 | 超文本传输协议，用于在Web浏览器和网站服务器之间传输网页内容。 |
| HTTPS | **TCP 443** | 加密   | HTTPS=HTTP+SSL<br>HTTP协议加上SSL/TLS加密，保护数据传输的安全性和完整性。 |

统一资源定位符（UniformResourceLocators,URL)

http://www.wxyz.org/welcome.html

- 其中http是协议名称
- www.w3.org是服务器主机名
- welcome.html是网页文件名

#### 网页访问过程

如果用户选择了一个要访问的网页，则浏览器和Web服务器的交互过程如下：

1. 浏览器接收URL。
2. 浏览器通过DNS服务器查找www.w3.org的IP地址。
3. DNS给出IP地址18.23.0.32。
4. 浏览器与主机(18.23.0.32)的端口80建立TCP连接。
5. 浏览器发出请求GET/welcome.html文件。
6. www.w3.org服务器发送welcome.html文件。
7. 释放TCP连接。
8. 浏览器显示welcome.html文件

<font color="red">GET</font>是HTTP协议提供的少数操作方法中的一种，其含义是读一个网页。常用的还有<font color="red">HEAD</font>（读网页头信息)和<font color="red">POST </font>(把消息加到指定的网页上)等。

#### 例题

##### 例题1

![image-20250305201044411](https://img.yatjay.top/md/20250305201044450.png)

##### 例题2

![image-20250305201052088](https://img.yatjay.top/md/20250305201052118.png)

### P2P应用

文件传输、电子邮件、网页浏览都采用了<font color="red">C/S或B/S模式</font>。

另外一种应用模式叫作<font color="red">点对点应用（Peer-to-Peer，P2P）</font>，在这种模式中，没有客户机和服务器的区别，<font color="red">每一个主机既是客户机，又是服务器</font>，它们的角色是对等的，没有中心节点，P2P是一种<font color="red">对等通信</font>的网络模型。

- BitTorrent是最早出现的P2P文件共享协议
- Kademlia算法

![image-20250305210839986](https://img.yatjay.top/md/20250305210840019.png)

#### 例题

##### 例题1

![image-20250305201104687](https://img.yatjay.top/md/20250305201104719.png)

##### 例题2

![image-20250305201120874](https://img.yatjay.top/md/20250305201120921.png)

![image-20250305201130693](https://img.yatjay.top/md/20250305201130733.png)

##### 例题3

![image-20250305201141699](https://img.yatjay.top/md/20250305201141737.png)

解析：P2P网络是区块链技术去中心化的网络架构
