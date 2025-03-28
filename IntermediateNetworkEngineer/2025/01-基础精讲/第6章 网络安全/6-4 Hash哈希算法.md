## 6-4 Hash哈希算法

### 哈希算法Hash

Hsah函数，又称为杂凑函数、散列函数，它能够将<font color="red">任意长度的信息</font>转换成<font color="red">固定长度的哈希值</font>（数字摘要），并且任意不同消息或文件所生成的哈希值是不一样的。

h表示hash函数，则h满足下列条件：

1. h的输入可以是任意长度的消息或文件M。
2. h的输出的长度是固定的。
3. 给定h和M，计算h（M）是容易的。
4. 给定h的描述，找两个不同的消息M1和M2，使得h（M1）=h_(M2）是计算上不可行的。

哈希函数特性：<font color="red">不可逆性（单向）、无碰撞性、雪崩效应</font>

### 哈希分类

常见的Hash算法有：

1. <font color="red">MD5算法</font>：以**512位数据块**为单位来处理输入，产生<font color="red">128位</font>的信息摘要。常用于文件校验。
2. <font color="red">SHA算法</font>：以**512位数据块**为单位来处理输入，产生<font color="red">160位</font>的哈希值，比MD5更安全。
3. <font color="red">SM3国产算法</font>：消息分组长度为**512比特**，输出<font color="red"> 256位</font> 摘要。

### HASH应用

#### 文件完整性校验

![image-20250308202058009](https://img.yatjay.top/md/20250308202058054.png)

#### 账号密码存储

1. 明文存储，无安全防护

![image-20250308202141130](https://img.yatjay.top/md/20250308202141162.png)

2. 哈希存储(Rainbow Table Attack，彩虹表攻击)

![image-20250308202151479](https://img.yatjay.top/md/20250308202151502.png)

3. (<font color="red">盐+哈希</font>）存储(彩虹表攻击失效)：盐可以是用户注册时间或者生成的随机数

![image-20250308202203578](https://img.yatjay.top/md/20250308202203613.png)

#### 用户身份认证

典型的应用是在PPPoE中使用的用户认证：

- 增加一个随机数R做哈希HMAC = Hash(密码+R)
- 需要双方预先知道这个R
- MAC：消除中间人攻击源认证(避免密码直接传输)+完整性校验

![image-20250308202231555](https://img.yatjay.top/md/20250308202231592.png)

客户端需要到PPPoE的服务端进行验证，首先客户端发起认证，服务端查询数据库，发现数据库中有张三用户，返回随机数R，客户端将张三的密码123结合随机数R进行哈希后返回给服务端，服务端同样将张三的密码123结合随机数R进行哈希后比对。如果相等，说明用户张三知道正确密码；如果不相等，说明客户端不知道用户密码。如此能够消除中间人攻击源认证+完整性校验

### 例题

#### 例题1

![image-20250308202314496](https://img.yatjay.top/md/20250308202314530.png)

解析：PGP不是一种完全的非对称加密体系，它是个混合加密算法，它是由<font color="red">对称加密算法（IDEA）、非对称加密算法（RSA）、单向散列算法（MD5）</font>组成，其中MD5验证报文完整性。

#### 例题2

![image-20250308202328199](https://img.yatjay.top/md/20250308202328230.png)

解析：记忆常见哈希算法输出

#### 例题3

![image-20250308202337016](https://img.yatjay.top/md/20250308202337041.png)

#### 例题4

![image-20250308202344550](https://img.yatjay.top/md/20250308202344575.png)
