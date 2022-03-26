参考文章

- https://juejin.cn/post/6989422030839889950

- https://zhuanlan.zhihu.com/p/31777509

## 一、环境描述

Docker version 19.03.5, build 633a0ea838

webgoat-8.0:v8.0.0.M26

webwolf:v8.0.0.M26

宿主机镜像：Ubuntu 16.04.6 LTS，硬盘16G。

## 二、安装过程

注:以下命令皆在root用户下运行

### 1.安装Docker环境

1.更新源

```shell
sudo -i
apt-get update && apt-get clean
apt install -y curl git
```

2.安装Docker

```shell
curl -fsSL https://get.docker.com | bash -s docker --mirror Aliyun
```

3.配置Docker加速

```shell
vi /etc/docker/daemon.json
```

添加以下内容

```json
{
    "registry-mirrors":["https://docker.mirrors.ustc.edu.cn/"]
}
```

重新启动Docker并查看Docker信息

```bash
systemctl daemon-reload
systemctl restart docker

docker info
```

### 2.安装靶场环境

**下面实际演示运行的是goatandwolf:v8.1.0集成版本**

1.搜索webgoat镜像

```shell
docker search webgoat
# webgoat/webgoat-8.0           Latest development version of WebGoat

# webgoat/webgoat-7.1           Latest stable version of WebGoat

docker search webwolf
```

2.安装webgoat和webwolf

 镜像地址

https://github.com/WebGoat/WebGoat/releases

https://hub.docker.com/r/webgoat/webgoat-8.0/tags

https://hub.docker.com/r/webgoat/webwolf/tags



```shell
docker search webgoat
docker pull webgoat/webgoat-8.0:v8.1.0  /*webgoat/webgoat-8.0:v8.1.0安装指定版本，若不指定，默认安装最新版本*/
docker pull webgoat/webwolf:v8.1.0
docker pull webgoat/goatandwolf:v8.1.0
```

3.确定下完成

 查看本机docker镜像列表情况

```shell
docker images
```

![](https://img.yatjay.top/md/202203251601095.png)

4.运行容器

```bash
docker run -d -p 8888:8888 -p 8080:8080 -p 9090:9090 webgoat/goatandwolf:v8.1.0
```

or

注意：容器存在自己定义的端口，通过端口映射到主机之后才能在主机访问

```bash
docker run --name goatandwolf -p 8888:8888 -p 8080:8080 -p 9090:9090 -t webgoat/goatandwolf:v8.1.0

//指定端口映射——容器端口:主机端口
//8888端口：导航页面——http://IP:8888
//8080端口：webgoat——http://IP:8080/WebGoat
//9090端口：webwolf——http://IP:9090/WebWolf
```
or  (使用以下命令)
```
docker run -p 8080:8080 -t webgoat/webgoat-8.0:v8.0.0.M26

//上述命令只映射8080端口，因此只能成功访问http://IP:8080/WebGoat
```

![](https://img.yatjay.top/md/202203251601742.png)

如果需要后台运行，

```shell
docker run -d -p 8080:8080 webgoat/webgoat-8.0:v8.0.0.M26
```

5.查看容器使用的宿主机端口是否被打开，若未打开则让防火墙放行该端口

```shell
netstat -ntulp |grep 8080
netstat -ntlp
```

![](https://img.yatjay.top/md/202203251601505.png)

显示正常，则至此WebGoat安装完成。

## 三、访问靶场

### 1.访问导航

先访问 `http://IP:8888` (只有8888端口映射到主机才能访问)

![](https://img.yatjay.top/md/202203251601569.png)

### 2.访问WebGoat

进入页面，`http://IP:8080/WebGoat`（注意WebGoat的大小写），点击登录按钮下的，Register new user链接，注册一个用户。 

每次重新开启该容器，都要重新注册

![](https://img.yatjay.top/md/202203251601052.png)

 登录后显示如下

![](https://img.yatjay.top/md/202203251601192.png)

### 2.访问WebWolf

如果映射了9090端口，也可同理访问WebWolf

