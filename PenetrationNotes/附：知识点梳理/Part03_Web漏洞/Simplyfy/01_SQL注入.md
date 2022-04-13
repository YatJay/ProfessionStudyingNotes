# 原理

一般代码执行SQL语句的过程，本质上就是传递SQL语句到数据库中去执行

SQL注入即是**通过参数传递，将恶意SQL语句传到后台可以执行的SQL语句当中**，实现自定义的攻击方式

1. 对用户输入的参数没有进行严格过滤（如过滤单双引号 尖括号等），就被带到数据库执行，造成了SQL注入
2. 使用了字符串拼接的方式构造SQL语句

# 产生条件

1. 可控变量：即变量可以由客户端控制，或者说可以通过参数传递改变

2. 带入数据库查询：将参数拼接到SQL语句中带入数据库执行

3. 对于用户可控制的变量未过滤或过滤不严谨

# 危害——高危漏洞

## 危害数据安全

1. 数据库信息泄露：未经授权访问数据库中信息
2. 网页篡改：登录后台发布恶意内容

## 危害网站权限

1. 网站挂马
2. 私自添加系统账号
3. 读写文件获取webshell

# 分类

## 按SQL注入参数类型分类——排除干扰符号，正确闭合SQL语句

为了便于绕过SQL语句中的干扰符号，即为了正确闭合SQL语句，需要区分提交参数的类型，主要有以下几种

1. 数字类型
2. 字符类型
3. 搜索类型(模糊查询)
4. Json类型

注：其中 SQL 语句干扰符号：’ , " ,% , ) , } 等，具体需看写法

## 按SQL注入的请求方法分类——明确参数提交位置，确定何处进行提交

区分请求方法，是为了明确SQL注入提交参数的位置在哪里

1. GET方法——`$_GET['g']`：小数据一般采用GET方法提交
2. POST方法——`$_POST['p']`：大数据一般采用POST方法提交
3. REQUEST方法——`$_REQUEST['r']`：REQUEST不论GET方法或POST方法提交参数都能接收
4. COOKIE——`$_COOKIE['c']`：COOKIE方法提交参数，需要在请求头的Cookie值处提交
5. SERVER——`$\_SERVER`：SQL注入漏洞的原理即是接收数据并且把该数据带入数据库中进行查询，若目标主机会把访问者信息中的字段带入数据库进行执行，此时数据接收发生在数据包中，只需在数据包中写注入语句，即是**HTTP头部注入**。

## 按数据库类型分类——数据库特殊性决定注入语句的构造

1. MySQL
2. Access
3. SQL server/MSSQL
4. PostgreSQL
5. Oracle
6. MongoDB
7. SQLite

# 检测

## 检测方式



## 检测位置

与数据库交互提交参数处

# 利用

## 利用目的

1. 网站数据
2. 网站权限

## 利用方式之纯粹注入——网站数据

### 手动注入——以MySQL为例

