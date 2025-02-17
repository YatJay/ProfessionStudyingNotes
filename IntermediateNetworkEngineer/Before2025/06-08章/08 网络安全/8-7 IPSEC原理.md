# 8-7 IPSEC原理

## IPSEC简介

**IPSec (Intemet Protocol Security)**是**IETF定义的一组协议**，用于增强IP网络的安全性

IPSec来源：IPSec本身是为IPv6设计，IPv6本身集成安全功能。由于IPv4使用多且IPv4本身使用明文传输，被引入到IPv4网络

IPSec协议集提供如下安全服务:

- 数据完整性(Data Integrity)：完整性校验
- 认证(Autentication)：认证算法灵活选择
- 保密性(Confidentiality)：加密
- 应用透明安全性(Application-transparent Security)：对上层应用是透明的，上层应用不用管网络层的IPSec

## IPSec原理(非常重要，背下)

lPSec功能分为三类：**认证头(AH)、封装安全负荷(ESP)、 Internet密钥交换协议(IKE)**

这三类分别提供以下功能：

- **认证头（AH（Authentication Header）)**：**提供数据完整性和数据源认证**，但**不提供数据保密**服务，使用的算法是**哈希**如MD5、SHA

- **封装安全负荷(ESP（Encapsulating Security Payload）)**：**提供数据加密功能**，加密算法有DES、3DES、AES等

- **Internet密钥交换协议(IKE（Internet Key Exchange）)**：用于**生成和分发秘钥**，比如在上述两类功能ESP和AH中使用的密钥

![image-20230307210811973](https://img.yatjay.top/md/image-20230307210811973.png)

## IPSec的两种封装模式(知道AH在哪里插即可)

### 传输模式——原IP头中间插

传输模式：在**原来的IP报文中**插入认证头，该认证头可以是AH、ESP、IKE，原来的IP报文格式不变

![image-20230307210952294](https://img.yatjay.top/md/image-20230307210952294.png)

### 隧道模式——原IP头前面插

隧道模式：**原来的IP报文头不变**，在**原来的IP头前面插入**AH/ESP/IKE，再在其前面添加一个新的IP头

![image-20230307210952294](https://img.yatjay.top/md/image-20230307210952294.png)

只需记住插在哪里：

- 传输模式中间插，不添加新的IP头
- 隧道模式前面插，再添加一个新的IP头

### 例题

![image-20230307211109893](https://img.yatjay.top/md/image-20230307211109893.png)

D选项解析：IPSec是网络层的功能

各层出于安全考虑而使用的VPN协议如下：

- 二层VPN协议：L2TP、PPTP
- 三层VPN协议：**IPSec**、GRE（GRE不考）
- 四层VPN协议：SSL

[SSL/TSL到底是属于哪一层的协议？](https://www.jianshu.com/p/5ee027c51af0)

## 本节小结

本节只需掌握以下2个知识点：

- IPSec的3类功能：认证头(AH)、封装安全负荷(ESP)、 Internet密钥交换协议(IKE)
- IPSec的2种封装模式：传输模式、隧道模式
