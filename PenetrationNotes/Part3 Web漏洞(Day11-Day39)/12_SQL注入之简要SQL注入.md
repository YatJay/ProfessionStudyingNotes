**前言**：在本系列课程学习中，SQL 注入漏洞将是重点部分，其中 SQL 注入又非常复杂，区分各种数据库类型，提交方法，数据类型等注入，我们需要按部就班的学习，才能学会相关 SQL 注入的核心。同样此类漏洞是WEB 安全中严重的安全漏洞，学习如何利用，挖掘，修复也是很重要的。(**右边是重点**)

![](https://gitee.com/YatJay/image/raw/master/img/202201271607354.png)

   # 涉及知识

## 上述思维导图简要说明



## SQL注入安全测试中危害

分为两类：

1. 危害数据库里的数据

   1. 数据库信息泄露
   2. 网页篡改：登陆后台后发布恶意内容

2. 直接危害到网站的权限(需要满足条件)

   1. 网站挂马 : 当拿到webshell时或者获取到服务器的权限以后，可将一些网页木马挂在服务器上，去攻击别人
   2. 私自添加系统账号
   3. 读写文件获取webshell

## SQL注入产生原理详细分析

### 原理

通过参数传递，将恶意SQL语句传到后台可以执行的SQL语句当中，实现自定义的攻击方式

1. 对用户输入的参数没有进行严格过滤（如过滤单双引号 尖括号等），就被带到数据库执行，造成了SQL注入
2. 使用了字符串拼接的方式构造SQL语句

### 代码：以sqlilabs-Lesson-2为例查看PHP参数的传递和SQL语句的构造过程

以下是sqlilabs-Lesson-2目录下的index.php代码中的php部分，注意看中文注释

```php
<?php
//including the Mysql connect parameters.
include("../sql-connections/sql-connect.php");
error_reporting(0);
// take the variables
if(isset($_GET['id']))
{
$id=$_GET['id'];                                   //Step1：接收数据：在客户端表现为.../index.php?id=1
//logging the connection parameters to a file for analysis.
$fp=fopen('result.txt','a');
fwrite($fp,'ID:'.$id."\n");
fclose($fp);


// connectivity 
$sql="SELECT * FROM users WHERE id=$id LIMIT 0,1"; //Step2：拼接数据：将接收来的数据通过变量$id拼接到SQL语句当中
$result=mysqli_query($con, $sql);                  //Step3：执行数据：执行SQL语句
   
												   //Step4：以下代码将SQL语句执行结果进行展示
$row = mysqli_fetch_array($result, MYSQLI_BOTH);       

	if($row)
	{
  	echo "<font size='5' color= '#99FF00'>";
  	echo 'Your Login name:'. $row['username'];
  	echo "<br>";
  	echo 'Your Password:' .$row['password'];
  	echo "</font>";
  	}
	else 
	{
	echo '<font color= "#FFFF00">';
	print_r(mysqli_error($con));
	echo "</font>";  
	}
}
	else
		{ 	
		echo "Please input the ID as parameter with numeric value";
		}

?>
```

以上代码执行SQL语句的过程，本质上就是传递SQL语句到数据库当中去执行

比如客户端传参数时访问链接为

`http://localhost/pentest/sqlilabs/Less-2/index.php?id=1`

![](https://gitee.com/YatJay/image/raw/master/img/202201281650180.png)

则实际在MySQL数据库中执行的查询语句就是

```sql
SELECT * FROM users WHERE id=1 LIMIT 0,1
```

![](https://gitee.com/YatJay/image/raw/master/img/202201281647940.png)

同理客户端传参数时访问链接为

`http://localhost/pentest/sqlilabs/Less-2/index.php?id=-2`

![](https://gitee.com/YatJay/image/raw/master/img/202201281650209.png)

则实际在MySQL数据库中执行的查询语句就是

```sql
SELECT * FROM users WHERE id=-2 LIMIT 0,1
```

![](https://gitee.com/YatJay/image/raw/master/img/202201281648854.png)

同理客户端传参数时访问链接为(假定知晓数据库名及列名(当然这些可以从MySQL默认的表当中获得，后续学习))

`http://localhost/pentest/sqlilabs/Less-2/index.php?id=-2 union select 1,email_id,3 from emails `

![](https://gitee.com/YatJay/image/raw/master/img/202201281652474.png)

则实际在MySQL数据库中执行的查询语句就是

```sql
SELECT * FROM users WHERE id=-2 union select 1,email_id,3 from emails LIMIT 0,1
```

![](https://gitee.com/YatJay/image/raw/master/img/202201281649176.png)

可见，如果对客户端提交的SQL语句参数不加过滤，则会直接拼接到SQL语句中执行，若有意利用，则会产生极大危害。

==注意最终显示结果的**宽度**是由原来的、规范的、正常的SQL语句执行结果决定的，这也正是提交id=-2的原因，因为基本不可能有id为负值，提交后前一句查询结果为空，但是显示位置还在，就会在空处显示为我们想要查询的SQL结果。==

![](https://gitee.com/YatJay/image/raw/master/img/202201281556015.png)

### 产生条件：可控变量，代入数据库查询，变量未过滤或过滤不严谨

- 可控变量：即变量可以由客户端控制，或者说可以通过参数传递改变

  上述源码中的`$id`就是一个可控变量：`$id=$_GET['id'];   `

- 带入数据库查询：将参数拼接到SQL语句中带入数据库执行

  上述源码中的`$sql="SELECT * FROM users WHERE id=$id LIMIT 0,1";`就把客户端传递的参数`id`通过变量`$id`拼接到了SQL语句当中完成拼接

  查询：需要有能够执行SQL语句的函数来执行拼接好的SQL语句

  上述源码中的`$result=mysqli_query($con, $sql);` 就将拼接好的SQL语句代入数据库执行查询

- 变量未过滤或过滤不严谨

### 可能存在SQL注入的特征

问：以下可能存在注入的编号选项？

答：是1 2 3 4 都可能存在SQL注入漏洞

| 选项                             | 分析                                       |
| -------------------------------- | ------------------------------------------ |
| 1 www.xiaodi8.com/index.php?id=8 | 如上例所示，可能存在                       |
| 2 www.xiaodi8.com/?id=10         | 相当于1，只是将index.php被省略了           |
| 3 www.xiaodi8.com/?id=10&x=1     | 多加了一个参数， 本质和上例相同            |
| 4 www.xiaodi8.com/index.php      | 可能有post注入，post方式提交在地址上看不到 |

问：参数x存在注入，以下那个注入测试是正确的？

答：B、C

**注意：url中&是连接不同参数的间隔符，and是SQL语句中的与关系，对前面查询的语句进行追加限定作用的，别搞混了**

| 选项                                                   | 分析                                                         |
| ------------------------------------------------------ | ------------------------------------------------------------ |
| A www.xiaodi8.com/news.php?y=1 and 1=1&x=2             | 错误。注入点是x，却把注入的代码写在了y的后面，x后面没有注入语句 |
| B www.xiaodi8.com/news.php?y=1&x=2 and 1=1             | 正确。                                                       |
| C www.xiaodi8.com/news.php?y=1 and 1=1 &x=2 and 1=1    | 正确。                                                       |
| D www.xiaodi8.com/news.php?xx=1 and 1=1 &xxx=2 and 1=1 | 错误。错误的原因是注入点是x，却没有出现x这个变量的参数传递   |


## 搭建SQL注入学习靶场环境——sqlilabs

### sqli-labs项目地址

https://github.com/Audi-1/sqli-labs

### 安装说明

1. 解压apache文件夹内的内容，例如/var/www
2. 这将在其下创建一个文件夹sqlilabs，将下载的源码放在里面
3. 打开 sql-labs 文件夹内 sql-connections 文件夹下的文件“db-creds.inc”。
4. 更新您的 MYSQL 数据库用户名和密码。
5. 从浏览器访问 sql-labs 文件夹以加载 index.html(http://127.0.0.1/pentest/sqlilabs/index.html)
6. 单击链接 setup/resetDB 以创建数据库、创建表和填充数据。
7. 准备使用的实验室，点击课程编号打开课程页面。
8. 享受实验室

## MySQL注入基础

**信息收集**：不同于前面广义的信息收集，此处SQL注入的信息收集只涉及收集数据库相关的信息，为后续SQL注入操作做准备

**数据注入**：看图

**高权限注入**：下一天

![](https://gitee.com/YatJay/image/raw/master/img/202201281516171.png)

### MySQL数据库的基本结构

网站A——→数据库A——→表名——→列名——→数据

网站B——→数据库B——→表名——→列名——→数据

网站C——→数据库C——→表名——→列名——→数据

**这也是进行SQL注入时的一般思路来源：欲获取数据库里的数据，前期的数据库名、表名、列名都需要知道**

### SQL语句基础：基本SQL语句及其执行结果

#### 进入MySQL命令行

鼠标右键phpstudy——MySQL工具——MySQL命令行——输入数据库密码——`mysql>`

#### 最常用的显示命令

| 作用                   | 命令                                         |
| ---------------------- | -------------------------------------------- |
| **显示数据库列表**     | **show databases**                           |
| **切换数据库**         | **use 数据库名**                             |
| **显示库中的数据表**   | **use 数据库名;  show tables;**              |
| **显示数据表的结构**   | **describe 表名**                            |
| **显示表中的所有记录** | **select * from 表名**                       |
| 将表中记录清空         | delete from 表名;                            |
| 建库                   | create database 库名                         |
| 建表                   | use 库名；create table 表名 (字段设定列表)； |
| 删库和删表             | drop database 库名;    drop table 表名；     |

#### 执行命令的显示效果

show databases;   显示数据库列表

![](https://gitee.com/YatJay/image/raw/master/img/202201282333037.png)

 use 数据库名;   切换数据库

![](https://gitee.com/YatJay/image/raw/master/img/202201282334302.png)

show tables;   显示数据库中的表名列表

![](https://gitee.com/YatJay/image/raw/master/img/202201282335546.png)

select * from 表名;    显示表中的所有记录

![](https://gitee.com/YatJay/image/raw/master/img/202201282336007.png)

==**select * from users where id = 5 or id = 6;  查询特定条件下的数据记录可见查询结果是一条或多条的数据记录**==

![](https://gitee.com/YatJay/image/raw/master/img/202201282338573.png)

### 对SQL注入而言MySQL必知知识点——information_schema数据库(数据库字典)

#### information_schema数据库(数据库字典)

在MYSQL5.0以上版本中，MYSQL存在一个自带的名为information_schema的数据库，它是一个存储记录有所有数据库名，表名，列名的数据库，也相当于可以通过查询它获取指定数据库下面的表名或者列名信息。

information_schema这这个数据库中保存了MySQL服务器所有数据库的信息。

如数据库名，数据库的表，表栏的数据类型与访问权限等。

再简单点，这台MySQL服务器上，到底有哪些数据库、各个数据库有哪些表，

每张表的字段类型是什么，各个数据库要什么权限才能访问，等等信息都保存在information_schema里面。

数据库中符号"."代表下一级，如xiaodi.user表示xiaodi数据库下的user表名。

| 表名                        | 字段信息                                    |
| --------------------------- | ------------------------------------------- |
| information_schema.schemata | 列schema_name记录了所有数据库的名字         |
| information_schema.tables   | 列table_schema记录了所有数据库的名字        |
| information_schema.tables   | 列table_name记录了所有数据库的表的名字      |
| information_schema.columns  | 列table_schema记录了所有数据库的名字        |
| information_schema.columns  | 列table_name记录了所有数据库的表的名字      |
| information_schema.columns  | 列column_name记录了所有数据库的表的列的名字 |

MySQL版本5.0 以下没有 information_schema 这个系统表，无法列表名等，只能暴力跑表名。

#### information_schema数据库概览

如information_schema数据库中的tables表如下：

![](https://gitee.com/YatJay/image/raw/master/img/202201281937105.png)

如information_schema数据库中的columns表如下：

![](https://gitee.com/YatJay/image/raw/master/img/202201281940212.png)

### 显错注入-联合查询（Mysql数据库）的基本流程图示

![](https://gitee.com/YatJay/image/raw/master/img/202201281932899.png)

### SQL注入的最一般步骤

#### 第1步：判断是否存在注入点

##### 老办法

- 注入点之后加`and 1=1` 页面正常
- 注入点之后加`and 1=2 `页面错误

则可能存在注入点

###### 原理：SQL语句的条件在WHERE之后(WHERE 子句用于提取那些满足指定条件的记录。)

`SELECT * FROM users WHERE id=1 and 1=1 LIMIT 0,1  ` 正常

`SELECT * FROM users WHERE id=1 and 1=2 LIMIT 0,1` 错误

`id=1 `——真

`1=1` ——真

`1=2 ` ——假

真 and 真 = 真，则`SELECT * FROM users WHERE id=1 and 1=1 LIMIT 0,1`  ，意思是查询id为1并且1=1条件下的数据库数据，若真能传入数据库执行，则必定执行正常

真 and 假 = 假，则 `SELECT * FROM users WHERE id=1 and 1=2 LIMIT 0,1` ，意思是查询id为1并且1=2条件下的数据库数据，若真能传入数据库执行，则必然执行错误

###### 能不能用or判断？不能！

`SELECT * FROM users WHERE id=1 or 1=1 LIMIT 0,1` 正常

`SELECT * FROM users WHERE id=1 or 1=2 LIMIT 0,1` 正常

原因

当or前面的语句为真的时候，不能判断第两个语句为真还是假

##### 新办法

```sql
SELECT * FROM users WHERE id=1dadad(随便输入) LIMIT 0,1
```

在可能存在注入的变量后**随便输入值**后，存在三种情况：

1. 对网页有影响即返回结果发生变化，说明带入数据库进行查询有注入点；

2. 对网页没有影响即返回结果无变化，说明没有带入数据库查询，说明对应的参数没有漏洞，可能是参数被过滤了；

3. 网站出现404错误或自动跳转到其他页面，这个时候说明网站对输入的东西有检测，出现这种情况，基本不存在SQL注入漏洞。

#### 第2步： 猜解列名字段数量 order by

提交?id=1 order by x 

x可先后尝试1、2、3、4、5……；x是正常与错误的临界值

##### order by猜解列名字段数量原理

order by是mysql中对查询数据进行排序的方法， 使用示例

```sql
select * from 表名 order by 列名(或者数字) asc；升序(默认升序)
select * from 表名 order by 列名(或者数字) desc；降序
```

这里的重点在于order by后既可以填列名或者是一个数字。举个例子： id是user表的第一列的列名，那么如果想根据id来排序，有两种写法:

```sql
select * from user order by id;
selecr * from user order by 1;
```

order by 用于判断显示位，order by 原有的作用是对查询结果按照第x个字段进行排序。

在sql注入中用order by 来判断排序，order by 1就是对一个字段进行排序，如果一共4个字段，你order by 5 ，则数据库不知道怎么排序，就执行错误无返回值。因此字段数量x就是order by正常排序与错误排序的临界值。

#### 第3步：联合查询寻找输出点 union select 1,2,3,…,x

##### union select如何发挥作用

union为联合查询，a联合b进行了查询，为查询了前面的sql语句(原有的)后再进行后面的sq查询(我们添加的)，但两张表联合查询的字段数必须相同。

##### -1 union select1,2,3,…,x寻找输出点

==///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////==

# 涉及案例

## 简易代码分析SQL注入原理

## sqlilabs注入靶场搭建简要使用

## 墨者靶机真实MySQL注入演示

靶机地址：https://www.mozhe.cn/bug/detail/elRHc1BCd2VIckQxbjduMG9BVCtkZz09bW96aGUmozhe

### 第1步：判断是否存在注入点

?id=1

![](https://gitee.com/YatJay/image/raw/master/img/202201281850420.png)

?id=1fjaioghiajkasdlg

![](https://gitee.com/YatJay/image/raw/master/img/202201281850987.png)

则说明有可能存在SQL注入

### 第2步： 猜解列名字段数量 order by

order by 4 显示正常  http://219.153.49.228:45239/new_list.php?id=1order%20by%204     %20是空格的url编码

![](https://gitee.com/YatJay/image/raw/master/img/202201282129738.png)

order by 5 显示异常  http://219.153.49.228:45239/new_list.php?id=1%20order%20by%205   %20是空格的url编码

![](https://gitee.com/YatJay/image/raw/master/img/202201282128749.png)

### 第3步：报错猜解准备

http://219.153.49.228:49521/new_list.php?id=1 union select 1,2,3,4





# 涉及资源

- https://github.com/Audi-1/sqli-labs
- 忍者安全测试系统-禁用软盘安装
- https://www.mozhe.cn/bug/detail/elRHc1BCd2VlIckQxbjduMG9BVCtkZz09bW96aGUmozhe
