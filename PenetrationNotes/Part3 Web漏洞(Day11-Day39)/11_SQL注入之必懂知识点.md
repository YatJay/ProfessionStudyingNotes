[toc]

前言：本章节将讲解各种 WEB 层面上的有那些漏洞类型，具体漏洞的危害等级，以简要的影响范围测试进行实例分析，思维导图中的漏洞也是后面我们将要学习到的各个知识点，其中针对漏洞的形成原理，如何发现，如何利用将是本章节学习的重点内容！（**右边相对于左边比较重要，实战中左边漏洞较少**）

![](https://gitee.com/YatJay/image/raw/master/img/202201270937387.png)

# 涉及知识

## Web漏洞应用场景

- CTF
- SRC(Security Response Center,应急响应中心)：注重漏洞理解
- 红蓝对抗
- 实战：注重权限获取

## 简要说明以上漏洞危害情况

### 不同漏洞的危害程度和影响范围不同

攻击目的可能是

1. 获取网站数据，

2. 获取网站权限，

3. 其他；

攻击目的的不同决定了各种漏洞在攻击过程中扮演着不同的角色，比如攻击目的是GetShell(获取WebShell的动作又叫做GetShell，是渗透测试各项能力的综合体现，也是渗透测试一个重要的阶段性目标。)，那么在渗透过程中如果存在SQL注入漏洞，它的作用就是间接的，用于获取用户及管理员数据；如果存在文件上传漏洞，它的作用就是直接的，能够直接得到网站权限(此处不区分网站后台权限、网站权限、服务器权限)

### 1. SQL注入

1. 攻击者未经授权可以访问数据库中的数据，盗取用户的隐私以及个人信息，造成用户的信息泄露。

2. 可以对数据库的数据进行增加或删除操作，例如私自添加或删除管理员账号。

3. 如果网站目录存在写入权限，可以写入网页木马。攻击者进而可以对网页进行篡改，发布一些违法信息等。

4. 经过提权等步骤，服务器最高权限被攻击者获取。攻击者可以远程控制服务器，安装后门，得以修改或控制操作系统。

### 2. XSS

1.  窃取管理员帐号或Cookie，入侵者可以冒充管理员的身份登录后台。使得入侵者具有恶意操纵后台数据的能力，包括读取、更改、添加、删除一些信息。
2. 窃取用户的个人信息或者登录帐号，对网站的用户安全产生巨大的威胁。例如冒充用户身份进行各种操作
3. 网站挂马。先将恶意攻击代码嵌入到Web应用程序之中。当用户浏览该挂马页面时，用户的计算机会被植入木马
4. 发送广告或者垃圾信息。攻击者可以利用XSS漏洞植入广告，或者发送垃圾信息，严重影响到用户的正常使用

### 3. XXE

1. 读取任意文件
2. 执行系统命令
3. 探测内网端口
4. 攻击内网网站

### 4. 文件上传

如果 Web应用程序存在上传漏洞 , 攻击者甚至可以将一个webshell直接上传到服务器上

### 5. 文件包含

可能含有文件包含的漏洞特征：inurl:php?file=

1. web服务器的文件被外界浏览导致信息泄露
2. 脚本被任意执行，可能会篡改网站、执行非法操作、攻击其他网站

### 6. 文件读取

通过任意文件下载，可以下载服务器的任意文件，web业务的代码，服务器和系统的具体配置信息，也可以下载数据库的配置信息，以及对内网的信息探测等等

### 7. CSRF(用户请求伪造)

1. 以受害者名义发送邮件，发消息，盗取受害者的账号，甚至购买商品，虚拟货币转账，修改受害者的网络配置（比如修改路由器DNS、重置路由器密码）等等
2. 个人隐私泄露、机密资料泄露、用户甚至企业的财产安全

### 8. SSRF(服务端请求伪造)

1. 可以对外网、服务器所在内网、本地进行端口扫描，获取一些服务的banner 信息
2. 攻击运行在内网或本地的应用程序
3. 攻击内外网的 web 应用，主要是使用 GET 参数就可以实现的攻击(如：Struts2，sqli)
4. 下载内网资源(如：利用file协议读取本地文件等)
5. 进行跳板
6. 无视cdn
7. 利用Redis未授权访问，HTTP CRLF注入实现getshell

### 9. 反序列化

远程攻击者利用漏洞可在未经任何身份验证的服务器主机上执行任意代码，被攻击者间接控制服务器

### 10.代码执行

1. 代码执行漏洞造成的原理是由于服务器端没有针对执行函数做过滤，导致在没有指定绝对路径的情况下就执行命令，可能会允许攻击者通过改变 $PATH 或程序执行环境的其他方面来执行一个恶意构造的代码。造成代码执行相关的函数分别是：eval、assert函数
2. 暴露服务器信息
3. 木马植入
4. 敏感文件暴露
5. 可能升级为命令执行

### 11. 逻辑漏洞

1. 在提交订单的时候抓取数据包或者直接修改前端代码，然后对订单的金额任意修改。
2. 黑客只需要抓取Response数据包便知道验证码是多少或直接绕过
3. 有些业务的接口，因为缺少了对用户的登陆凭证的较验或者是验证存在缺陷，导致黑客可以未经授权访问这些敏感信息甚至是越权操作
4. 有些关键性的接口因为没有做验证或者其它预防机制，容易遭到枚举攻击
5. Cookie的效验值过于简单。有些web对于cookie的生成过于单一或者简单，导致黑客可以对cookie的效验值进行一个枚举
6. 单纯读取内存值数据来当作用户凭证
7. 用户修改密码时，邮箱中会收到一个含有auth的链接，在有效期内用户点击链接，即可进入重置密码环节。而大部分网站对于auth的生成都是采用rand()函数，那么这里就存在一个问题了，Windows环境下rand()最大值为32768，所以这个auth的值是可以被枚举的

### 12. 未授权访问

敏感信息泄露导致

### 13. 命令执行

1. 继承Web服务程序的权限去执行系统命令或读写文件
2. 反弹shell
3. 控制整个网站甚至控制服务器
4. 进一步内网渗透

### 14. 目录遍历

攻击者通过访问网站某一目录时，该目录没有默认首页文件或没有正确设置默认首页文件，将会把整个目录结构列出来，将网站结构完全暴露给攻击者； 攻击者可能通过浏览目录结构，访问到某些隐秘文件（如PHPINFO文件、服务器探针文件、网站管理员后台访问地址、数据库连接文件等）

## 简要说明以上漏洞等级划分

**漏洞危害决定漏洞等级**

### 高危漏洞：直接影响数据安全、权限丢失

#### 高危漏洞

- SQL注入
- 文件上传
- 文件包含
- 代码执行
- 未授权访问
- 命令执行

#### 影响

直接影响到网站权限和数据库权限，能够获取数据或者网站的敏感文件。涉及到数据安全和权限的丢失都为高危漏洞

### 中危漏洞：存在部分影响

- 反序列化

- 逻辑安全

### 低危漏洞：小范围数据安全受到影响

#### 低危漏洞

- XSS跨站脚本攻击
- 目录遍历
- 文件读取

#### 影响

网站的源码，网站部分账号密码

## 简要说明以上漏洞重点内容

### CTF

- SQL注入
- 文件上传
- 反序列化(常考)
- 代码执行

### SRC

上面图片上漏洞都能出现，其中逻辑安全漏洞(代码开发过程中逻辑处理不当)出现比较多

### 红蓝对抗

红蓝对抗强调权限，涉及的高危漏洞：

- 文件上传
- 文件包含
- 代码执行
- 命令执行

## 简要说明以上漏洞形势问题

### 找不到漏洞的原因？

1. 信息收集没做好
2. 自己对漏洞知识的理解不够
2. 漏洞挖掘经验不足

# 案例演示

## 配置靶场

项目地址：https://github.com/zhuifengshaonianhanlu/pikachu

下载后安装到本地站点根目录下，修改/inc/config.inc.php文件配置数据库信息，再访问站点进行初始化

![](https://gitee.com/YatJay/image/raw/master/img/202201271041014.png)

## SQL注入漏洞——数据库操作危害

提交查询 ‘1’ 

![](https://gitee.com/YatJay/image/raw/master/img/202201280944958.png)

对比源代码和数据库内容

相关代码

![](https://gitee.com/YatJay/image/raw/master/img/202201280950096.png)

相关数据表内容

![](https://gitee.com/YatJay/image/raw/master/img/202201280951002.png)

burp抓包

![](https://gitee.com/YatJay/image/raw/master/img/202201281030143.png)

修改数据包的id后提交查询

![](https://gitee.com/YatJay/image/raw/master/img/202201281034764.png)

可见SQL注入能够直接获取到数据库里面的数据，甚至是敏感数据。通过注入点就可以和数据库进行交互。

## 目录遍历漏洞——源码结构泄露危害

### 目录遍历漏洞案例一

点击不同链接对应访问的文件不同

```http
http://localhost/pikachu/vul/dir/dir_list.php?title=jarheads.php
```

```http
http://localhost/pikachu/vul/dir/dir_list.php?title=truman.php
```

**可以看到这个两个地址构成：网站域名-文件夹-文件名，页面的内容由等号之后的文件名决定，如果该网站对可访问的文件不做限制，那么只要修改后面的文件名就可以访问其他文件内容，甚至跨目录访问文件。**

已知`http://localhost/pikachu/vul/dir/dir_list.php?title=jarheads.php`获取的是

`D:\JayProgram\PHPstudy\PHPTutorial\WWW\pikachu\vul\dir\soup\jarheads.php`

![](https://gitee.com/YatJay/image/raw/master/img/202201281101304.png)

那么获取`D:\JayProgram\PHPstudy\PHPTutorial\WWW\test.php`就应该访问

`http://localhost/pikachu/vul/dir/dir_list.php?title=../../../../test.php`

![](https://gitee.com/YatJay/image/raw/master/img/202201281100851.png)

同理就能访问到网站配置文件等关键信息比如此靶场的config.inc.php文件中就写有数据库管理账号及密码。

已知`http://localhost/pikachu/vul/dir/dir_list.php?title=jarheads.php`获取的是

`D:\JayProgram\PHPstudy\PHPTutorial\WWW\pikachu\vul\dir\soup\jarheads.php`

要查看`D:\JayProgram\PHPstudy\PHPTutorial\WWW\pikachu\inc\config.inc.php`文件内容就需要访问

`http://localhost/pikachu/vul/dir/dir_list.php?title=../../../inc/config.inc.php`

### 目录遍历漏洞案例二

试在www目录下创建dir.php，代码如下(该代码列出文件目录树)

```php
<?php
function my_dir($dir) {
    $files = array();
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
                if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                    $files[$file] = my_dir($dir."/".$file);
                } else { //不然就将文件的名字存入数组；
                    $files[] = $file;
                }
 
            }
        }
        closedir($handle);
        return $files;
    }
}
echo "<pre>";
$d=$_GET['d'];
print_r(my_dir($d));
echo "</pre>";
```

在浏览器中进行访问网址，http://127.0.0.1/dir.php?d=pikachu，可以访问到pikachu文件夹的结构

![](https://gitee.com/YatJay/image/raw/master/img/202201281123935.png)

在在浏览器中进行访问网址，http://127.0.0.1/dir.php?d=…\ 即可访问到www上一级的全部文件结构

![](https://gitee.com/YatJay/image/raw/master/img/202201281123820.png)

到此，通过这个漏洞，我们可以获取到整个网站的**源码结构，文件名**。我们需要结合其他思路和漏洞进行安全测试

## 文件读取漏洞——源码内容获取危害

### 目录遍历和文件读取漏洞的区别

- 目录遍历漏洞：获取到文件夹、文件的内容，但不能读到文件里面的内容

- 文件读取漏洞：读取文件的内容，但不能读取文件夹的内容，也就是单个文件的读取

## 文件上传漏洞——Web权限丢失危害

欲上传一个PHP文件，内容如下：

```php
<?php
phpinfo();
?>
```

但是客户端检查文件后缀名，所以写好PHP文件之后，将文件后缀改成.jpg格式

![](https://gitee.com/YatJay/image/raw/master/img/202201281134792.png)

对改名之后的文件进行上传

![](https://gitee.com/YatJay/image/raw/master/img/202201281135493.png)

尝试访问，提示无法访问。这是因为提交的文件本质上是php代码修改成了jpg后缀，而访问时服务器认为是jpg格式文件进行解析，所以出错。

![](https://gitee.com/YatJay/image/raw/master/img/202201281137937.png)

针对客户端检查的文件上传漏洞，可以在上传后使用burp抓包，把jpg后缀修改回php后缀，既绕过了上传时的后缀名检查，又实现了php文件上传。

修改前

![](https://gitee.com/YatJay/image/raw/master/img/202201281143490.png)

修改后

![](https://gitee.com/YatJay/image/raw/master/img/202201281143589.png)

发送数据包进行上传

![](https://gitee.com/YatJay/image/raw/master/img/202201281145476.png)

访问上传的php文件则访问到的正是我们写好的php文件的内容

![](https://gitee.com/YatJay/image/raw/master/img/202201281146023.png)

**利用文件上传漏洞可以将自己的后门文件传到网站上去，植入后门，通过访问触发后门文件，以及获取网站权限。因此文件上传漏洞是高危漏洞**

## 文件下载漏洞——补充演示拓展演示

### 文件下载漏洞案例一

通过点击对应的人名，我们发现可以下载图片下来

![](https://gitee.com/YatJay/image/raw/master/img/202201281152864.png)

下载照片后，复制照片的下载地址，进行比对，发现只有文件名不同，我们可以尝试下文件下载漏洞

```http
http://127.0.0.1/pikachu/vul/unsafedownload/execdownload.php?filename=ai.png
```

```http
http://127.0.0.1/pikachu/vul/unsafedownload/execdownload.php?filename=kb.png
```

在已知网站的结构下（我有着整个网站的源码），我们可以通过改变后面的ai.png这些来下载同级或者不同级目录下的其他文件，在这里我们选择下载www目录下phpinfo.php文件，成功下载

![](https://gitee.com/YatJay/image/raw/master/img/202201281154503.png)

### 文件下载漏洞案例二

目标网站：https://www.znds.com/

下载文件链接：

```http
http://down.znds.com/getdownurl/?s=L3VwZGF0ZS8yMDIxLTEyLTI0L2RhbmdiZWltYXJrZXRfNC4zLjVfMjc3X3puZHMuYXBr
```

尝试对s=后面的内容进行base解密得到：`/update/2021-12-24/dangbeimarket_4.3.5_277_znds.apk`

而我们下载得到的文件名为：`dangbeimarket_4.3.5_277_znds.apk`

可见要下载哪一个文件，只需把s=后的文件名称用base64加密后提交，比如下载首页的index.php文件就把index.php进行加密：

```http
http://down.znds.com/getdownurl/?s=aW5kZXgucGhw
```

失败

![](https://gitee.com/YatJay/image/raw/master/img/202201281210958.png)

# 涉及资源

https://github.com/zhuifengshaonianhanlu/pikachu