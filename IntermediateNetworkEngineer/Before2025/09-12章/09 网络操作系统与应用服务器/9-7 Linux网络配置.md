# 9-7 Linux网络配置

## Linux网络配置基础

- Linux系统，设备和配置**都是文件**

- 网络相关配置文件大多数位于**/etc**目录下

- 这些文件可以在系统运行时修改，不用重启或停止任何守护程序，更改**立刻生效**

笔者注：“#”开头的为注释内容

## Linux下几个重要的网络配置文件

1. 网络配置文件：`/etc/sysconfig/network-script/ifcfg-eno1621222`（**i**nter**f**ace**c**on**f**i**g**-**e**thet**n**etxxxxx）

   不要求会写，但要求能够看得懂
   
   [【linux】配置网口IP|RDMA配置IP|ens、eno、enp网口的区别|ping不通问题排查|DNS设置](https://blog.csdn.net/bandaoyu/article/details/116308950)
   
   > ens、eno、enp网口的区别
   >
   > 扩展知识内容:
   > en标识ethernet
   > o：主板板载网卡，集成是的设备索引号p:独立网卡，PCI网卡
   > s：热插拔网卡，USB之类的扩展槽索引号
   > nnn(数字）)：MAC地址+主板信息计算得出唯一序列
   > eno1：代表由主板bios内置的网卡
   > ens1：代表有主板bios内置的PCI-E网卡enp2s0:PCI-E独立网卡
   > eth0：如果以上都不使用，则回到默认的网卡名

```bash
TYPE=Ethernet   #网络接口类型(即网卡接口类型)

BOOTPROTO=none  #静态地址

DEFROUTE=yes

......

**NAME=eno1621222  #网卡名称**

UUID=6120dma3-8123-41jf-mb23-rjedo2

ONBOOT=no

**IPADDRO=192.168.0.2   #IP地址**

**PREFX=24  #子网掩码**

**GATEWAY0=192.168.0.1 #网关地址**

**DNS1=114.114.114.114   #DNS地址**

**HWADDR=00:0C:29:61:34:7D#网卡物理地址**

......
注意：**加粗**重点项目
```

2. `/etc/hostname`：系统主机名文件

   ![image-20240305233207016](https://img.yatjay.top/md/image-20240305233207016.png)

3. `/etc/hosts`：包含**IP地址**和**主机名**之间的映射，还包含**主机别名**

   ```shell
   127.0.0.1 pc1 localhost    #127.0.0.1是IP地址，pc1是主机名，localhost是别名
   192.168.0.2 pc2
   ```

   ![image-20240305233248597](https://img.yatjay.top/md/image-20240305233248597.png)

4. `/etc/host.conf`：指定**客户机域名解析顺序**，下面为该文件内容

   ```shell
   order hosts , bind  #表示先查询本地hosts文件，如果没有结果，再尝试查找BIND dns服务器(bind就是Linux下的DNS服务器)
   ```

   ![image-20240305233459154](https://img.yatjay.top/md/image-20240305233459154.png)

5. `/etc/resolv.conf`：( 18上33）这是配置Linux系统DNS服务器的配置文件，用来指定客户机域名搜索顺序和DNS服务器的地址

```shell
Seach test.edu.cn

nameserver 114.114.114.114  #首选DNS服务器

nameserver 8.8.8.8  #备用DNS服务器
```

![image-20240305233632612](https://img.yatjay.top/md/image-20240305233632612.png)

## Linux网络接口配置

[理解物理网卡、网卡接口、内核、IP等属性的关系](https://cloud.tencent.com/developer/article/1662501)

引：**网络接口名称**就是系统内核为物理网卡取的名称，用户可以通过网卡接口名称识别网卡，但网卡接口名称是不可靠的，接口名称只是显示给用户看的，对于内核来说，内核是通过为该网卡接口分配的UUID属性来识别网卡的。内核和网卡交互时，内核需要根据网卡接口的配置信息做出决策。

#### ifconfig 命令：用来配置网络接口信息(空出某个字段要求填空)

为某网卡接口配置IP地址：`ipconfig interface-name-ip-address up|down`，示例如下

```bash
[root@localhost~]#ifconfig eno11230132    10.1.1.1 netmask 255.255.255.0 up
```

```bash
[root@localhost~]#ifconfig eno11230132      # 配置eno11230132这个网络接口的信息
inet 10.1.1.1 netmask 255.255.255.0
ether 00:20:57:95:23:ce 
txqueuelen 1000 (Ethernet)
```

### route命令：配置路由

#### route不带参数：查看本机的路由表

![image-20230311145432854](https://img.yatjay.top/md/image-20230311145432854.png)

#### 添加和删除路由：Route [add|del] [-net/-host] target [netmask Nm] [gw GW] [if]

 [add|del]：添加或删除一条路由

[-net/-host]：-net表示一条子网路由，-host表示主机路由

target：配置的目的网段或目的主机

[netmask Nm]：子网掩码

[gw GW]：网关或出接口的地址

[if]：

```shell
route add -net target 3.3.3.0/24 gw 2.2.2.254    #这是一条子网路由，表示在3.3.3.0网段的走2.2.254这个网关出去
route add -host target 192.168.168.119 gw 192.168.168.1    #这是一条主机路由，表示192.168.168.119这个IP的走192.168.168.1这个网关出去
```

### netstat命令：查询网络

- -a  显示所有连接的信息，包括正在侦听的

- -i  显示已配置网络设备的统计信息

- -c  持续更新网络状态（每秒一次）直到被人中止

- **-r  显示内核路由表**

- -n  以数字格式而不是已解析的名称显示远程和本地地址

  #### 常用netstat -nr显示路由表

![image-20230311150431868](https://img.yatjay.top/md/image-20230311150431868.png)

### 例题

![image-20230311151905771](https://img.yatjay.top/md/image-20230311151905771.png)

![image-20230311150758487](https://img.yatjay.top/md/image-20230311150758487.png)

解析：启动则最后肯定有up，配置接口IP则肯定有ethxx
