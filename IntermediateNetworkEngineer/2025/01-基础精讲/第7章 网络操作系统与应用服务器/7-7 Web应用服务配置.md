## 7-7 Web应用服务器配置

### Web服务软件

在Linux操作系统中，常用的Web服务软件有<font color="red">Apache和Nginx</font>。通过简单的配置，即可提供<font color="red">HTTP或HTTPS服务（默认端口80和443）</font>

Windows中Web服务软件是**IIS**，可以提供**Web、FTP和SMTP服务**。**【没有POP3】**

标识一台Web主机主机：<font color="red">基于IP地址、基于端口、基于域名</font>。

### Apache安装与配置

#### Apache安装

使用yum安装Apache服务：**`[root@uos~]# yum -y install httpd`**

安装完成后验证：**`[root@uos~]# rpm -qa httpd`**

```
[root@uos~]# rpm -qa httpd
httpd-2.4.37-47.module+uelc20+724+c3a76df7.2.x86_64
```

启动Apache服务：**`[root@uos ~]# systemctl start httpd`**

停止Apache服务：**`[root@uos ~]# systemctl stop httpd`**

查看Apache服务状态：**`[root@uos ~]# systemctl status httpd`**

#### Apache配置

Web站点根目录：**`/var/www/html`**

Apache的配置文件目录： **`/etc/httpd/conf/httpd.conf`**

![image-20250311231725330](https://img.yatjay.top/md/20250311231725373.png)

### Nginx

Nginx是一款<font color="red">高性能的HTTP和反向代理</font>Web服务软件。

可以在大部分的**UNIX、Linux、Windows系统运行**，一般与其他Web中间件配合使用，实现<font color="red">反向代理、负载均衡和缓存功能</font>。

特点：**内存占用少、启动极快、高并发能力强、支持热部署**。

#### Nginx安装与启动

安装Nginx服务：**`[root@uos ~]# yum install nginx`**

启动Nginx服务：**`[root@uos #systemctl start nginx`**

停止Nginx服务：**`[root@uos ~]# systemctl stop nginx`**

查看Nginx服务状态：**`[root@uos ~]#systemctl status nginx`**

![image-20250311231812412](https://img.yatjay.top/md/20250311231812457.png)

#### 反向代理

Nginx一般部署在后端服务器之前，提供反向代理功能，代理客户端请求到后端服务器，比如Nginx代理Web服务器

反向代理：客户端（用户）<-->Nginx服务器<-->后端服务器

Nginx反向代理功能:

1. <font color="red">负载均衡</font>：将请求分发到多个后端服务器，实现负载均衡，提升系统性能和可靠性。
2. <font color="red">缓存加速</font>：缓存后端服务器的响应，减少后端负载并加快响应速度。比如视频网站，可以将近期热点视频提前缓存到Nginx的服务器，减少后端负载并加快响应
3. <font color="red">安全性增强</font>：
   - 隐藏后端服务器的IP和端口，增加安全性。
   - 可以配置SSL/TLS，提供HTTPS支持，保护数据传输安全。

反向代理一般是面向服务器，正向代理一般是面对客户端，二者区别见下表：

![image-20250311231845735](https://img.yatjay.top/md/20250311231845784.png)

### 例题

#### 例题1

![image-20250311231917168](https://img.yatjay.top/md/20250311231917211.png)

解析：

- Apache的主配置文件：`/etc/httpd/conf/httpd.conf`
- 默认站点主目录：`/var/www/html`(选一个最接近的)

#### 例题2

![image-20250311231928226](https://img.yatjay.top/md/20250311231928272.png)

解析：

- WindowsServer系统采用lIS搭建Web服务器，可以提供Web、SMTP、FTP服务，
- Linux系统采用Apache、Nginx。
- Tomcat被广泛用于基于Java的Web应用程序，通常与Nginx结合使用，Nginx可以作为反向代理，将HTTP请求分发给Tomcat。Nginx处理静态内容并将动态请求代理到Tomcat，达到负载均衡和提高性能的目的。

#### 例题3

![image-20250311231945153](https://img.yatjay.top/md/20250311231945203.png)

![image-20250311231953549](https://img.yatjay.top/md/20250311231953585.png)

