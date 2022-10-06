# 1. mina在应用程序中的地位

主要屏蔽了网络通信的一些细节,对sokcet进行封装,并且是NIO的一个实现架构.可以帮助我们快速的开发网络通信.常常用户游戏的开发,中间件等服务端程序.

# 2. IOService接口

用于描述我们的客户端和服务端接口,其子类是 IOConnector和IOAcceptor,分别用于描述我们的客户端和服务端，实际使用的子类分别为NIOSocketConnector和NioSocketAcceptor，IOSession是客户端和服务端连接的描述，用于发送和接收数据

IOProceser多线程环境来处理我们的连接请求流程.

IOFilter 提供数据的过滤工作:包括编解码,日志等信息的过滤.

Hanlder就是我们的业务对象,自定义的handler需要实现IOHandlderAcceptor

![285763-20180111211746769-143042617](https://img.yatjay.top/md/202205051109207.png)

# 3. mina的类图结构

![285763-20170421134836446-17844684](https://img.yatjay.top/md/202205051110890.png)

# 总结

## mina大致流程如下

客户端：IOConnector–>IOProcessor–>IOFilter–>Handler

服务端：IOAcceptor–>IOProcessor–>IOFilter–>Handler

## 大致的类

IOConnector、IOAcceptor、NIOSocketConnector、NioSocketAcceptor

IOSession、IOFilter



下一节：长连接和短连接