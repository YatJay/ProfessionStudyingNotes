# 6-13 传输层协议TCP UDP

## TCP、UDP简介

![image-20231002162748210](https://img.yatjay.top/md/image-20231002162748210.png)

- TCP(*Transmission Control Protocol*，传输控制协议)：属于可靠面向连接的网络协议
  - 可靠：保证数据传输没有被篡改
  - 面向连接：数据传输开始之前需要建立连接
  - 应用：如文件传输、登录数据传输

- UDP(*User Datagram Protocol*，用户数据报协议)：属于不可靠面向无连接的网络协议
  - 不可靠：不保证数据完整性等
  - 无连接：直接传输，不建立连接
  - 应用：对延时要求高的应用如视频传输

![image-20231002163449244](https://img.yatjay.top/md/image-20231002163449244.png)

## TCP报文格式

![image-20231002163536726](https://img.yatjay.top/md/image-20231002163536726.png)

## UDP报文格式

![image-20231002163551553](https://img.yatjay.top/md/image-20231002163551553.png)

与TCP相比，做了很大精简，省略诸多控制字段

## 端口号

![image-20231002163631588](https://img.yatjay.top/md/image-20231002163631588.png)

- 源端口随机分配，目标端口使用知名端口
- 应用客户端使用的源端口一般为系统中未使用的且大于1024
- 目的端口号为服务器端应用服务器的进程，如telnet为23

## TCP控制机制

### TCP序列号及确认号

![image-20231002164039942](https://img.yatjay.top/md/image-20231002164039942.png)

### TCP三次握手建立连接

Send SYN：标志位置为SYN表示请求建立连接

Send SYN、ACK：标志位置为SYN和ACK，ACK表示已经收到上一个SYN请求（ack = seq +1 表示收到上一个数据包），并返回一个SYN，两个数据包合二为一

Established：确认建立连接，标志位置为ACK（ack = seq +1 表示收到上一个数据包）

![image-20231002164132790](https://img.yatjay.top/md/image-20231002164132790.png)

### TCP四次挥手断开连接

发送FIN请求：标志位置为FIN、ACK

发送ACK：标志位置为ACK

再发送FIN请求：标志位置为FIN、ACK

发送ACK：标志位置为ACK

![image-20231002164731399](https://img.yatjay.top/md/image-20231002164731399.png)

### TCP的滑动窗口机制

![image-20231002165010516](https://img.yatjay.top/md/image-20231002165010516.png)