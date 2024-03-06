# 9-15 Windows Server 2008R2 DHCP服务器2

## 第0步：关闭Vmware DHCP功能

虚拟网络编辑器：勾选仅主机模式并取消勾选“使用本地DHCP服务将IP地址分配给虚拟机”

![image-20240306231551469](https://img.yatjay.top/md/image-20240306231551469.png)

虚拟机服务器网络配置

![image-20240306231738782](https://img.yatjay.top/md/image-20240306231738782.png)

## 第1步：安装DHCP服务

服务器配置静态IP

![image-20240306232027416](https://img.yatjay.top/md/image-20240306232027416.png)

![image-20240306232118738](https://img.yatjay.top/md/image-20240306232118738.png)

服务器管理——角色——添加角色——添加角色向导——DHCP服务器——下一步

- 网络连接绑定中配置固定静态IP

![image-20240306232207013](https://img.yatjay.top/md/image-20240306232207013.png)

- IPv4 DNS设置：绑定域名、首选DNS服务器的IP地址、备用DNS服务器的IP地址

![image-20240306232358243](https://img.yatjay.top/md/image-20240306232358243.png)

- 配置DHCP作用域：即添加DHCP的地址池范围，比如从192.168.200.201~192.168.200.210，并相应配置子网掩码和默认网关（192.168.200.254，意即默认网关的IP不分配给DHCP请求的主机）

![image-20240306232327644](https://img.yatjay.top/md/image-20240306232327644.png)

- 配置DHCP IPv6：不管

- 配置结束

![image-20240306232458964](https://img.yatjay.top/md/image-20240306232458964.png)

## 第2步：测试配置是否生效

![image-20240306232648233](https://img.yatjay.top/md/image-20240306232648233.png)

ipconfig查看获取到的IP地址，可见通过我们的DHCP服务器获得到了IP地址

![image-20240306232745598](https://img.yatjay.top/md/image-20240306232745598.png)

同样的在DHCP服务器处可以看到已被分配出去的IP地址

![image-20240306233049835](https://img.yatjay.top/md/image-20240306233049835.png)

## 第3步：关闭DHCP服务，还原虚拟机网络

停止DHCP服务

![image-20240306233431343](https://img.yatjay.top/md/image-20240306233431343.png)

虚拟机配置还原

![image-20240306234252696](https://img.yatjay.top/md/image-20240306234252696.png)
