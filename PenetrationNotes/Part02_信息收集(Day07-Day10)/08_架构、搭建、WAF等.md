[TOC]

前言：信息收集对于渗透测试前期来说是非常重要的，因为只有我们掌握了目标网站或目标主机足够多的信息之后，我们才能更好地对其进行漏洞检测。在安全测试中，信息收集是非常重要的一个环节，此环节的信息将影响到后续的成功几率，**掌握信息的多少将决定发现漏洞机会大小**，换言之决定着是否能完成目标的测试任务。

前面已经讲解了CDN绕过技术，本节主要讲下图中无CDN这一块内容。

![](https://img.yatjay.top/md/202203251614641.png)

声明：以下涉及的网络真实目标只做技术分析，不做非法操作。



# 涉及知识

## CMS识别技术

见于[Part1 04_Web源码拓展.md](../Part01_基础入门(Day01-Day06)/04_Web源码拓展.md) 

## 源码获取技术

见于[Part1 04_Web源码拓展.md](../Part01_基础入门(Day01-Day06)/04_Web源码拓展.md) 

## 架构信息获取

见于[Part1 04_Web源码拓展.md](../Part01_基础入门(Day01-Day06)/04_Web源码拓展.md) 

## 站点搭建分析——搭建习惯分析

### 1. 目录型站点

#### 目录型：在同一个服务器中，通过在不同目录下搭建不同的网站

#### 示例

sti.blcu.edu.cn  和  sti.blcu.edu.cn/bbs   (当前这两个网站已经无法访问)

- sti.blcu.edu.cn 主网站,源码在主目录之下，显示为一个学院网站
- sti.blcu.edu.cn/bbs 网站 源码在主网站目录下的一个子目录之下，显示为一个由Discuz3.4搭建的论坛网站

显然学院网站和论坛网站一定不是同一套CMS中间件搭建的，一般如果其中一个网站出现漏洞，整个服务器都可能被攻破。

#### 目录型站点发现方法

在渗透测试时可以通过目录扫描工具查到主目录的子目录之下是否有其他网站

### 2. 端口型站点

#### 端口型：在同一个服务器中，通过在不同端口下搭建不同的网站

#### 示例

web.0516jz.com:80(即浏览器默认访问时使用，加不加80都一样) 和 web.0516jz.com:8080    端口类站点分析

![](https://img.yatjay.top/md/202203251615328.png)



![](https://img.yatjay.top/md/202203251615305.png)

可以看到80端口上和8080端口上是不同的两套网站程序

#### 端口型站点发现方法

- 进行端口扫描后尝试访问，或者在网上搜索信息

- 有的站点不是使用的是默认的站点80而是其他的端口、可以使用shodan这种工具去收集端口

### 3. 子域名站点

#### 子域名站点：在不同的子域名下有着不同的CMS

#### 子域名解析的两种情况

1. 不同子域名同一个服务器上，即IP地址是一样

2. 不同子域名不在同一个服务器上，又分以下两种情况

   1. 不同子域名的IP在同一网段，即属于内网安全范畴

   2. 不同子域名的IP不在同一网段，我们就需要通过分站寻找一些主站的敏感信息，相关的网站多多少少存在联系

#### 示例：

www.goodlift.net 和 bbs.goodlift.net    两个子域名两套CMS

访问前者：

![](https://img.yatjay.top/md/202203251615936.png)

可以看到www子域名网站的搭建平台：

![](https://img.yatjay.top/md/202203251615358.png)



![](https://img.yatjay.top/md/202203251615868.png)

可以看到bbs网站的搭建平台是Discuz3.4，对于两个不同的子域名，使用的搭建平台也不相同。

其原因就在于网站在设置域名解析时可能设置www和bbs分别解析到不同IP，也可能两套网站程序都在同一台服务器上，下述可见本例的www和bbs是在同一个IP即同一台服务器上。

![](https://img.yatjay.top/md/202203251615135.png)

### 4. 类似域名站点

#### 类似域名：一个网站可能有多个域名可以访问

#### 示例

以下就是同一个网站的不同域名

- www.xiaomi.com 
- www.xiaomi.cn
- www.mi.com
- www.mi.cn

#### 类似域名发现方法

- 我们可以通过编写脚本固定前面，修改后面的为com cn net这样来查询是否存在其他域名能够访问

- 固定后面的com，修改前面的来查询是否是否存在其他域名能够访问

我们可以通过对不同域名（同个网站）进行分析，这样得到的消息越多，我们越知道漏洞所在

### 5. 旁注，C段站点

#### 旁注：同服务器不同站点

同服务器不同站点。两个网站或者多个网站放在同一个服务器上，其中一个网站是你的目标,尝试去看别的网站是否有机会。

##### 前提条件

对方有多个站点位于一个服务器。

如果是独立站点服务器，而且目标只有一个站点（目标就是它），这时候不能使用旁注，而是选择用c段。

##### 示例

服务器IP：192.168.1.100

网站1：www.a.com

网站2：www.b.com（目标）

…………………………

目标是b.com，但由于技术有限，找不到漏洞，就可以先检查a的漏洞，对a进行安全测试，获取到相关的权限，进而跳转到b这个网站中去

#### C段：同网段不同服务器不同站点(涉及内网渗透相关)

同网段不同服务器不同站点。网站有一个或多个站点，通过服务器IP地址的网段来进行测试。



服务器1的IP：192.168.1.100

网站1：www.a.com（目标）

网站2：www.b.com

……………………

服务器2的IP：192.168.1.101

网站3：www.c.com

网站4：www.d.com

……………………

通过查询网段1-254，去获取101服务器权限，再通过**与101服务器处于同个网段的目标主机**来实施内网安全的测试方法，来获取指定网服务器的权限。

当一个漏洞找不到漏洞时，可以百度旁注查询，来获取其在同个服务器的不同网站，进行其进行安全测试

### 6. 搭建软件特征站点

#### 搭建软件

一体化搭建软件：宝塔（控制面板：端口号8888）、PHPSTUDY、WMAP、INMAP

搭建软件都有常规的数据库的默认账号密码，如果搭建者不去更改的话，数据库数据就很容易被泄漏。

#### 示例：

在网站数据包中查看HTTP响应头部中的Server信息：

![](https://img.yatjay.top/md/202203251615229.png)

如果是自己分别安装各Web套件，则此处一般显示为上图所示的Server:nginx，即只显示Web中间件；

如果使用现成的搭建平台搭建网站，则显示为如下的信息，信息很全基本上是搭建软件：

```http
Apache/2.4.41(win32)OpenSSL/1.1.1c mod_fcgid/2.3.9a ————宝塔
Apache/2.4.41(win32)OpenSSL/1.0.2j PHP/5.4.45  ————phpstudy
```

此时如果搭建平台存在安全漏洞，则会被利用，比如phpstudy默认安装phpadmin数据库管理软件，其默认的安全设置中存在弱口令root：root登录数据库，如果管理员不做修改就可以利用到。

## WAF防护分析
### 什么是WAF应用
#### WAF
Web应用防护系统（也称为：网站应用级入侵防御系统。英文：Web Application Firewall，简称： WAF）。利用国际上公认的一种说法：Web应用防火墙是通过执行一系列针对HTTP/HTTPS的安全策略来专门为Web应用提供保护的一款产品。

#### 分类：硬件形式和软件形式。

在安全公司单位购买的防火墙都是硬件形式，个人网站和小企业搭建都是软件形式。

### 如何快速识别WAF

#### wafw00f工具

##### Github地址

https://github.com/EnableSecurity/wafw00f

##### 官方使用文档

https://github.com/enablesecurity/wafw00f/wiki

##### 安装环境

python3环境 ——→使用 pip install wafw00f 进行安装

##### 使用方法

a. Windows下安装使用

教程地址： https://www.cnblogs.com/qingchengzi/articles/13451885.html

b. Linux下安装使用

开启命令：wafw00f  域名/IP

##### 可识别WAF库

```
ACE XML Gateway                  Cisco
     aeSecure                         aeSecure
     AireeCDN                         Airee
     Airlock                          Phion/Ergon
     Alert Logic                      Alert Logic
     AliYunDun                        Alibaba Cloud Computing
     Anquanbao                        Anquanbao
     AnYu                             AnYu Technologies
     Approach                         Approach
     AppWall                          Radware
     Armor Defense                    Armor
     ArvanCloud                       ArvanCloud
     ASP.NET Generic                  Microsoft
     ASPA Firewall                    ASPA Engineering Co.
     Astra                            Czar Securities
     AWS Elastic Load Balancer        Amazon
     AzionCDN                         AzionCDN
     Azure Front Door                 Microsoft
     Barikode                         Ethic Ninja
     Barracuda                        Barracuda Networks
     Bekchy                           Faydata Technologies Inc.
     Beluga CDN                       Beluga
     BIG-IP Local Traffic Manager     F5 Networks
     BinarySec                        BinarySec
     BitNinja                         BitNinja
     BlockDoS                         BlockDoS
     Bluedon                          Bluedon IST
     BulletProof Security Pro         AITpro Security
     CacheWall                        Varnish
     CacheFly CDN                     CacheFly
     Comodo cWatch                    Comodo CyberSecurity
     CdnNS Application Gateway        CdnNs/WdidcNet
     ChinaCache Load Balancer         ChinaCache
     Chuang Yu Shield                 Yunaq
     Cloudbric                        Penta Security
     Cloudflare                       Cloudflare Inc.
     Cloudfloor                       Cloudfloor DNS
     Cloudfront                       Amazon
     CrawlProtect                     Jean-Denis Brun
     DataPower                        IBM
     DenyALL                          Rohde & Schwarz CyberSecurity
     Distil                           Distil Networks
     DOSarrest                        DOSarrest Internet Security
     DotDefender                      Applicure Technologies
     DynamicWeb Injection Check       DynamicWeb
     Edgecast                         Verizon Digital Media
     Eisoo Cloud Firewall             Eisoo
     Expression Engine                EllisLab
     BIG-IP AppSec Manager            F5 Networks
     BIG-IP AP Manager                F5 Networks
     Fastly                           Fastly CDN
     FirePass                         F5 Networks
     FortiWeb                         Fortinet
     GoDaddy Website Protection       GoDaddy
     Greywizard                       Grey Wizard
     Huawei Cloud Firewall            Huawei
     HyperGuard                       Art of Defense
     Imunify360                       CloudLinux
     Incapsula                        Imperva Inc.
     IndusGuard                       Indusface
     Instart DX                       Instart Logic
     ISA Server                       Microsoft
     Janusec Application Gateway      Janusec
     Jiasule                          Jiasule
     Kona SiteDefender                Akamai
     KS-WAF                           KnownSec
     KeyCDN                           KeyCDN
     LimeLight CDN                    LimeLight
     LiteSpeed                        LiteSpeed Technologies
     Open-Resty Lua Nginx             FLOSS
     Oracle Cloud                     Oracle
     Malcare                          Inactiv
     MaxCDN                           MaxCDN
     Mission Control Shield           Mission Control
     ModSecurity                      SpiderLabs
     NAXSI                            NBS Systems
     Nemesida                         PentestIt
     NevisProxy                       AdNovum
     NetContinuum                     Barracuda Networks
     NetScaler AppFirewall            Citrix Systems
     Newdefend                        NewDefend
     NexusGuard Firewall              NexusGuard
     NinjaFirewall                    NinTechNet
     NullDDoS Protection              NullDDoS
     NSFocus                          NSFocus Global Inc.
     OnMessage Shield                 BlackBaud
     Palo Alto Next Gen Firewall      Palo Alto Networks
     PerimeterX                       PerimeterX
     PentaWAF                         Global Network Services
     pkSecurity IDS                   pkSec
     PT Application Firewall          Positive Technologies
     PowerCDN                         PowerCDN
     Profense                         ArmorLogic
     Puhui                            Puhui
     Qcloud                           Tencent Cloud
     Qiniu                            Qiniu CDN
     Reblaze                          Reblaze
     RSFirewall                       RSJoomla!
     RequestValidationMode            Microsoft
     Sabre Firewall                   Sabre
     Safe3 Web Firewall               Safe3
     Safedog                          SafeDog
     Safeline                         Chaitin Tech.
     SecKing                          SecKing
     eEye SecureIIS                   BeyondTrust
     SecuPress WP Security            SecuPress
     SecureSphere                     Imperva Inc.
     Secure Entry                     United Security Providers
     SEnginx                          Neusoft
     ServerDefender VP                Port80 Software
     Shield Security                  One Dollar Plugin
     Shadow Daemon                    Zecure
     SiteGround                       SiteGround
     SiteGuard                        Sakura Inc.
     Sitelock                         TrueShield
     SonicWall                        Dell
     UTM Web Protection               Sophos
     Squarespace                      Squarespace
     SquidProxy IDS                   SquidProxy
     StackPath                        StackPath
     Sucuri CloudProxy                Sucuri Inc.
     Tencent Cloud Firewall           Tencent Technologies
     Teros                            Citrix Systems
     Trafficshield                    F5 Networks
     TransIP Web Firewall             TransIP
     URLMaster SecurityCheck          iFinity/DotNetNuke
     URLScan                          Microsoft
     UEWaf                            UCloud
     Varnish                          OWASP
     Viettel                          Cloudrity
     VirusDie                         VirusDie LLC
     Wallarm                          Wallarm Inc.
     WatchGuard                       WatchGuard Technologies
     WebARX                           WebARX Security Solutions
     WebKnight                        AQTRONIX
     WebLand                          WebLand
     wpmudev WAF                      Incsub
     RayWAF                           WebRay Solutions
     WebSEAL                          IBM
     WebTotem                         WebTotem
     West263 CDN                      West263CDN
     Wordfence                        Defiant
     WP Cerber Security               Cerber Tech
     WTS-WAF                          WTS
     360WangZhanBao                   360 Technologies
     XLabs Security WAF               XLabs
     Xuanwudun                        Xuanwudun
     Yundun                           Yundun
     Yunsuo                           Yunsuo
     Yunjiasu                         Baidu Cloud Computing
     YXLink                           YxLink Technologies
     Zenedge                          Zenedge
     ZScaler                          Accenture
```

##### 演示

![](https://img.yatjay.top/md/202203251616534.png)

### 手动识别WAF

网页检查数据包响应头部中的信息，比如出现 X-Powered-By:WAF/2.0说明存在WAF

### 识别WAF对于安全测试的意义

如果发现目标有WAF的话，不要用扫描工具进行扫描。因为WAF会对扫描访问进行防护处理，直接扫描有可能封IP。


# 演示案例

- sti.blcu.edu.cn  和  sti.blcu.edu.cn/bbs   (当前这两个网站已经无法访问)
- web.0516jz.com:80 和 web.0516jz.com:8080    端口类站点分析
- www.goodlift.net 和 bbs.goodlift.net    两个子域名两套CMS
- mi/xiaomi-cn.com.net 等各种常用域名后缀
- wafw00f-shodan(X-Powered-By: WAF)-147.92.47.120

# 涉及资源

- https://www.shodan.iol
- https://www.webscan.ccl
- https://github.com/EnableSecurity/wafw00f
