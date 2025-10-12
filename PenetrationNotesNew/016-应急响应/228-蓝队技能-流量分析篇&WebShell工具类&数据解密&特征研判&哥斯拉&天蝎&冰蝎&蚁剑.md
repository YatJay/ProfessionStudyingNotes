# 蓝队技能-流量分析篇&WebShell工具类&数据解密&特征研判&哥斯拉&天蝎&冰蝎&蚁剑

![image-20251005171441542](https://img.yatjay.top/md/20251005171441589.png)

## 知识点

1、流量分析-WebShell工具

2、流量分析-蚁剑&天蝎&冰蝎&哥斯拉

3、流量分析-WebShell流量加密解密

---

学习流量分析的目的：

1、明白项目平台系统警告信息原理

2、学习流量分析的技术和思路

3、通过特征尝试性开发规则进行检测

## 演示案例

### 蓝队技能-流量分析-WebShell工具

WebShell工具类：菜刀，蚁剑，冰蝎，天蝎，哥斯拉

各个工具的流量特征集中体现在以下方面面：

1、请求头

2、请求体：尝试利用默认密钥等解密，成功还原算法明文即可认定对应工具

3、回显数据：尝试利用默认密钥等解密，成功还原算法明文即可认定对应工具

#### 菜刀

菜刀现在已经不常使用，因为该工具未开源，二次开发成本较高，冰蝎、哥斯拉现在使用最多也魔改最多

菜刀的流量特征见下面文章

https://mp.weixin.qq.com/s/SjUMoY6EafQzAb3RNWNkpg

#### 蚁剑

使用蚁剑，在默认配置下连接webshell，其流量特征如下：

##### Post方法提交

0、使用Post方法提交：大量数据的提交一般都适用于Post方法，Get方法只适用少量数据

![image-20251011214443660](https://img.yatjay.top/md/20251011214443733.png)

##### User-Agent字段特征

1、`User-Agent: antSword/v2.1`：请求头的User-Agent字段默认显示为此

![image-20251011214422692](https://img.yatjay.top/md/20251011214422766.png)

##### 编码器特征

2、编码器决定：

![image-20251011215323799](https://img.yatjay.top/md/20251011215323836.png)

- 默认的编码器为`@ini_set("display_errors", "0");@set_time_limit(0);`，当配置为此编码器时，数据包流量显示为：

![image-20251011214729042](https://img.yatjay.top/md/20251011214729091.png)

- 当自行修改编码器为`（default,rot13）`时，数据包流量显示为：

![image-20251011215355428](https://img.yatjay.top/md/20251011215355480.png)

##### 第3位开始Base64编码

3、Base64编码提交的数据（去掉前2位）：提交的数据(即\==号之后的参数值)去掉前2位之后可以进行Base64解码得到明文

![image-20251011214325351](https://img.yatjay.top/md/20251011214325402.png)

同时满足上述三个及以上特征时，可以确定是蚁剑连接。

当然，上述特征都是基于工具的默认设置，具体攻击时可以自行配置：右键单击——编辑数据——可以对编码格式、请求头等信息进行配置

![image-20251011214609255](https://img.yatjay.top/md/20251011214609304.png)

#### 冰蝎

使用冰蝎，在默认配置下连接webshell，其流量特征如下：

##### Post方法提交

基本所有工具都是使用Post方法提交数据

##### User-Agent头部字段

1、User-Agent: Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko

![image-20251011215934763](https://img.yatjay.top/md/20251011215934836.png)

##### Accept头部字段为固定值

HTTP头部中，Accept字段值为固定值，这是一个强特征

```bash
Accept: 
text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application
/signed-exchange;v=b3;q=0.9
```

![image-20251011220148661](https://img.yatjay.top/md/20251011220148731.png)

##### Accept-Language头部字段为固定值

HTTP头部中，Accept-Language字段值为固定值，这是一个强特征

```bash
Accept-Language: zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7
```

![image-20251011220221711](https://img.yatjay.top/md/20251011220221776.png)

##### 全加密

提交数据都是加密的，请求体中的数据全部加密，响应体中的数据全部解密

一般使用AES算法，需要秘钥、偏移量才可以解密。若需解密，一般要先知道其Webshell连接秘钥

冰蝎存在自带的默认秘钥且不设置偏移量，当连接秘钥未修改时，是一个固定值，可以用作密文数据的解密秘钥尝试解密，若解密成功，则说明是使用冰蝎连接

![image-20251011220458169](https://img.yatjay.top/md/20251011220458240.png)

##### Referer参数

部分3.0以后的子版本至4.0以后增加了referer参数，即数据包当中会有这个字段(蚁剑就没有这个字段)，而且末尾文件名随机大小写

![image-20251011221555131](https://img.yatjay.top/md/20251011221555193.png)

#### 天蝎

天蝎实际上是冰蝎的魔改开源版本

##### Post方法提交

基本所有工具都是使用Post方法提交数据

##### X-Forwarded-For头部字段值在变化

1、X-Forwarded-For头部字段值在跳变，跳变为不同的IP地址

![image-20251011221837951](https://img.yatjay.top/md/20251011221838008.png)

![image-20251011221908633](https://img.yatjay.top/md/20251011221908700.png)

##### User-Agent头部字段

User-Agent头部字段特征如下：

```bash
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36 Edg/89.0.774.57
```

![image-20251011221940431](https://img.yatjay.top/md/20251011221940499.png)

##### Content-Type头部字段

Content-Type头部字段特征如下：

```bash
Content-Type: application/octet-stream
```

![image-20251011222115028](https://img.yatjay.top/md/20251011222115099.png)

同时满足上述三个及以上特征时，可以确定是天蝎连接。

#### 哥斯拉

##### Post方法提交

基本所有工具都是使用Post方法提交数据

##### 请求体开头部分特征

（PHP_EVAL_XOR_BASE64）

0、eval(base64_decode(strrev(urldecode('

![image-20251011223045974](https://img.yatjay.top/md/20251011223046034.png)

##### 连接时的流量包数量(强特征)

强特征：

- 链接失败情况下，为2个流量包

![image-20251011222947275](https://img.yatjay.top/md/20251011222947333.png)

- 连接成功情况下，为3个流量包

![image-20251011222831194](https://img.yatjay.top/md/20251011222831265.png)

##### 第一个请求及其Cookie特征

第一个请求总会发送大量数据，这是配置信息，且请求包内无cookie，服务器响应包无内容，生成一个session，后续请求会带上此session到请求包中的cookie中

![image-20251011223316701](https://img.yatjay.top/md/20251011223316757.png)

##### cookie后面有个分号(强特征)

强特征：生成的cookie后面有个分号

![image-20251011223346875](https://img.yatjay.top/md/20251011223346937.png)

##### Accept头部字段为固定值

Accept字段值为固定值

```bash
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
```

##### Accept-Language头部字段为固定值

```bash
Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2
```

同时满足上述三个及以上特征时，可以确定是哥斯拉连接。

#### 工具数据包解密

对于请求体和响应体中的密文，可以尝试利用默认密钥等解密，若成功还原算法明文即可认定对应工具

默认秘钥是通过反编译该工具程序拿到的，而在之前提到过的[蓝队工具箱](https://github.com/abc123info/BlueTeamTools)中存在针对天蝎，冰蝎，哥斯拉这3款工具的默认解密密钥，可以辅助解密。(蚁剑是去掉前2位的Base64解密)

##### BlueTeamTools解密操作

工具运行

![image-20251011224314196](https://img.yatjay.top/md/20251011224314253.png)

点击解密Webshell流量选项卡，可以看到工具已经提前配置好默认连接密码和默认加解密秘钥(反编译得到的)

![image-20251011224420345](https://img.yatjay.top/md/20251011224420400.png)

复制流量包中的请求体，粘贴至“待解密内容”处，据实际配置是何种编程语言的程序

![image-20251011224627122](https://img.yatjay.top/md/20251011224627174.png)

测试平台是php写的，按上面选择后点击解密

![image-20251011224742762](https://img.yatjay.top/md/20251011224742818.png)

返回包解密同理

![image-20251011224926970](https://img.yatjay.top/md/20251011224927021.png)

##### BlueTeamTools的缺陷

知道密钥（工具内置的默认密钥，魔改二开发生的算法变化或密钥修改，则失效）

项目地址：https://github.com/abc123info/BlueTeamTools

#### WireShark使用

Wireshark过滤语法

##### IP地址

```bash
ip.addr==IP   //针对某个IP过滤
ip.src==IP  // 源IP过滤
ip.dst==IP   //目的IP过滤过滤目的ip    
```



##### 端口

```
tcp.port==端口号    // 显示主机端口的数据包
tcp.srcport==端口号    // 显示源主机端口的数据包
tcp.dstport==端口号     //显示目的主机端口的数据包
```

##### 协议：

tcp、udp、http、arp、icmp、smtp、pop、telnet、ssh、rdp等等

小写直接输入，回车

##### GET/POST过滤

注意大写

```bash
http.request.method=="GET"
http.request.method=="POST"
```

##### 逻辑运算符：and、or、|| 、&等等

```bash
ip.src==IP and http     //过滤源地址并且是http协议的    
```

##### 特定字符过滤

```bash
http contains "Content-Type: "
http.request.uri=="/pikachu/vul/unsafeupload/uploads/1.php" 
```

#### 真实案例：蓝队面试题型

##### 冰蝎数据包

###### 题目

题目提示：在调查清楚攻击者的攻击路径后你暗松了一口气，但是攻击者仍控制着服务器，眼下当务之急是继续深入调查攻击者对服务器进行了什么操作，同时调查清楚攻击者的身份，请你分析攻击者与WebShell通讯的流量获取攻击者获取的相关信息，目前可以得知的是攻击者使用了冰蝎进行WebShell连接。Tip：沿着前序题目的进度分析会更符合逻辑，或许有助于解题FLAG格式：flag攻击者获取到的服务器用户名_服务器内网IP地址}例如flag[web_10.0.0.3}

题目线索：pcap数据包

考察目的：熟悉常用Webshell工具流量特征、熟悉Wireshark使用

###### 思路

已知攻击者使用了冰蝎连接Webshell，尝试找到计算机名、计算机内网IP地址，可以确定

- http协议
- POST方法提交
- 筛选数据包

###### 分析过程

1、找到流量数据包：定位POST方法提交的流量包

```shell
http.request.method=="POST"
```

![image-20251011230404587](https://img.yatjay.top/md/20251011230404722.png)

发现1.php文件存在大量访问记录，点击第一个1.php相关流量并追踪流，可以看到提交参数值，这是冰蝎的加密流量

![image-20251011230513357](https://img.yatjay.top/md/20251011230513446.png)

2、请求回显流量解密

加密算法：AES-BASE64

将数据使用蓝队工具箱尝试解密——解密失败，密码错误

![image-20251011230634556](https://img.yatjay.top/md/20251011230634621.png)

应该是蓝队工具箱问题，因此我们测试直接AES解密

- 默认密钥：e45e329feb5d925b(蓝队工具箱复制出来的)
- 在线AES解密工具：http://tools.bugscaner.com/cryptoaes/

其中秘钥是蓝队工具箱复制出来的——解密成功

![image-20251011230856260](https://img.yatjay.top/md/20251011230856342.png)

显然解密结果中使用了Base64加密，我们再进行一次Base64解密操作，得到明文

![image-20251011231041226](https://img.yatjay.top/md/20251011231041276.png)

同理，对返回数据也使用上述方法解密

![image-20251011231228952](https://img.yatjay.top/md/20251011231229008.png)

![image-20251011231245027](https://img.yatjay.top/md/20251011231245067.png)

3、定位存在计算机名和IP地址的数据包

冰蝎连接有一个特征，就是连接上初始化时，会在冰蝎界面显示目标主机的信息如IP地址和计算机名，而这些信息实际上是通过Webshell执行`phpinfo()`函数得到目标主机的phpinfo页面而得到的。因此，POST方法提交请求返回的第一个响应包应该包含phpinfo信息，当中就有我们需要的计算机名和IP地址信息。

将第一个(或靠前的且返回长度较大的)响应包的响应体中数据使用上述方法进行解密和解码，很容易得到phpInfo页

解密操作

![image-20251011232746487](https://img.yatjay.top/md/20251011232746563.png)

可见这是一个JSON格式文本，将msg字段的值复制出来进行Base64解码

![image-20251011232856779](https://img.yatjay.top/md/20251011232856823.png)

又得到一个JSON格式文本，将basicinfo字段值再复制出来进行Base64解码，得到一个HTML页面，这个页面正是phpinfo页，当中就有计算机名和IP地址信息

![image-20251011233021625](https://img.yatjay.top/md/20251011233021683.png)

将明文结果保存到一个.html文档中，用浏览器打开

![image-20251011233140258](https://img.yatjay.top/md/20251011233140332.png)

寻找IP地址：IP为172.17.0.2或192.168.65.1

![image-20251011233227104](https://img.yatjay.top/md/20251011233227153.png)

寻找主机名：webmaster

![image-20251011233300065](https://img.yatjay.top/md/20251011233300110.png)

##### 蚁剑数据包

###### 题目

Flag格式：flagWebShell密码\_服务器JAVA版本\_攻击者删除的文件内容}例如flag{cmd_8u231_nothinginhere}

###### 分析过程

1、找到流量数据包

```shell
http.request.method=="POST"
```

![image-20251011233752711](https://img.yatjay.top/md/20251011233752837.png)

可以看到，在进行文件上传操作后开始连接Webshell。追踪上传的数据流如下

![image-20251011233854102](https://img.yatjay.top/md/20251011233854188.png)

这就是前面章节讲到过的Tomcat后台弱口令文件上传导致Webshell，上传文件为war包，上图所示的`filename=“shell.war”`，而连接密码通过解压war包可以还原出来，请求体下面的PK……部分就是war包内容，里面包含了密码等信息

继续按追踪流查看响应包，在第一个响应包中，存在如下特征可以确定Java版本信息为`1.7.0_121-b00`

![image-20251011234353475](https://img.yatjay.top/md/20251011234353562.png)

则按照Flag格式：flagWebShell密码\_服务器JAVA版本\_攻击者删除的文件内容}有flag如下：

flag{xxx_7u121_xxxxxxxx}

> `6u131`、`7u121`、`8u111`这些编号是 Oracle Java 旧版本（JDK 6, 7, 8 早期）的特定更新版本号，这个格式可以看作是：`<主版本>u<更新版本号>`
>
> - `6`、`7`、`8`： 这指的是 Java 的主版本。`6`= Java SE 6`7`= Java SE 7`8`= Java SE 8
> - `u`： 是 "update" 的缩写，意思是“更新”。
> - `131`、`121`、`111`： 这是 更新版本的序号。数字越大，表示该主版本下的更新发布得越晚，包含的修复和补丁越多

==1:35:01==



2、请求回显流量解密