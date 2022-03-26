[toc]

**前言**：在安全测试中，若WEB无法取得进展或无WEB的情况下，我们需要借助APP或其他资产在进行信息收集，从而开展后续渗透，那么其中的信息收集就尤为重要，这里我们用案例讲解试试如何。

![](https://img.yatjay.top/md/202203251617379.png)

# 涉及知识

## 存在APP：APP提取一键反编译提取

### APK一键提取反编译

一般一键化的提取工具反编译得到的信息有限，需要配合抓包来完整提取网站相关。

本节通过漏了个大洞文件夹中的apk数据提取工具，根据使用指南文件就可以对app进行信息提取：

#### 工具使用方法

1. 将.apk文件放到apps目录中
2. 直接运行.exe文件进行apk提取
3. 在result目录中查看提取结果，每个目录名对应apk名，分为2个部分，其中files目录是反编译后的文件，需要懂一些Java代码，目前不必要理解；本节我们重点关注urls.txt，当中记录和apk相关的网站信息，不过信息有限，需要配合抓包来完整提取网站相关信息

### APP抓数据包进行工具配合

- 首先，我们需要通过burpsuite进行代理的ip地址和端口绑定，将手机的wifi设置代理，设置相同的ip地址和端口
- 接着，在burpsuite软件中，我们不采取截断再放行的方式，只点击进去HTTP history，然后通过点击手机对应要安全测试的app软件，通过点击软件中各个按钮来进行对应数据报的收集，从而知道更多的相关网站，达到了从app层面到web层面的转换，为后续安全测试提供更多可能性。
- 如果没有找到app对应的url地址/网站，我们就需要对这个app进行反编译的处理，前提懂java语言

## 各种第三方应用相关探针技术

1. 使用nmap软件进行端口扫描，通过所得端口得到后续进行安全测试的思路（这个是最全面的，但所需要的时间可能最长）。当遇到没有目标网站时，端口是实现渗透突破的好地方，端口信息可能会提供新的渗透思路。

2. 使用第三方的网站（黑暗引擎）进行信息收集（会提供对应网站的开放端口，服务等等），比较有用的几个

   - https://fofa.so/ （推荐）

   - https://www.shodan.io/  （国外网站适用）

   - https://www.zoomeye.org/

3. 第二点通过获取的信息，通过不断更改网站端口进入，从而进一步获取到更加有价值的东西,有如知道对应使用的框架，使用的中间件，开放端口是否可以访问等等信息，可以进行搜索引擎找到对应的漏洞进行安全测试（如果输入遇到账号密码的情况，可以尝试账号密码都是admin这种弱口令，万一进去了呢）

## 各种服务接口信息相关探针技术

(略)

# 涉及案例

## 案例一：APP提取及抓包及后续配合

### 某APK一键提取反编译



### 利用burp历史抓更多URL

逍遥模拟器——设置——WLAN——当前WiFi名称——长按鼠标左键——修改网络——高级——手动配置代理——设置代理IP(设置为计算机的IP)和端口

![](https://img.yatjay.top/md/202203251617541.png)

burpsuite——监听IP和端口——配置和APP设置中配置相同——不采取截断再放行的方式而是看HTTP History

![](https://img.yatjay.top/md/202203251617329.png)

若需抓https的数据包则需要在手机中安装证书：浏览器访问代理IP:端口，如192.168.1.4:8888，进入证书下载页面，点击右上角的CA Certificate下载后在文件管理器中点击安装。

点击目标APP后在burp监听历史中查看抓到的数据包信息，如果此时发现APP进入后无法加载，则先在WLAN设置中取消代理，等进入后加载过一些页面后再开启代理。





















![](https://img.yatjay.top/md/202203251617143.png)

## 案例二：某IP无WEB框架下的第三方测试

### 目标IP：45.33.42.112(可以访问)

### 各种端口一顿乱扫-思路

使用黑暗搜索引擎查找该IP的相关信息

- shodan

  ![](https://img.yatjay.top/md/202203251618919.png)

- 钟馗之眼

​		![](https://img.yatjay.top/md/202203251618339.png)

a. 端口8332：发现出现登录框

![](https://img.yatjay.top/md/202203251618580.png)

查看源码

![](https://img.yatjay.top/md/202203251618516.png)

搜索相关内容

![](https://img.yatjay.top/md/202203251618389.png)



b. 端口628

![](https://img.yatjay.top/md/202203251618368.png)

并且可以使用弱口令admin:admin登录

![](https://img.yatjay.top/md/202203251618004.png)

![](https://img.yatjay.top/md/202203251618773.png)

![](https://img.yatjay.top/md/202203251618518.png)

![](https://img.yatjay.top/md/202203251618431.png)

- Fofa

  ![](https://img.yatjay.top/md/202203251618897.png)

需要注意的是，由于黑暗搜索引擎是被动地爬取网络数据，它得到的端口信息是不全面的，一般在黑暗引擎搜索之后，有必要进行Nmap对端口做全面地扫描。

使用Nmap的`nmap -sV 45.33.42.112`命令对该IP的端口进行扫描：

![](https://img.yatjay.top/md/202203251618483.png)

### 各种接口一顿乱扫-思路

(略)

###  接口部分一顿测试-思路

(略)

## 案例三：授权测试下的服务测试

### 目标网站：www.caredaily.com

### 测试过程

#### 一、 黑暗引擎搜索收集相关信息

访问该网站

![](https://img.yatjay.top/md/202203251618273.png)

黑暗引擎搜索(shodan搜索无结果，Fofa当前(20220126)无法访问)

![](https://img.yatjay.top/md/202203251618222.png)

![](https://img.yatjay.top/md/202203251618942.png)

依次打开各站点IP进行分析，如果遇到站点打不开，可能是被云服务商防止攻击屏蔽掉了，可以打开代理尝试访问。

![](https://img.yatjay.top/md/202203251618234.png)

点击IP进行端口分析：

- 发现OpenSSH(属于网站的第三方应用)

![](https://img.yatjay.top/md/202203251619463.png)

​	搜索是否可以利用

![](https://img.yatjay.top/md/202203251619925.png)

- 发现MongoDB数据库，存在爆破的可能性

![](https://img.yatjay.top/md/202203251619629.png)

​	查询相关漏洞是否可以进行利用

![](https://img.yatjay.top/md/202203251619357.png)

- 发现FTP

  ![](https://img.yatjay.top/md/202203251619902.png)

  尝试连接失败

  ![](https://img.yatjay.top/md/202203251619095.png)

- 发现登陆入口

  ![](https://img.yatjay.top/md/202203251619322.png)

  ping网址，得到登陆入口(子域名站点)的IP

  ![](https://img.yatjay.top/md/202203251619443.png)

  使用黑暗引擎搜索该IP

  ![](https://img.yatjay.top/md/202203251619787.png)

- 使用Nmap再进行进一步的端口扫描(略)

#### 二、 基于以上信息进行站点搭建分析

##### 1. 子域名相关信息收集

百度直接搜索子域名

![](https://img.yatjay.top/md/202203251619255.png)

子域名工具搜索

![](https://img.yatjay.top/md/202203251619630.png)

![](https://img.yatjay.top/md/202203251619452.png)

- To be continue.

##### 2. 旁注、C段站点信息收集

同IP段查询

![](https://img.yatjay.top/md/202203251619722.png)

![](https://img.yatjay.top/md/202203251619141.png)

To be continue.

##### 3. 备案查询——同一个公司备案的其他网站

![](https://img.yatjay.top/md/202203251619937.png)

![](https://img.yatjay.top/md/202203251620730.png)

针对以上查到的备案信息中的网站进行信息收集

www.hkaspire.cn    访问

![](https://img.yatjay.top/md/202203251620726.png)

换到www.hkaspire.net

![](https://img.yatjay.top/md/202203251620670.png)

![](https://img.yatjay.top/md/202203251620794.png)

![](https://img.yatjay.top/md/202203251620682.png)

![](https://img.yatjay.top/md/202203251620088.png)

![](https://img.yatjay.top/md/202203251620880.png)

- 子域名查询IP

![](https://img.yatjay.top/md/202203251620848.png)

 黑暗引擎搜索有关端口信息等

![](https://img.yatjay.top/md/202203251620892.png)

To be continue.

##### 4. 类似域名站点信息收集

###### 通过官网名称搜索信息

![](https://img.yatjay.top/md/202203251620335.png)

![](https://img.yatjay.top/md/202203251620666.png)

![](https://img.yatjay.top/md/202203251620718.png)

###### 搜索近似URL相关信息

![](https://img.yatjay.top/md/202203251620601.png)

![](https://img.yatjay.top/md/202203251620274.png)

![](https://img.yatjay.top/md/202203251620991.png)

![](https://img.yatjay.top/md/202203251621911.png)

尝试admin

![](https://img.yatjay.top/md/202203251621609.png)

搜索搭建平台漏洞

![](https://img.yatjay.top/md/202203251621182.png)

To be continue.

##### 5. 搜索php有关界面

![](https://img.yatjay.top/md/202203251621321.png)

- 搜索时发现.xyz的域名

- ![](https://img.yatjay.top/md/202203251621808.png)

-  /robots.txt 发现有用信息

- ![](https://img.yatjay.top/md/202203251621783.png)

-  发现CMS信息

- ![](https://img.yatjay.top/md/202203251621617.png)

- 继续搜索收集可利用的信息。

- To be continue.

# 涉及资源

- https://fofa.so/
- http://tool.chinaz.com
- https://www.shodan.io/
- https://www.zoomeye.org/
- https://nmap.org/download.html