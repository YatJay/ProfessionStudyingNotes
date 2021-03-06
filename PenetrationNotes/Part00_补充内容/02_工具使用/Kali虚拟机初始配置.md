# 快速搭建Kali Linux

1~8参考文章：https://www.freebuf.com/sectool/269360.html

## 1、vmware版本的kali直接导入

Kali官方提供了适用于虚拟机的Kali，可以直接导入虚拟机软件而不需要使用.iso文件从头配置。下载链接[虚拟机版本Kali下载](https://www.kali.org/get-kali/#kali-virtual-machines)

![](https://img.yatjay.top/md/202203251559208.png)

如果不想使用最新版本Kali，历史版本的Kali在以下链接[Kali历史版本](http://old.kali.org/kali-images/)。VMware版本Kali的历史版本也在其中。

![](https://img.yatjay.top/md/202203251559777.png)

下载解压之后直接使用VMware——打开虚拟机——选择刚下载的文件夹中的.vmx文件即可直接开启该虚拟机

![](https://img.yatjay.top/md/202203251559589.png)

## 2、切换到root登录

第一次开机，我们只能用系统自带的kali / kali登录

![](https://img.yatjay.top/md/202203251559288.png)

用kali身份设置root密码,我设置的root / toor

```bash
┌──(kali㉿kali)-[~/Desktop]
└─$ sudo passwd root

We trust you have received the usual lecture from the local System
Administrator. It usually boils down to these three things:

    #1) Respect the privacy of others.
    #2) Think before you type.
    #3) With great power comes great responsibility.

[sudo] password for kali: 
New password: 
Retype new password: 
passwd: 
password updated successfully
```

下面重启，用root用户登录

## 3、更换Kali默认源

```bash
vim /etc/apt/sources.list
```

```bash
以下国内源随便选择即可
#中科大
deb http://mirrors.ustc.edu.cn/kali kali-rolling main non-free contrib
deb-src http://mirrors.ustc.edu.cn/kali kali-rolling main non-free contrib

#阿里云
deb http://mirrors.aliyun.com/kali kali-rolling main non-free contrib
deb-src http://mirrors.aliyun.com/kali kali-rolling main non-free contrib

#清华大学
deb http://mirrors.tuna.tsinghua.edu.cn/kali kali-rolling main contrib non-free
deb-src https://mirrors.tuna.tsinghua.edu.cn/kali kali-rolling main contrib non-free

#浙大
deb http://mirrors.zju.edu.cn/kali kali-rolling main contrib non-free
deb-src http://mirrors.zju.edu.cn/kali kali-rolling main contrib non-free

#东软大学
deb http://mirrors.neusoft.edu.cn/kali kali-rolling/main non-free contrib
deb-src http://mirrors.neusoft.edu.cn/kali kali-rolling/main non-free contrib
```

![](https://img.yatjay.top/md/202203251559901.png)

## 4、升级Kali软件

导入后不升级很多命令无法使用，若过程中如跳出其他界面，尝试q或者回车。

```bash
apt-get update //下载索引文件
apt-get upgrade  //系统将现有的Package升级,如果有相依性的问题,而此相依性需要安装其它新的Package或影响到其它Package的相依性时,此Package就不会被升级,会保留下来。
apt-get dist-upgrade  //可以聪明的解决相依性的问题,如果有相依性问题,需要安装/移除新的Package,就会试着去安装/移除它。
apt-get clean  //清理安装包
apt-get auto-clean  //清理安装包
```

运行完上述命令两次后，重启一下

## 5、apt安装 JRE 和 JDK

```bash
apt install default-jre
apt install default-jdk
```

javac检验一下（出现以下为成功）

![](https://img.yatjay.top/md/202203251559936.png)

运行完重启一下

## 6、ssh连接kali

1. 编辑ssh文件，输入命令：

   ```bash
   vim /etc/ssh/sshd_config
   ```

   按ese+ i键进入编辑模式，在尾行输入：set nu 回车，会出现行数排列

 ![](https://img.yatjay.top/md/202203251559708.png)

2. 找到大概33行，去掉PermitRootLogin注释,改成 PermitRootLogin yes

 ![](https://img.yatjay.top/md/202203251600810.png)

3. 找到大概57行，把前面#注释号删掉加上yes。

 ![](https://img.yatjay.top/md/202203251600615.png)1.

4. 再按Esc键（键盘左上角），再输入 :wq (注意冒号是英文状态下)，回车保存退出

5. systemctl restart ssh.service重启ssh服务
   systemctl status ssh.service 查看ssh状态

![](https://img.yatjay.top/md/202203251600046.png)

7. 使用Finalshell远程连接工具进行测试

![](https://img.yatjay.top/md/202203251600290.png)

若此时普通用户名kali：密码kali能够远程登录，但是root用户无法远程登录，重启Kali后再次启动ssh服务即可。

## 7、Kali汉化及安装中文输入法

### 汉化

检查更新，并且安装字体包

```bash
apt-get update
apt-get install ttf-wqy-microhei ttf-wqy-zenhei xfonts-wqy fonts-wqy-zenhei fonts-wqy-microhei
```

终端输入如下命令：

```bash
dpkg-reconfigure locales
```

按空格勾选如下三个（方括号中出现`*`就表示已选中），三个都全部选中后回车确定。

笔者使用的Kali中中文相关选项只有zh_CN.UTF-8 UTF-8，只勾选zh_CN.UTF-8 UTF-8，以及默认勾选en_US.UTF-8 UTF-8。

![](https://img.yatjay.top/md/202203251600053.jpeg)

随后提示的语言选择中选择zh_CN.UTF-8 UTF-8

![](https://img.yatjay.top/md/202203251600242.jpeg)

下面的效果即为安装成功

![](https://img.yatjay.top/md/202203251600985.jpeg)

### 使用ibus安装中文输入法

```bash
apt-get install ibus ibus-pinyin
```

![img](https://img.yatjay.top/md/202203251600012.jpeg)

```bash
ibus-setup
```

添加中文-pinyin，设置默认快捷键。

![](https://img.yatjay.top/md/202203251600300.png)

使用快捷键切换中文输入法

![](https://img.yatjay.top/md/202203251600550.png)



## 8、修改Kali默认时区

kali的默认时间是美国时间，这里我们可以用命令调整

```bash
timedatectl set-timezone "Asia/Shanghai"    #修改时区为上海
hwclock -w   #将系统时间写入到硬件时钟，防止重启系统后更改失效
```

## 9、安装GNOME桌面

参考：[xfce换gnome桌面环境](https://blog.csdn.net/zonei123/article/details/105171054)

1. 下载安装gnome

```bash
apt -y install kali-desktop-gnome
```

2. 安装完会弹出界面，选择gdm3，按回车键

![](https://img.yatjay.top/md/202203251600506.png)

3. 接着会自动解压安装gnome

![](https://img.yatjay.top/md/202203251600048.png)

4.解压安装完，重启就可以了

![](https://img.yatjay.top/md/202203251600987.png)

5. [kali解决GNOME桌面root用户登录问题](https://www.codenong.com/cs110698977/)

修改以下两个配置文件

- 第一个配置文件

```bash
vim /etc/pam.d/gdm-autologin
```

将配置文件

```bash
找到这一行：
auth required pam_succeed_if.so user != root quiet_success
然后给它添加注释变成：
#auth required pam_succeed_if.so user != root quiet_success
```

![](https://img.yatjay.top/md/202203251601521.png)



- 第二个配置文件

```bash
vim /etc/pam.d/gdm-password
```

将配置文件
```bash
找到这一行：
auth required pam_succeed_if.so user != root quiet_success
然后给它添加注释变成：
#auth required pam_succeed_if.so user != root quiet_succes
```

![](https://img.yatjay.top/md/202203251601497.png)

reboot重启使用root登录成功

![](https://img.yatjay.top/md/202203251601007.png)

## 10、Kali配置Clash代理

### Clash

- 优先选择：[通过管理脚本在Shell环境下便捷使用Clash](https://github.com/juewuy/ShellClash/blob/master/README_CN.md)
- 使用原版Clash
  - [Kali linux如何安装Clash](https://www.iculture.cc/cybersecurity/pig=265)
  - [kali使用clash for linux 代理](https://www.cnblogs.com/4396yoga7/p/15334187.html)

### 命令行代理

- [proxychains命令行代理](https://cloud.tencent.com/developer/article/1683030)
- 使用：在执行命令前添加proxychains

```bash
proxychains curl -i www.google.com
```
## 11、创建虚拟机快照

