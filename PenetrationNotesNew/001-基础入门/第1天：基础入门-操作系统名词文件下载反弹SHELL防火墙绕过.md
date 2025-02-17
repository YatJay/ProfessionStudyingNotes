# Day1：基础入门-操作系统&名词&文件下载&反弹 SHELL&防火墙绕过

![image-20241013134944070](https://img.yatjay.top/md/202410131349165.png)

## 知识点

### 名词解释-渗透测试-漏洞&攻击&后门&代码&专业词

前后端，POC/EXP，Payload/Shellcode，后门/Webshe1l，木马/病毒，反弹，回显，跳板，黑白盒测试，暴力破解，社会工程学，撞库，ATT&CK等

| 术语                   | 英文全称                                           | 解释                                                         |
| ---------------------- | -------------------------------------------------- | ------------------------------------------------------------ |
| 前后端                 | Front-end and Back-end                             | 前端指用户界面部分，负责与用户的交互；后端则是处理数据逻辑的部分，通常涉及数据库操作。 |
| POC (Proof of Concept) | Proof of Concept                                   | 中文 ' 概念验证 ' ，常指一段漏洞证明的代码                   |
| EXP (Exploit)          | Exploit                                            | 中文 ' 利用 '，指利用系统漏洞进行攻击的动作                  |
| Payload/Shellcode      | -                                                  | Payload中文 ' 有效载荷 '，指成功exploit之后，真正在目标系统执行的代码或指令；<br>Shellcode是简单翻译 ' shell代码 '，是Payload的一种，由于其建立正向/反向shell而得名。 |
| 后门/Webshell          | Backdoor / Webshell                                | 后门是指绕过正常认证机制获取系统访问权限的方法；<br/>Webshell是通过网站上传到服务器上的恶意脚本，允许攻击者远程控制服务器。 |
| 木马/病毒              | Trojan / Virus                                     | 木马是一种伪装成合法软件的恶意程序，主要目的在于远程控制；<br/>病毒是可以自我复制并传播给其他计算机的恶意程序，主要是简单而有破坏性。 |
| 反弹                   | Reverse Shell                                      | 将目的主机的权限移交(反弹)到其他机器上去                     |
| 回显                   | Echo Response                                      | 在网络通信中，服务器接收到请求后返回的信息，用于确认请求已被接收。 |
| 跳板                   | Pivot                                              | 在渗透测试或攻击过程中，利用已经攻陷的系统作为进一步攻击其他系统的平台，即中介。 |
| 黑白盒测试             | Black-box Testing / White-box Testing              | 黑盒测试只关注输入和输出，不了解内部结构；<br/>白盒测试则了解并测试内部实现细节。 |
| 暴力破解               | Brute Force Attack                                 | 通过尝试所有可能的组合来猜测密码或其他秘密信息的攻击方式。   |
| 社会工程学             | Social Engineering                                 | 通过操纵**人**来获取敏感信息或使他们执行某些操作的一种攻击方法。 |
| 撞库                   | Credential Stuffing                                | 使用从一个服务泄露的用户名和密码尝试登录另一个服务，利用人们在不同服务间重复使用相同凭证的习惯。 |
| ATT&CK                 | Adversarial Tactics, Techniques & Common Knowledge | MITRE开发的一个框架，用于描述和分类攻击者的行为和技术，帮助组织理解和防御网络攻击。<br/>https://attack.mitre.org/ |

参考链接：

- https://www.cnblogs.com/sunny11/p/13583083.html

- https://forum.ywhack.com/bountytips.php?download

- https://forum.ywhack.com/reverse-shell/





### 必备技能-操作系统-用途&命令&权限&用户&防火墙



### 必备技能-文件下载-缘由&场景&使用-提权&后渗透



### 必备技能-反弹命令-缘由&场景&使用-提权&后渗透







## 演示案例

### 基础案例1：操作系统-用途&命令&权限&用户&防火墙

#### 1、个人计算机&服务器用机

#### 2、Windows&Linux常见命令

参考链接：https://blog.csdn.net/weixin_43303273/article/details/83029138

#### 3、文件权限&服务权限&用户权限等

渗透测试过程中，需要注意文件、服务、用户权限，注意权限限制及提权操作

==(https://www.bilibili.com/video/BV1Ay4y1K7X6?t=1997.0)==33:18

#### 4、系统用户&用户组&服务用户等分类

#### 5、自带防火墙出站&入站规则策略协议

### 实用案例1：文件上传下载-解决无图形化&解决数据传输



### 实用案例 2：反弹 Shell命令-解决数据回显&解决数据通讯



### 结合案例1：防火墙绕过-正向连接&反向连接&内网服务器



### 结合案例2：学会了有手就行-Fofa拿下同行Pikachu服务器

