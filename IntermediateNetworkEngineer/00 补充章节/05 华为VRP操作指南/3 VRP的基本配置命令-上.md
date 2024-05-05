# 3 VRP的基本配置命令-上

## 基本配置命令

1. 配置设备名称

```shell
[Huawei] sysname name
```

2. 设置系统时钟：用户视图下修改系统时钟

```shell
<Huawei> clock timezone time-zone-name { add | minus } offset    # 用来对本地时区信息进行设置

<Huawei> clock timezone BJ add 08:00:00   # 时区设置为北京，在UTC标准时间上+8小时
```

```shell
<Huawei> clock datetime [ utc ] HH:MM:Ss YYYY-MM-DD   # 用来设置设备当前或UTC日期和时间。
```

```shell
<Huawei> clock daylight-saving-time  # 用来设置设备的夏令时。
```

```shell
<Huawei> display clock  # 显示当前时钟信息
```

![image-20240309221452152](https://img.yatjay.top/md/image-20240309221452152.png)

3. 配置命令等级

用来设置**指定视图内的命令的级别**。命令级别分为参观、监控、配置、管理4个级别，分别对应标识0、1、2、3。

```shell
[Huawei] command-privilege level level view view-name command-key

[Huawei] command-privilege level 3 view shell display ip int b   # 登录进来用户级别为3的用户才能使用display ip int b命令
```

4. 配置用户通过Password方式登录设备

用来进入指定的用户视图并配置用户认证方式为password。系统支持的用户界面包括Console用户界面和VTY用户界面，Console界面用于本地登录，VTY界面用于远程登录。默认情况下，设备一般最多支持15个用户同时通过VTY方式访问。

```shell
[Huawei]user-interface vty 0 4   # 允许0~4共计5个vty远程用户登录访问
[Huawei-ui-vty0-4]set authentication password cipher information # 为vty远程用户设置登录密码

[Huawei-ui-vty0-4]set authentication password simple 123456
```

5. 配置用户界面参数

用来设置用户界面断开连接的超时时间。如果用户在一段时间内没有输入命令，系统将断开连接。缺省情况下，超时时间是10分钟。

```shell
[Huawei] idle-timeout minutes [ seconds ]    # 用户无操作n分钟后自动断开连接
```

6. 配置接口的IP地址

用来给设备上的物理或逻辑接口配置IP地址。

```shell
[Huawei]interface interface-number
[Huawei-interface-number]ip address ip address

[Huawei]interface g0/0/3
[Huawei-interface-number]ip address 192.168.1.3 24

```

7. 查看当前运行的配置文件(用户视图)

显示当前运行的配置，含机器出厂配置好的内容

```shell
<Huawei>display current-configuration
```



8. 配置文件保存(用户视图)

将当前在内存中的配置，保存到Flash/NVRAM等存储介质中

```shell
<Huawei>save
```

9. 查看已保存的配置(用户视图)

```shell
<Huawei>display saved-configuration
```

10. 清除已保存的配置(用户视图)

```shell
<Huawei>reset saved-configuration
```

11. 查看系统启动配置参数(用户视图)

用来查看设备本次及下次启动相关的系统软件、备份系统软件、配置文件、License文件、补丁文件以及语音文件。

```shell
<Huawei> display startup
```

12. 配置系统下次启动时使用的配置文件(用户视图)

设备升级时，可以通过此命令让设备下次启动时加载指定的配置文件

```shell
<Huawei>startup saved-configuration configuration-file

<Huawei>startup saved-configuration ly.zip  # 此条命令执行后输入display startup可以看到下次启动相关配置文件已修改为flash:/ly.zip
```

13. 配置设备重启

```shell
<Huawei>reboot
```

