# 红队APT-钓鱼投递篇&邮件系统&SPF绕过&自建SMTP&EwoMail配合Gophish转发&劫持网页

![image-20251007213731970](https://img.yatjay.top/md/20251007213732010.png)

![image-20251007213738971](https://img.yatjay.top/md/20251007213739010.png)

## 知识点

1、邮件钓鱼-绕过-SPF机制

2、邮件钓鱼-平台-Gophish

3、邮件钓鱼-系统-SendCloud

---

经过上一节的实验我们知道，可以使用在线平台如SendCloud进行邮件转发，或者使用Gophish配置QQ邮箱或163邮箱进行邮件转发，但是收件人侧会提示转发邮箱地址，而且在线平台提供的域名地址容易被识别，QQ邮箱或163邮箱这种大批量转发时容易被封号，存在缺陷，即

- 收件人处显示的转发地址不可控
- 用到网上公开邮箱服务容易被封

因此自行搭建邮件系统是必要的，本节主要讲自建邮件系统实现邮件转发

## 演示案例
### 红队APT-邮件钓鱼-软硬绕过SPF

见201

### 红队APT-邮件钓鱼-自建EwoMail

#### 推荐配置

域名：xdsec.icu

服务器：Centos7 香港服务器 开放25端口(解析至国内服务器还需域名备案，解析至香港则不需要)

配置最低：1核2G

安装参考：http://doc.ewomail.com/docs/ewomail/

注意：国内X云服务器要解封25，建议买推荐的测试使用，请勿用于非法用途！

服务器购买：https://www.wuyuidc.com/

#### 0、连通性测试

检查运营商是否开放25端口出站方向

- 25端口是邮局通信的固定端口，不能更换成其他端口，465端口只用登录（465端口不是用于发送邮件，只用于登录，将邮件数据加密传送到本地服务器，最后本地服务器将会链接对方邮局的25端口，进行邮件发送，基本所有的邮局，都只用于25端口来接收邮件）
- 如果你的25端口出站方向被屏蔽了，那么你就不能发送邮件到外面的邮局。
- 但你可以使用465端口登录第三方服务器的邮局

```bash
yum install telnet -y
telnet smtp.qq.com 25 
```

#### 1、修改主机

关闭防护：

```bash
vi /etc/sysconfig/selinux
```

SELINUX=enforcing 改为 SELINUX=disabled

修改主机名：

```bash
hostnamectl set-hostname mail.xdsec.icu
```

修改本地解析

```bash
vi /etc/hosts
127.0.0.1 mail.xdsec.icu stmp.xdsec.icu imap.xdsec.icu xdsec.icu
```

创建swap分区（内存超过2G，可不配置）

#### 2、安装EW系统

```bash
yum -y install git
cd /root
git clone https://gitee.com/laowu5/EwoMail.git
cd /root/EwoMail/install
sh ./start.sh xdsec.icu
```

#### 3、配置域名解析

http://doc.ewomail.com/docs/ewomail/main_domain

```bash
记录类型	主机记录	解析线路	记录值	MX优先级	TTL值	状态(暂停/启用)
MX	@	默认	mail.xdsec.icu	1	600	启用
CNAME	pop	默认	mail.xdsec.icu		600	启用
CNAME	imap	默认	mail.xdsec.icu		600	启用
CNAME	smtp	默认	mail.xdsec.icu		600	启用
TXT	@	默认	v=spf1 ip4:103.97.200.62 -all		600	启用
A	mail	默认	103.97.200.62		600	启用
A	@	默认	103.97.200.62		600	启用
```

#### 4、登录后台添加邮箱用户

8000端口：即WebMail页面

8010端口：即邮件管理后台

http://xx.xx.xx.xx:8010/

默认账号：admin

默认密码：ewomail123

#### 5、登录WebMail测试发信

http://xx.xx.xx.xx:8080/

写邮件测试发送至QQ邮箱

![image-20251008161618848](https://img.yatjay.top/md/20251008161618908.png)

在QQ邮箱中查看能够正常接收，说明搭建并测试发送成功

![image-20251008161736869](https://img.yatjay.top/md/20251008161736954.png)

在QQ邮箱中回复，EW这边也能正常接收，说明测试接收成功

![image-20251008161820316](https://img.yatjay.top/md/20251008161820385.png)



### 红队APT-邮件钓鱼-Gophish批量

下面是利用自建的EwoMail配合Gophish中转绕过SPF机制的实现

上面搭建邮件系统并测试，都是通过我们定义的邮箱域名及邮箱地址向受害人发送邮件，我们的目的是在收件人一侧，看到发件人是`system@notice.aliyun.com`这种，转发地址是我们定义的邮箱地址，因此需要自建邮箱系统配合Gophish来实现

#### 配置Gophish发件接口

配置Gophish发件接口

![image-20251008162232647](https://img.yatjay.top/md/20251008162232707.png)

测试发送至某163邮箱，若能正确发送，说明中转配置没问题

![image-20251008162342911](https://img.yatjay.top/md/20251008162342965.png)

#### 配置外链跳转地址

下面配置外链跳转地址：在Landing Pages配置连接跳转地址，这里是测试，定义为百度首页

![image-20251008162602472](https://img.yatjay.top/md/20251008162602535.png)

#### 配置发件模板

下面配置发送模板：在Email Templates配置邮件模板，如推广网页

这里先导出一个阿里云的推广网页的eml

![image-20251008162809101](https://img.yatjay.top/md/20251008162809208.png)

再将该eml导入Gophish的邮件模板配置页，就能模拟推广网页的界面

![image-20251008162923031](https://img.yatjay.top/md/20251008162923104.png)

这里的Envelope Sender选项就需要**先填写成**我们自己注册域名搭建并配置的邮件地址了如`test@xiaodi.icu`

#### 配置邮件活动

在New Campaign页配置邮件活动

- name：活动名称，自定义
- Email Template：选择上面配置好的名为Aliyun-AK的页面模板
- Landing Page：选择上面配置好的外连跳转页百度
- Sending Profile：发送角色，选择我们自己搭建的域名
- Groups：这是收件人的地址组，也就是被钓鱼的地址组列表，可以提前配置

![image-20251008163445775](https://img.yatjay.top/md/20251008163445839.png)

配置完成后点击Launch Campaign运行活动，Gophish会自动按照配置批量发送邮件，在接收方查看

![image-20251008163926250](https://img.yatjay.top/md/20251008163926317.png)

会发现，发件人仍然是我们注册的域名邮箱地址，而没有变成阿里云的地址

收件人点击邮件中的外链，会跳转至百度，这是和配置相符的，实战中需要换成我们的钓鱼页面

#### 伪造发件人信息

在Email Templates页面，填写Envelope Sender时，填写伪造的发件人地址，点击保存，这样发件人就会变成我们伪造的`monitor@monitor.aliyun.com`

![image-20251008164245245](https://img.yatjay.top/md/20251008164245308.png)

重新配置New Campaign

![image-20251008164518980](https://img.yatjay.top/md/20251008164519043.png)

运行Campaign发送邮件，在接收方查看，可以看到此时发件人成了我们伪造的邮箱地址，转发人成了我们注册搭建的邮箱域名地址

![image-20251008164625918](https://img.yatjay.top/md/20251008164626014.png)

如此，就达到了自建邮箱系统配合Gophish实现伪造发件人进行批量钓鱼的目的



本节后半段是演示服务器购买、域名解析、EW搭建的操作，此处略

后续内容大概是讲：钓鱼外链网页构造、钓鱼后门附件构造等，这里先跳回蓝队钓鱼相关章节(20251008)
