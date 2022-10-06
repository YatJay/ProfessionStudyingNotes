**阅读目录**



- [前言](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label00)

- [使用](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label01)

- 

- - [AWVS界面](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label10)
  - [新建扫描任务](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label11)
  - [报告导出](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label12)
  - [表单填充](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label13)
  - [小工具](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_label14)

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

## 前言

　　AWVS是一款可与IBM AppScan比肩的、功能十分强大的Web漏洞扫描器。由[Acunetix](https://www.acunetix.com/)开发，官方站点提供了关于各种类型漏洞的解释和如何防范，具体参考：[Acunetix Web Vulnerabilities Index](https://www.acunetix.com/vulnerabilities/web/)。同时，这款工具也个人十分喜爱的一款工具，扫描快速，资源占用率低、扫描策略设置简单、批量十分方便。

　　虽然现在AWVS已经更新到了AWVS12版本，但个人用的最多的还是AWVS 10.5版本。

　　原版下载地址：[Acunetix Web Vulnerability Scanner 10.x Consultant Edition KeyGen By Hmily[LCG\]](https://www.52pojie.cn/thread-377625-1-1.html)

　　Github下载链接：[AWVS 10.5破解版](https://github.com/starnightcyber/Miscellaneous/releases/tag/awvs10.5)。

　　安装过程可参考：[AWVS12破解版使用](https://www.cnblogs.com/Hi-blog/p/AWVS12.html) 的安装破解过程。

　　AWVS官方扫描测试站点：http://testphp.vulnweb.com/。

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

## 使用

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

### AWVS界面

　　AWVS的界面相对更为简洁和直观，让人一目了然。具体如下图所示：（邮件打开查看图片更清晰哦）

　　左侧是工具栏（AWVS自带很多小的工具）、中间是扫描结果（漏洞类型和数量，同时显示站点的目录结构）、右侧包括：漏洞描述、细节、数据包查看及漏洞影响和修复方法。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191219200213658-1208156222.png)

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

### 新建扫描任务

#### 方法一：客户端界面

　　双击桌面创建的快捷方式启动AWVS扫描工具，点击[New Scan] >> 填入目标扫描地址，按提示内容（默认）操作即可。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220085133618-203293113.png)

 　采用客户端方式的便利之处在于可以直接观测的扫描进程，一些实时扫描出的问题可以直接看到，如下所示：

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220090034924-900974300.png)

#### 方法二：Web界面

　　从上得知可以访问http://localhost:8183/，来查看Web界面。![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220090656178-1138792340.png)

| ![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220090944373-339688303.png) | ![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220091009720-1348727327.png) |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
|                                                              |                                                              |

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

### 报告导出

 　工具栏Tools >> Reporter >>

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220093824715-597107984.png)

 　弹出一个新的窗口，选择Generate Report>>

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220093850331-1801937212.png)

 

 　可以设置报告中可包含的内容 >>![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220093942837-1736198082.png)

 

　　点击Generate >> 

　　右侧即是生成的报告内容，可以阅览，我们可以导出我们想要的报告格式，PDF|HTML|Word等。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220094220076-458166763.png)

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

### 表单填充

　　如测试站点有个登录框：http://testphp.vulnweb.com/login.php

　　需要输入正确的用户名密码方能进行下一步扫描工作。

　　我们直接将登录页面作为扫描的目标地址，创建任务>>>Login，点击右侧箭头标注的New Login Sequence

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220095904139-1039711350.png)

 

 　即会弹出一个模拟浏览器的登录过程，我们输入用户名密码，点击login。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220100116834-490735719.png)

 

 　登录成功之后，我们可以随意浏览，这样扫描器就能获得更多的信息，有助于发现更多的问题。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220100310043-527766238.png)

 

 　点击Finish，会让你保存当前的登录序列，保存开始扫描任务即可。

[回到顶部](https://www.cnblogs.com/Hi-blog/p/AWVS-User-Guide.html#_labelTop)

### 小工具

#### 子域名探测 

　　选择左侧Tools >> SubDomain Scanner，输入Domain进行扫描即可，但功能比较有限，可以作为辅助使用。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220101938303-1818212471.png)

 

 

#### 站点目录爬取

　　选择左侧Tools >> Site Crawler，输入站点URL，点击Start即可。

　　如图所示，在扫描站点目录的时候，很有可能发现一些意向不到的配置文件、数据备份、站点源码备份文件等。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220102234380-1278301201.png)

 

#### HTTP Editor

　　这个小工具可以让你方便的编辑HTTP Header、发送和查看响应。

![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220102910680-811195196.png)

 

 　可以查看和编辑请求Request >>![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220102953458-887040034.png)

 

#### 编码工具

　　在测试一些SQL注入|XSS漏洞，我们需要调整输入的参数，编码是其中一种重要的手段，在编辑参数时，同样提供了这样的功能，如下图所示，提供了多种编码/参数变换方式。

　　图中是对admin123做MD5运算的结果。

　　![img](https://img2018.cnblogs.com/blog/624934/201912/624934-20191220103152972-1022431553.png)