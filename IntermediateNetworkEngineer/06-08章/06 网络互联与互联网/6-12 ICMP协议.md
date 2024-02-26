# 6-12 ICMP协议

![image-20230914224424778](https://img.yatjay.top/md/image-20230914224424778.png)

## ICMP简介

**网际控制报文协议**ICMP (Internet Control Message Protocol）是网络层的一个重要协议。ICMP协议用来**在网络设备间传递各种差错和控制信息**，并对于收集各种网络信息、诊断和排除各种网络故障等方面起着至关重要的作用。使用基于ICMP的应用时，需要对ICMP的工作原理非常熟悉。

![image-20230914224724045](https://img.yatjay.top/md/image-20230914224724045.png)

## ICMP重定向

![image-20230914224830935](https://img.yatjay.top/md/image-20230914224830935.png)

1.主机A希望发送报文到服务器A，于是根据配置的默认网关地址向**网关RTB**发送报文。

2.网关RTB收到报文后，检查报文信息，发现报文**应该转发到与源主机在同一网段的另一个网关设备RTA**，因为此转发路径是更优的路径。

3.RTB会向主机发送一个ICMP的重定向消息**Redirect消息**，通知主机直接向另一个网关RTA发送该报文。

4.主机收到**Redirect消息**后，会向RTA发送报文，然后RTA会将该报文再转发给服务器A。

## ICMP差错检测

![image-20230914225006479](https://img.yatjay.top/md/image-20230914225006479.png)

ICMP Echo消息常用于诊断源和目的地之间的网络连通性

同时还可以提供其他信息，如报文往返时间等。

ping命令的实质就事发送ICMP的Echo消息，目的IP收到后发送Echo回复报文

## ICMP报文格式

ICMP报文是一个三层报文，封装在IP报文里面，IP报文又封装在以太网的数据帧内

![image-20230914225637273](https://img.yatjay.top/md/image-20230914225637273.png)



## ICMP消息类型和编码类型

（对应上述Type、Code字段）

![image-20230914225657489](https://img.yatjay.top/md/image-20230914225657489.png)

## ICMP典型应用

### Ping

略

### Tracert

Tracert显示数据包在网络传输过程中所经过的每一跳。

![image-20230914230451894](https://img.yatjay.top/md/image-20230914230451894.png)

## 总结

1.Ping利用ICMP **Echo请求消息（Type值为8）**来发起检测目的可达性。目的端收到ICMP Echo请求消息后，根据IP报文头中的源地址向源端发送ICMP **Echo回复消息(Type值为0)**。

2.如果IP数据包在到达目的地之前TTL值已经降为0，则收到IP数据包的网络设备会**丢弃该数据包**，并向源端发送ICMP消息通知源端TTL超时。