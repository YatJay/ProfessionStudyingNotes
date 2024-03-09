# 6-12 ICMP协议

如下图所示，ICMP协议工作在网络层

![image-20230914224424778](https://img.yatjay.top/md/image-20230914224424778.png)

## ICMP简介

**网际控制报文协议**ICMP (Internet Control Message Protocol）是网络层的一个重要协议。ICMP协议用来**在网络设备间传递各种差错和控制信息**，并对于收集各种网络信息、诊断和排除各种网络故障等方面起着至关重要的作用。使用基于ICMP的应用时，需要对ICMP的工作原理非常熟悉。Ping命令使用的正是ICMP协议。

![image-20230914224724045](https://img.yatjay.top/md/image-20230914224724045.png)

## ICMP的功能

### ICMP重定向

![image-20230914224830935](https://img.yatjay.top/md/image-20230914224830935.png)

1.主机A希望发送报文到服务器A，于是根据配置的默认网关地址向**网关RTB**发送报文。

2.网关RTB收到报文后，检查报文信息，发现报文**应该转发到与源主机在同一网段的另一个网关设备RTA**，因为此转发路径是更优的路径。

3.RTB会向主机发送一个ICMP的重定向消息**Redirect消息**，通知主机直接向另一个网关RTA发送该报文。

4.主机收到**Redirect消息**后，会向RTA发送报文，然后RTA会将该报文再转发给服务器A。

### ICMP差错检测

![image-20230914225006479](https://img.yatjay.top/md/image-20230914225006479.png)

- ICMP Echo消息常用于诊断源和目的地之间的网络连通性，同时还可以提供其他信息，如报文往返时间等。

- ping命令的实质就是发送ICMP的Echo Request消息，目的IP收到后发送Echo Reply回复报文

![image-20240309154401043](https://img.yatjay.top/md/image-20240309154401043.png)

## ICMP报文格式(了解)

ICMP报文是一个三层报文，封装在IP报文里面，IP报文又封装在以太网的数据帧内

![image-20230914225637273](https://img.yatjay.top/md/image-20230914225637273.png)



## ICMP消息类型和编码类型

（对应上述Type、Code字段）

![image-20230914225657489](https://img.yatjay.top/md/image-20230914225657489.png)

## ICMP典型应用

### Ping

![image-20240309155106266](https://img.yatjay.top/md/image-20240309155106266.png)

在华为路由器输入`ping ?`其参数罗列中，常用的是`-a`和`-c`：

- `-a` ：一台路由器有许多接口，如上述RTA有绿色和黄色各个接口，若`ping 10.0.0.2`的RTB路由器，默认通过与之直连在同一网络的绿色接口的IP来ping；而`-a`参数可以指定一个源IP地址(如上图黄色标注的接口IP)去ping
- `-c`：指定ping要发送的ICMP数据包的数量为n个

华为路由器上使用ping命令成功返回结果如下：

![image-20240309155558087](https://img.yatjay.top/md/image-20240309155558087.png)

### Tracert

tracert/trace route显示数据包在网络传输过程中所经过的每一跳。

![image-20230914230451894](https://img.yatjay.top/md/image-20230914230451894.png)

## 本节总结

1. ping使用的是哪两类ICMP消息？

   ping利用ICMP **Echo请求消息（Type值为8）**来发起检测目的可达性。目的端收到ICMP Echo请求消息后，根据IP报文头中的源地址向源端发送ICMP **Echo回复消息(Type值为0)**。

2. 当网络设备收到TTL值为0的IP报文时，会如何操作？

   如果IP数据包在到达目的地之前TTL值已经降为0，则收到IP数据包的网络设备会**丢弃该数据包**，并向源端发送ICMP的*网络不可达*消息通知源端TTL超时。