![](https://img.yatjay.top/md/202203251630319.png)

#### 联合查询的显错注入

##### 基本流程

![202201281932899](https://img.yatjay.top/md/202203261110509.png)

###### 第1步：判断是否存在注入点

###### 第2步： 猜解SQL语句执行结果列名字段数量 order by

###### 第3步：联合查询寻找输出点 union select 1,2,3,…,x

###### 第4步：数据库信息收集——在注入点处向系统自带库查询库名、表名、字段名

1. 爆数据库名database()、用户名user()

2. 爆当前数据库中数据表名group_concat(table_name) from information_schema.tables where table_schema=database()

3. 爆当前数据库数据表的字段名group_concat(column_name) from information_schema.columns where table_name=‘表名’ and table_schema=database()

###### 第5步：查询指定数据——爆字段值 group_concat(username,'>',password),2 from users



#### SQL盲注

在SQL注入过程中，应用界面仅仅返回True（页面）或者False（页面）。无法根据应用程序的返回页面得到需要的数据库信息。可以通过构造逻辑判断来得到需要的信息。

##### 基于布尔的SQL盲注——逻辑判断



##### 基于时间的SQL盲注——延时判断



##### 基于报错的SQL盲注——报错回显



#### 二次注入

##### 定义

所谓二次注入是指已存储（数据库、文件）的用户输入被读取后再次进入到SQL 查询语句中导致的注入。 二次注入是sql注入的一种，但是比普通sql注入利用更加困难，利用门槛更高。 普通注入数据直接进入到SQL 查询中，而二次注入则是输入数据经处理后存储，取出后，再次进入到SQL 查询。

##### 原理

二次注入的原理，在第一次进行数据库插入数据的时候，仅仅只是使用了` addslashes` 或者是借助 `get_magic_quotes_gpc` 对其中的特殊字符进行了转义，在写入数据库的时候还是保留了原来的数据，但是数据本身还是脏数据。

在将数据存入到了数据库中之后，开发者就认为数据是可信的。在下一次进行需要进行查询的时候，直接从数据库中取出了脏数据，没有进行进一步的检验和处理，这样就会造成SQL的二次注入。比如在第一次插入数据的时候，数据中带有单引号，直接插入到了数据库中；然后在下一次使用中在拼凑的过程中，就形成了二次注入。

#### 加解密注入

提交的参数采用了base64等加密方式将数据进行加密，再通过参数传递给服务器

##### 举例

对于  www.xxx.com/index.php?id=MQ==

- 加密部分：MQ==

- 解密结果：id=MQ==相当于id=1

对于   base64加密结果：MSBhbmQgMT0x

则提交语句为：www.xxx.com/index.php?id=MSBhbmQgMT0x

##### 附：中转注入

比如受害者网站URL注入点是经过编码的，不能直接结合sqlmap进行漏洞利用，所以本地搭建一个网站，写一个php脚本编码文件，就可以结合sqlmap工具进行测试。因为，注入点经过复杂编码之后，就不能直接结合sqlmap进行漏洞攻击了。或者sqlmap自己编写tamper脚本进行攻击

以SQL加解密注入为例，如果我们所使用的工具不提供加密模块(burp和sqlmap都是提供加密模块的)，就需要自己编写脚本实现提交参数的加密工作。脚本的目的是当提交明文数据时先把明文发送到中转文件进行加密操作，然后再发送请求

#### DNSlog注入

略

#### 堆叠注入

##### 定义

Stacked injections(堆叠注入)从名词的含义就可以看到应该是**一堆(多条)SQL语句一起执行**。而在真实的运用中也是这样的,我们知道在MySQL中,主要是命令行中,每一条语句结尾加`;`表示语句结束。这样我们就想到了是不是可以多句一起使用。这个叫做stackedinjection。

##### 原理

在SQL中，分号（;）是用来表示一条sql语句的结束。试想一下我们在 ; 结束一个sql语句后继续构造下一条语句，会不会一起执行？因此这个想法也就造就了堆叠注入。而union injection（联合注入）也是将两条语句合并在一起，两者之间有什么区别么？区别就在于union 或者union all执行的语句类型是有限的，可以用来执行查询语句，而堆叠注入可以执行的是任意的语句。

##### 局限性

堆叠注入的局限性在于并不是每一个环境下都可以执行，可能受到API或者数据库引擎不支持的限制，当然了权限不足也可以解释为什么攻击者无法修改数据或者调用一些程序。

比如MySQL可以堆叠注入，msSQL和Oracle都不支持

##### 应用场景

比如注入需要管理员账号密码，密码是加密，无法解密，就可以使用堆叠注入insert进行插入数据，用户密码是自定义的，就能正常解密登录

以及其他场景，写在分号`;`之后的SQL语句自己定义，具体情况具体对待

### 工具注入

#### SQLmap注入——MySQL，SQL server/MSSQL，Access，SQLite，Oracle，PostgreSQL，IBM DB2，Sybase等

##### SQLmap使用



#### NoSQLAttack——MongoDB

略

## 利用方式之权限操作——网站权限

### 跨库查询

1. 数据库用户类型决定操作权限
2. 若存在SQL注入漏洞的数据库用户为root，则可以跨库拿到该主机其他数据库的内容，即该主机上其他站点的数据

### 文件读写

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



# 修复与防御方案

## 1. PHP自带配置

如魔术引号开关

## 2. PHP内置函数

如`is_int`函数检查提交参数

## 3. 自定义关键字替换

如提交参数中含有`select`等关键词，使用`str_replace()`函数对敏感关键字进行替换

## 4. WAF防护软件

安全狗、宝塔等

防护软件一般也是对关键字进行防护、触发了WAF等安全软件规则会将数据包丢弃。

WAF绕过时也是通过各种方法避免触发关键字，打乱关键字匹配规则。