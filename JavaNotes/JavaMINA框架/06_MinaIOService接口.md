# 1. IOService接口简介

1.IOService:实现了对网络通信的客户端和服务端之间的抽象，用于描述客户端的子接口IOConnector,用于描叙服务端的子接口IOAcceptor.

# 2. IOService的作用

IOService可以管理我们网络通信的客户端和服务端,并且可以管理连接双方的会话seession,同样可以添加过滤器|



# 3. IOService类结构

通过扩展子接口和抽象的子类到达扩展的目的.

![image-20220505152410790](https://img.yatjay.top/md/202205051524912.png)

# 4. 相关API

## 1. IOService常用API定义一些抽象的接口,可以获得我们的过滤器

1) getFilterChain()获得过滤器链
2) setHandler(IoHandler handler)设置我们真正业务handler
3) getSessionConfigO得到会话的配置信息.
4) dispose   在我们完成关闭连接的时候所调用的方法.

## 2. IOConnector

1. connect(SocketAddress remoteAddress)主要用户发起一个连接请求.

2) setConnectTimeout(int connectTimeout）连接超时的设置



## 3. IOAcceptor

1) bind(SocketAddress localAddress)绑定端口.
2) getLocalAddress(获得本地ip地址I

## 4. NioSocketAcceptor API

1. accept(IoProcessor<NioSession> proccessor, ServerSocketChannelhandle)接受一个连接.

2) open(SocketAddress localAddress)打开一个socketchannel 
3) select() 获得选择器

## 5.NioSocketConnector API

1) connect(SocketChannelhandle,SocketAddress remoteAddress) 用于描述连接请求
2) register(SocketChamnelhandle,AbstractPollingIoConnector. Connecti onRequest request)注册我们的I0事件

3) select(int timeout）返回选择器

