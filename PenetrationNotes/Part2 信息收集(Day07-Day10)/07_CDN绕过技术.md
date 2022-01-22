前言：CDN的全称是Content Delivery Network，即内容分发网络。CDN是构建在现有网络基础之上的智能虚拟网络，依靠部署在各地的边缘服务器，通过中心平台的负载均衡、内容分发、调度等功能模块，**使用户就近获取所需内容，降低网络拥塞，提高用户访问响应速度和命中率**。但在安全测试过程中，若目标存在CDN服务，将会影响到后续的安全测试过程。

![](https://gitee.com/YatJay/image/raw/master/202201201557279.png)



# 涉及知识

## 如何判断目标是否存在CDN服务？

利用多节点技术进行请求返回判断，使用[超级ping工具](https://ping.chinaz.com/)进行测试

- 无CDN情况

通过检测，发现只有一个IP返回，说明没有CDN

![](https://gitee.com/YatJay/image/raw/master/202201201619324.png)

- 有CDN情况

通过检测，发现有多个IP返回，说明存在CDN

![](https://gitee.com/YatJay/image/raw/master/img/202201222032999.png)


## CDN对于安全测试有哪些影响





## 目前常见的CDN绕过技术有哪些

### 子域名查询绕过

#### 子域名3中不同情况

1. 子域名和顶级域名在同一个IP，如：

   xiaodi8.com 	192.168.1.100

   www.xiaodi8.com	192.168.1.100

2. 子域名IP和顶级域名IP在同一网段，如：

   xiaodi8.com 	192.168.1.100

   bbs.xiaodi8.com	192.168.1.1~92.168.1.255  之间

3. 子域名IP和顶级域名IPO不再同一网段，分属不同IP

   xiaodi8.com 	192.168.1.100

   bbs.xiaodi8.com	36.56.89.54

对于以上子域名的三种情况，**最后一种情况不再CDN绕过的考虑范围之内，只考虑前两种情况。**

#### 为什么要进行子域名查询？

因为搭建CDN要花钱，所以管理员会对主站、访问量比较大的网站做CDN服务，但是不会对子站做CDN，这时候就可以通过查找子域名对应IP来查找网站的真实IP。一般情况下，子站跟主站在同一个C段内。

#### 如何获取子域名——子域名采集
可以使用一些子域名查询工具来获取某网站的子域名如[在线子域名查询](http://tools.bugscaner.com/subdomain/)

子域名挖掘机工具

#### 子域名查询绕过的小技巧

通常网站管理员将两个地址解析至同一ip，如下图所示：
![](https://gitee.com/YatJay/image/raw/master/img/202201221635842.png)

访问 xiaodi8.com 实际上解析到的是图中第一行记录值的IP

访问www.xioadi8.com 实际上解析到的是图中第二行记录值的IP

一般网站管理员都是这样设置，如果只设置www的解析记录，那么访问xiaodi8.com就没有响应，只有访问www.xiaodi8.com才可以访问到设置好的解析记录。如果只设置*的解析记录，那么是否加www都会解析到设置好的解析记录。

而搭建CDN服务时候，可能存在网站管理员只将其中一个设置解析至CDN服务器，即只给www.xxx.com解析时解析到CDN服务器，而*.xxx.com在解析时仍然解析到网站真实IP，也就是说两种访问方式访问的ip并不相同。

**因此可以利用多节点技术对加上www和不加www的域名先后进行访问，查看结果可以看到不加www时是否只有一个IP地址响应，从而确定真实IP。**

与此相似，亦可以通过访问 www.xxx.com 和 m.xxx.com(手机访问界面) 的IP结果对比得到网站的真实IP，其原理与上述的管理员的解析记录设置相似。

### 邮件服务查询

#### 为什么可以通过邮件发服务得到主站IP？

很多公司内部都会有一个邮箱服务器，这种邮箱服务器大部分是不会做CDN的。因为邮箱服务器一般都是公司内部人去访问，所以大部分不做CDN。因此，我们就可以根据对方邮件服务器给我们发送的邮件，来判断对方的IP地址。

另外，作为客户端访问主站时，有可能访问到的是CDN服务器，而邮件服务器多数时候是公司内部人去访问，作为客户端我们只接收邮件，不会涉及主动访问邮件服务器，所以可以根据邮件服务器发来的邮件判定发送者的IP地址，进而推测主站的IP地址。

### 国外地址请求

有些网站为了节省成本，不会把CDN部署在国外。假设现在你自己的网络公司有一个网站，但你的客户群体主要是在国内，因为国外用户不多，所以就不值得在国外搭建CDN，因此这样从国外访问国内的网站就很可能直接访问的就是主站的真实IP地址。

这里需要注意的是[超级ping工具](https://ping.chinaz.com/)提供的国外节点比较少，而且往往是一些非冷门的国家，因此在进行国外地址请求访问时要避免使用。这里推荐[App Synthetic Monitor](https://asm.ca.com/en/ping.php)  (当前已经无法免费使用)。除此之外，还可以使用VPN里的冷门国家节点来进行ping测试。

### 遗留文件搜索

#### 什么是遗留文件？

- 一些站点在搭建之初，会用到一些文件测试站点，例如“phpinfo()”文件，此类文件里就有可能包含了真实的IP地址。可以利用Google搜索引擎搜索关键字“site:xxx.com inurl:phpinfo.php”，搜索站点是否有遗留文件

- ### 扫描全网

- 一旦以上方法都不行，究极办法就是，借助工具用世界各地的IP访问该网站，之后搜集整理响应的IP地址，而后分析筛选这些IP地址。由于1.不可能每一个IP都有CDN；2. 搜集整理的这些IP地址中一定有主站的真实IP；3. 

### 黑暗引擎搜索特定文件

#### 搜索引擎

- [谷歌google](www.google.com)

- [撒旦shodan](https://www.shodan.io/)

- [钟馗之眼Zoomeye](https://www.zoomeye.org/)

- [fofa网络空间搜索引擎](https://fofa.so/)

#### 特定文件

这里的特定文件，指的是站点的icon文件，也就是网站的图标，一般查看网页源代码可以找到，格式大致如下http://www.xx.com/favicon.ico  。

在shodan搜索网站icon图标的语法为：http.favicon.hash:hash值，hash是一个未知的随机数，我们可以通过shodan语法来查看一个已经被shodan收录的网站的hash值，来进一步获取到所有带有某icon的网站。



### DNS历史记录

站点在使用CDN服务之前，它的真实IP地址可能被DNS服务器所记录到，此时我们就可以通过DNS历史记录找到目标真实IP。

DNS历史记录查询网站：

[微步在线情报社区](https://x.threatbook.cnDNSdb)

[全球DNS搜索引擎](https://dnsdb.io/zh-cn/)



### 以量打量

“以量打量”就是常说的DDOS攻击或者说是流量耗尽攻击，在网上开CDN的时候，都会分地区流量，就比如这个节点有100M流量，当这流量用完后，用户再访问就会访问网站真实的IP地址，从而能够获得站点的真实IP。

### 获取疑似真实IP地址后绑定DNS解析地址

在获取疑似真实IP地址之后更改本地HOSTS解析指向文件，看能否正常访问。

**不过如果绑定某个CDN服务器地址，仍然能够成功访问。参考案例3：墨者安全最后的IP确定**

### 几个扫全网的CDN绕过工具

#### fuckcdn

##### 项目地址

https://github.com/Tai7sy/fuckcdn

##### 使用注意

优先选择fuckcdn

#### w8fuckcdn

##### 项目地址

https://github.com/boy-hack/w8fuckcdn 

##### 使用注意

==///////////////////////////////////////////////////////////////////////////////////////////////////////////////////==

#### zmap(老牌工具)

##### 使用注意

- 老牌工具
- 网络配置要求较高，500M带宽起步

# 涉及案例

## 常用方法
1. 利用子域名请求获取真实IP
2. 利用国外地址请求获取真实IP
3. 利用第三方接口查询获取真实IP
4. 利用右键服务器接口获取真实IP
5. 利用黑暗搜索引擎搜索特定文件获取真实IP

## 案例1：学而思
### 目标网站

https://www.xueersi.com/

### 绕过技术：子域名查询小技巧绕过

该网站正是网站管理员只将其中一个设置解析至CDN服务器，即只给www.xueersi.com解析时解析到CDN服务器，而xueersi.com在解析时仍然解析到网站真实IP，也就是说两种访问方式访问的ip并不相同，其中后者是真实IP。

### 实验过程

超级ping www.xueersi.com 结果，可见配置了CDN服务

![](https://gitee.com/YatJay/image/raw/master/img/202201221657737.png)

超级ping xueersi.com 结果，可见只有2个IP响应，没有配置CDN

![](https://gitee.com/YatJay/image/raw/master/img/202201221658695.png)

另外可以使用第三方网站查询真实IP(不一定准确)进行验证，如  [get-site-ip.com](https://get-site-ip.com/)

![](https://gitee.com/YatJay/image/raw/master/img/202201221701610.png)

可见使用小技巧判断网站真实IP的方法是比较有效的。



## 案例2：教视网

### 目标网站

www.sp910.com

### 目标网站

www.sp910.com

### 方法1 子域名查询小技巧绕过

此案例欲使用通过访问 www.sp910.com 和 m.sp910.com(手机访问界面) 的IP结果对比得到网站的真实IP的方法来绕过CDN。但当前此方法已经不可用。

不过，不加www直接使用根域名进行多节点请求绕过CDN查到真实IP：

超级ping www.sp910.com 结果，可见配置了CDN服务

![](https://gitee.com/YatJay/image/raw/master/img/202201221749606.png)

超级ping sp910.com 结果，可见只有1个IP响应，没有配置CDN

![](https://gitee.com/YatJay/image/raw/master/img/202201221750321.png)

### 方法2 第三方接口查询绕过

即利用第三方网站进行查询

![](https://gitee.com/YatJay/image/raw/master/img/202201222031481.png)

### 方法3 DNS历史记录

利用DNS历史记录查询网站：

[情报社区](https://x.threatbook.cnDNSdb)

![](https://gitee.com/YatJay/image/raw/master/img/202201221717484.png)

[全球DNS搜索引擎](https://dnsdb.io/zh-cn/)

### 方法四 国外地址请求获取真实IP

略



## 案例3：墨者安全

### 目标网站

www.mozhe.cn

### 绕过方法：邮件源码测试对比第三方查询(地区分析)

#### 1. 验证是否存在CDN服务

是否存在CDN 超级ping www.mozhe.cn 如下图所示说明存在CDN

![](https://gitee.com/YatJay/image/raw/master/img/202201222036051.png)

能否通过不加www的多节点访问绕过：超级ping mozhe.cn 如下图所示说明不能通过不加www的多节点访问来绕过

![](https://gitee.com/YatJay/image/raw/master/img/202201222039849.png)



#### 2. 设法获得从目标网站发来的邮件

使用邮箱注册mozhe或者在账户页面绑定邮箱( 绑定邮箱赠送墨币)，随后收到mozhe发来的验证邮件

![](https://gitee.com/YatJay/image/raw/master/img/202201222053419.png)

点击显示邮件原文可以查看邮件源代码，注意源代码里的内容，见下注释：

```http
Received: from qq.com (smtpbg410.qq.com [113.96.223.72])  //这里的IP时腾讯邮箱的中转服务器IP
	by newxmmxsza98.qq.com (NewMX) with SMTP id CA617AA1
	for <yatjay@foxmail.com>; Sat, 22 Jan 2022 20:50:38 +0800
X-QQ-mid: xmmxsza98t1642855838t1axeewvd
X-QQ-XMAILINFO: MkhAA4qIuU0psnuJmWJuE7lbbd98QAoWIQJ8NdHmY7qcRBDo2dCE2oMR/7qoBO
	 1Ss/Cg1AHMstdigcWvX1pr7euDqdJ9+UVIPHAYvqwhvFdjFs2NX5kLu4aEhK4jlGLDIcaLz162hL
	 DRk6FnUd69nzQLPkqhQTMMwqqTSebQN8CJYT77kjeo7rdWPfW5izGYPdsaYYLeQtozBplS6z6kzV
	 dn77HNhvV5gN1lPvrnmJwdI42iaKd2kfj+lcvAP0VjFCB331RCnf7jP+u8Eef37GK1q+gTRB6xqo
	 Yi/X8xRPCmM6jr2eGgfWHH+htKW2gB94+5C7R617OKo1vW615wmlJXKfD+gD6PPYrilSuKHBl+Xg
	 /G2JC9D1RxyyAO4QfJQngd68kURMNxKdo0tpkLOnyQP0OE2fp9Li5KWuZIkJNB2h0CNDNEFmGjkh
	 Q+x/RGod68wnNKMDPz5X84WUl6JfUTP6xl64uw7yplAeKjbqksdBFyJ8xOJ8tScHvN+XndcJZI2d
	 BPXjjLCMuR4i37lgxG+v7ZeQz1r01DGYrZlbMZKAjZO7x2o5W/Vxz2jkeYat+pz63FhDgvILMh7s
	 BVwdofuKQvCoeCJEI08Tfl2/INB5C1T9URVPk06VqhhJqYdjca1GEQPIwLjyjLoQqDdBoIEfBC34
	 1SVZntZAdEqx5RqtX2kRSJ2idQsbq7eQdN5YU1jm8p8uY2FSCWlzbBTh6RQEepT389aW8trSWgdT
	 mjUDOTrA2G3bxidinlGy0S1t8H6sgRvNlB9PH6htRO8TrxJPP1C8lvrNqoHY5yHEiU1sap8msmxK
	 7UkH+un0sCqctdrOirHnFYs2S+Lxbd3MllRnbg0LXRGfW7idn9Eef8iP8v6hDBCAyrMhEQBMBU/F
	 YOm0jJD+hdoJRL7IZgww70BykcWdg0vyGqrdZNHYxJw8CkIldummrBEYBtFKQ3L7NvxNtF5RcFGN
	 cIkaIeOprQVLCI08WWS3eua0csYARZVLUcj2vnpXT3PxSny2jDf8UL3HytegOA
X-BQ-mid: bizesmtp44t1642855836txeiwih0
Received: from www.mozhe.cn (unknown [219.153.49.169])  //!!!注意这里的IP才是mozhe的邮箱服务器IP
	by bizesmtp.qq.com (ESMTP) with SMTP id 0
	for <yatjay@foxmail.com>; Sat, 22 Jan 2022 20:50:35 +0800 (CST)
X-BQ-SSF: 00000000000000903000B00A0000000
X-BQ-FEAT: ILHsT53NKPjVwJO9W6jMeXR7j1M9AykPOdrNURqESvbDfYCQFs0UIE1Ravx17
	AY8cTVZPfsm+DQDGfm9NqeojeIbuMzS5o7uGdJMX5QQKdDouFlaaeNPLrDlwvTwFfLEbjMZ
	LSH7pktpaYbzwNYLK9R7TghY+aFkkomnSiAoUj9ew418TbEnAjkLmEjDEzBXPdNOnTzCv7C
	gNeyQi5KysBj0YjAmx9vfEgDuavWyZwej7noOk/jKZAYUQ7vHRLaLcqpy7fEjOGZfJfGi0y
	mHu9i5HOUnlDCuykFE9N+rN592ZZNjLb8bhOeKv1nVb4dmKpQWmqvFmf7mRKIJQpeCGMlry
	VqIfstX001CJcQ8kPHB4NRF0Aoi9nTkEtL+VGdE
Message-ID: <4b5ea2e8d76cf10b82f2a087b651a411@www.mozhe.cn>+4615B1BFB50F5811
Date: Sat, 22 Jan 2022 20:50:35 +0800
Subject: =?utf-8?Q?=E8=B4=A6=E5=8F=B7=E7=BB=91=E5=AE=9A?=
 =?utf-8?Q?=E9=82=AE=E7=AE=B1?=
From: =?utf-8?Q?=E5=A2=A8=E8=80=85=E5=AD=A6=E9=99=A2?=
 <noreply@mail.mozhe.cn>
To: YatJay <yatjay@foxmail.com>
MIME-Version: 1.0
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: quoted-printable
X-BQ-SENDSIZE: 520
Feedback-ID: bizesmtp:mail.mozhe.cn:qybgweb:qybgweb17

Hi YatJay,
<br>
<br>
=E6=88=91=E4=BB=AC=E7=9A=84=E7=B3=BB=E7=BB=9F=
=E6=94=B6=E5=88=B0=E4=B8=80=E4=B8=AA=E8=AF=B7=E6=B1=82=EF=BC=8C=E8=AF=B4=
=E6=82=A8=E5=B8=8C=E6=9C=9B=E9=80=9A=E8=BF=87=E7=94=B5=E5=AD=90=E9=82=AE=
=E4=BB=B6=E7=BB=91=E5=AE=9A=E4=BD=A0=E5=9C=A8 [=E5=A2=A8=E8=80=85=E5=AD=
=A6=E9=99=A2_=E4=B8=93=E6=B3=A8=E4=BA=8E=E7=BD=91=E7=BB=9C=E5=AE=89=
=E5=85=A8=E4=BA=BA=E6=89=8D=E5=9F=B9=E5=85=BB] =E7=9A=84=E8=B4=A6=E5=8F=
=B7=E3=80=82=E4=BD=A0=E5=8F=AF=E4=BB=A5=E9=AA=8C=E8=AF=81=E7=A0=81=E6=98=
=AF=EF=BC=9A
<br>
<br>
852406
<br>
<br>
=E5=A6=82=E6=9E=9C=
=E8=BF=99=E4=B8=AA=E8=AF=B7=E6=B1=82=E4=B8=8D=E6=98=AF=E7=94=B1=E6=82=A8=
=E5=8F=91=E8=B5=B7=E7=9A=84=EF=BC=8C=E9=82=A3=E6=B2=A1=E9=97=AE=E9=A2=98=
=EF=BC=8C=E8=AF=B7=E6=82=A8=E5=8F=AF=E4=BB=A5=E6=94=BE=E5=BF=83=E5=9C=B0=
=E5=BF=BD=E7=95=A5=E8=AF=A5=E9=82=AE=E4=BB=B6=E3=80=82
<br>
<br>
=
=E5=A2=A8=E8=80=85=E5=AD=A6=E9=99=A2_=E4=B8=93=E6=B3=A8=E4=BA=8E=E7=BD=
=91=E7=BB=9C=E5=AE=89=E5=85=A8=E4=BA=BA=E6=89=8D=E5=9F=B9=E5=85=
```

从邮箱源码中可以看到，mozhe的邮箱服务器IP是：219.153.49.169，记录备用。

#### 3. 第三方查询网站获取真实IP作为参考

使用第三方查询网站查到mozhe的真实IP不固定即多次点击查询得到的结果不同

![](https://gitee.com/YatJay/image/raw/master/img/202201222048172.png)

从第三方网站查询得到的几个真实IP地址分别是：14.204.139.142   113.200.17.157    58.243.200.63    59.83.204.154 ……这个查询结果基本不能用作CDN绕过。

#### 4. 查询当前获取到的几个IP的物理地址

![](https://gitee.com/YatJay/image/raw/master/img/202201222102670.png)



![](https://gitee.com/YatJay/image/raw/master/img/202201222103293.png)



![](https://gitee.com/YatJay/image/raw/master/img/202201222104634.png)



#### 5. 确定邮件源码获得的IP和第三方查到的IP哪一个才是真实IP

##### 方法1：社工方法查备案地址

![](https://gitee.com/YatJay/image/raw/master/img/202201222107425.png)

可见网站公司在重庆市，因此基本可以确定邮件查到的归属地为重庆IP地址就是mozhe的真实IP。

##### ==方法2：修改本地HOSTS文件==

为mozhe修改HOSTS文件中的解析地址，先修改成第三方查询到的网站IP，且ping后可以看到HOSTS的修改已经生效。

![](https://gitee.com/YatJay/image/raw/master/img/202201222116699.png)

使用浏览器访问mozhe

![](https://gitee.com/YatJay/image/raw/master/img/202201222123546.png)

访问成功说明即使是访问CDN节点的IP，仍然能成功访问到网站内容。

“CDN有可能给多个网站做流量缓存，如果是CDN的IP就打不开mozhe网页”这句话是错误的！



为mozhe修改HOSTS文件中的解析地址，再修改成第三方查询到的网站IP，但是该IP ping不通。

![](https://gitee.com/YatJay/image/raw/master/img/202201222131795.png)

但是访问mozhe网站仍然能够访问到内容

![](https://gitee.com/YatJay/image/raw/master/img/202201222133971.png)

==这只能够说明此IP有可能是真实IP，但不能确定一定就是==  



## 案例4：某在线赌博网站

### 目标网站

https://7780802.com/  //无ico文件

### 绕过方法：黑暗引擎shodan搜指定的hash文件

#### 1. 使用如下的Python脚本换算目标地址下的该文件的hash值

需要Python2环境，别搞错了执行环境

安装mmh3失败记得先安装下这个Microsoft visual C++ 14.0https://pan.baidu.com/s/12TcFkZ6KFLhofCT-osJOSg  提取码: wkgv

```python
import mmh3
import requests
response = requests.get('http://www.xx.com/favicon.ico')  ## 请求该网站的favicon.ico文件(该文件一般是网站独有、唯一的)，执行时修改此地址
favicon = response.content.encode('base64')
hash = mmh3.hash(favicon)
print('http.favicon.hash:' + str(hash))  ## 拿到文件hash值就去黑暗搜索引擎搜索去匹配
```



#### 2. 通过得到的hash值再黑暗引擎中搜索是否有相同hash值，从而判断该文件所在的IP地址

[黑暗引擎搜索参考公众号文章](https://mp.weixin.qq.com/s?biz=MzA5MzQ3MDE1NQ==&mid=2653939118&idx=1&sn=945b81344d9c89431a8c413ff633fc3a&chksm=8b86290abcf1a01cdc00711339884602b5bb474111d3aff2d465182702715087e22c852c158f&token=268417143&lang=zhCN#rd)

在shodan中使用hash值得搜索语法为

```http
http.favicon.hash:hash值
```

搜索结果如下：



# 涉及资源

- https://www.shodan.io

- https://x.threatbook.cn

- http://ping.chinaz.com

- https://www.get-site-ip.com/       第三方网站查询真实IP(不一定准确)

- -http://tools.bugscaner.com/subdomain     在线子域名查询

- https://asm.ca.com/en/ping.php
- https://github.com/Tai7sy/fuckcdn
- https://github.com/boy-hack/w8fuckcdn     w8fuckcdn
- https://mp.weixin.qq.com/s?biz=MzA5MzQ3MDE1NQ==&mid=2653939118&idx=1&sn=945b81344d9c89431a8c413ff633fc3a&chksm=8b86290abcf1a01cdc00711339884602b5bb474111d3aff2d465182702715087e22c852c158f&token=268417143&lang=zhCN#rd   黑暗引擎搜索参考公众号文章

