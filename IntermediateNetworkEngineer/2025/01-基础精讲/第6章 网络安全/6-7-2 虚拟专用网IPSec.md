## 6-7 虚拟专用网

### 6-7-2 虚拟专用网IPSec

#### IPSec基础

IPSec（IP Security）是IETF定义的**一组**协议，用于<font color="red">增强IP网络的安全性</font>(工作在网络层，实际上是为IPv4网络打了一个安全补丁，IPv6协议通过扩展头保证安全性，其扩展头中就有AH、ESP扩展头，即IPv6可以通过扩展头集成IPSec功能)

IPSec协议集提供如下安全服务：

- 数据完整性(Data Integrity)
- 认证 (Autentication)
- 保密性 (Confidentiality)
- 应用透明安全性(Application-transparentSecurity)

#### IPSec原理

IPSec功能分为三类：认证头（AH）、封装安全负荷（ESP）、Internet密钥交换协议（IKE）。

- <font color="red">认证头（AH）</font>：提供数据完整性和数据源认证，但**不提供数据保密服务**(即不支持数据加密)，实现算法有MD5、SHA。

- <font color="red">封装安全负荷（ESP）</font>：提供数据**加密功能**，加密算法有DES、3DES、AES等。

- <font color="red">Internet密钥交换协议（IKE）</font>：用于生成和分发在ESP和AH中使用的密钥，加密算法有非对称的DH算法等

  (记忆下面表格)

![image-20250309084236281](https://img.yatjay.top/md/20250309084236311.png)

#### IPSec两种封装模式

- 传输模式：在原有IP报文中插入AH头部即可

- 隧道模式：需要**重新封装认证头**

<font color="red">传输模式开销小、效率高；隧道模式开销大、更安全</font>

![image-20250309084312661](https://img.yatjay.top/md/20250309084312699.png)

#### GRE虚拟专网

GRE（Generic Routing Encapsulation,通用路由封装）是网络层隧道协议，**GRE对组播等技术支持很好，但本身<font color="red">不加密</font>，而IPSec可以实现加密，对组播支持不佳。**所以语音、视频、路由协议等组播业务中<font color="red">经常先用GRE封装，然后再使用IPSec进行加密，即GRE over IPSec</font>。

GRE协议号为<font color="red">47</font>。

IPSec IKE端口<font color="red">UDP 500和4500</font>(NAT穿越时会用到4500)。

#### 例题

##### 例题1

![image-20250309084407585](https://img.yatjay.top/md/20250309084407625.png)

解析：

- B描述的是AH的功能
- C描述的是隧道模式
- D中IPSec是网络层的技术

##### 例题2

![image-20250309084423357](https://img.yatjay.top/md/20250309084423392.png)

解析：AH没有封装在TCP内部。

##### 例题3

![image-20250309084435086](https://img.yatjay.top/md/20250309084435115.png)

解析：IP数据是三层，则选择三层VPN，**要支持加密**则只能选IPsec，IPsec中ESP协议提供数据加密服务。

##### 例题4

![image-20250309084449332](https://img.yatjay.top/md/20250309084449379.png)

解析：本题考查IPSec数据封装的基础知识。

- 传输模式在原有IP报文中插入AH头部即可

- 隧道模式需要**重新封装认证头**，故③采用的隧道模式

##### 例题5

![image-20250309084502619](https://img.yatjay.top/md/20250309084502652.png)

解析：Diffie-Hellman简称DH，是密钥交换算法去(非对称)

##### 例题6

![image-20250309084514393](https://img.yatjay.top/md/20250309084514436.png)

解析：认证头协议AH不能加密，只对数据报进行验证、保证报文完整性。AH采用哈希算法（MD5或SHA），防止黑客截断数据包或者在网络中插入伪造的数据包，也能防止抵赖。

##### 例题7

![image-20250309084530380](https://img.yatjay.top/md/20250309084530413.png)

解析：网际层即网络层（3层），二层是网际接入层，不要混淆。L2TP是二层虚拟专网，本身不提供加密服务，可以使用IPSEC加密L2TP。TLS是四层安全协议，可以用于HTTPS。PPRP可能写错了，没有这个协议，如果是PPTP也是二层协议。

##### 例题8

![image-20250309084601140](https://img.yatjay.top/md/20250309084601182.png)

解析：AH只提供完整性校验，不提供数据机密性服务，数据机密性服务由ESP提供

##### 例题9

![image-20250309084610922](https://img.yatjay.top/md/20250309084610952.png)

解析：

- 支持加密功能是IPSec技术(ESP实现数据加密）
- GRE支持组波但不支持加密，L2TP不支持加密(PPTP支持)

##### 例题10

![image-20250309084622600](https://img.yatjay.top/md/20250309084622638.png)

解析：数据保密性（加密功能）是由ESP实现，AH提供完整性、源认证和抗重放攻击服务。

##### 例题11

![image-20250309084644038](https://img.yatjay.top/md/20250309084644081.png)

解析：IPSecSA由一个三元组来唯一标识，这个三元组包括<font color="red">安全参数索引 SPI（Security ParameterIndex）、目的IP地址和使用的安全协议号（AH或ESP）</font>(记忆)。