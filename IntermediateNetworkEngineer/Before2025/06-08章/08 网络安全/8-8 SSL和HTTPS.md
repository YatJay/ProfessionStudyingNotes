# 8-8 SSL与HTTPS

## 传输层的安全协议——SSL及HTTPS

### SSL 安全套接层

安全套接层(Secure Socket Layer，SSL)是Netscape(网景)于1994年开发的**传输层(第4层)安全协议**，面向用于实现Web安全通信。

1999年，IETF基于SSL3.0版本，制定了传输层安全标准TLS (Transport Layer Security，安全传输层协议)

SSL/TLS在Web安全通信中被称为**HTTPS**，因此**SSL和TLS可以看成一个东西**。

记忆以下两个点↓

1. HTTPS基于TCP端口443

2. HTTP + SSL就成为了HTTPS

   ![image-20230307211829771](https://img.yatjay.top/md/image-20230307211829771.png)

#### 例题

![image-20230307211925064](https://img.yatjay.top/md/image-20230307211925064.png)

![image-20230307211947487](https://img.yatjay.top/md/image-20230307211947487.png)

## 应用层的安全协议——S-HTTP和PGP

### S-HTTP

对于一个不安全的HTTP，

- 打补丁的思路：HTTP头引入SSL，就成为了HTTPS；

- 重新开发的思路：HTTP重新开发协议，就成为S-HTTP

S-HTTP安全的超文本传输协议(Security HTTP)

S-HTTP**语法与HTTP一样，而报文头有所区别，进行了加密**

##### 例题

![image-20230307212415063](https://img.yatjay.top/md/image-20230307212415063.png)

### PGP（考试频率较高）

#### PGP简介

PGP (Pretty Good Privacy)是一个完整的**电子邮件**安全软件包

PGP提供如下2种服务（功能）：

- **数据加密**
- **数字签名**

PGP使用的认证、加密和完整性验证算法：

- 采用**RSA**公钥证书进行**身份认证**；

- 使用**IDEA**进行**数据加密**；
- 使用**MD5**进行数据**完整性验证**。

#### PGP应用广泛的原因

- 支持多平台(Windows，Linux，MacOS)上免费使用，得到许多厂商支持

- 基于比较安全的算法(RSA，IDEA，MD5)

- 既可以加密文件和电子邮件，也可以用于个人通信，应用集成PGP，现在已经集成到很多软件当中去了

#### 例题

![image-20230307212821324](https://img.yatjay.top/md/image-20230307212821324.png)

![image-20230307212835358](https://img.yatjay.top/md/image-20230307212835358.png)

注意：目前实际上PGP是支持邮件压缩的

![image-20230307212859481](https://img.yatjay.top/md/image-20230307212859481.png)

### S/MIME，SET和Kerberos认证（了解）

**S/MIME** ( Security/Multipurpose Internet Mail Extensions )提供**电子邮件**安全服务，与PGP功能类似，且相比PGP，S/MIME支持多媒体

**SET** ( Secure Electronic Transation )安全的电子交易，用于保障**电子商务**安全

**Kerberos**是**用于进行身份认证**的安全协议，支持AAA即**认证、授权和审计**。

![image-20230307220147663](https://img.yatjay.top/md/image-20230307220147663.png)

#### 例题

![image-20230307220217317](https://img.yatjay.top/md/image-20230307220217317.png)