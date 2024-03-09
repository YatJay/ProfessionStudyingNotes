# 2 VRP的基本操作

## 视图模式

1. 用户视图
2. 系统视图

## 基本的命令结构

华为提供的命令按照一定的格式设计，用户可以通过命令行界面输入命令，由命令行界面对命令进行解析，实现用户对路由器的配置和管理。

![image-20240309213020507](https://img.yatjay.top/md/image-20240309213020507.png)

- **命令字**：规定了系统应该执行的功能，如display(查询设备状态)，reboot(重启设备）等命令字。
- **关键字**：特殊的字符构成，用于进一步约束命令，是对命令的拓展，也可用于表达命令构成逻辑而增设的补充字符串。
- **参数列表**：是对命令执行功能的进一步约束。包括一对或多对参数名和参数值。

示例：

```shell
display ip interface GE0/0/0，查看接口信息的命令
```

命令字：display

关键字：ip

参数名：interface参数值:GE0/0/0

```
reboot   重启设备
```

命令字：display

关键字：ip

参数名：interface参数值:GE0/0/0

注意：

- e：以太网口——10Mbps
- f：快速以太网口——100Mbps
- g：GigabitEthernet即G比特以太网口——1000Mbps

## 命令行视图

设备提供了多样的配置和查询命令，为便于用户使用这些命令，VRP系统**按功能分类将命令分别注册在不同的命令行视图下**。

![image-20240309213703145](https://img.yatjay.top/md/image-20240309213703145.png)

- 用户视图：用户可以完成查看运行状态和统计信息等功能
- 系统视图：用户可以配置系统参数以及通过该视图进入其他的功能配置视图
- 其他视图：比如接口视图，协议视图，用户可以进行接口参数和协议参数配置

### 命令行视图的切换

![image-20240309213918999](https://img.yatjay.top/md/image-20240309213918999.png)

命令行举例:

```shell
<Huawei>system-view                                                            # 用户首先进入用户视图,通过命令进入系统视图
[Huawei]interface GigabitEthernet 0/0/1                                # 在系统视图进入接口视图
[Huawei-GigabitEthernet0/0/1]ip address 192.168.1.124       # 配置IP地址
[Huawei-GigabitEthernet0/0/1]quit                                         # 退回到上一个视图
[Huawei]ospf 1                                                                         # 在系统视图进入协议视图
[Huawei-ospf-1]area 0                                                              # 在协议视图进入OSPF区域视图
[Huawei-ospf-1-area-0.0.0.0]return                                          # 返回用户视图
<Huawei>
```

## 编辑命令行

设备的命令行界面提供基本的命令行编辑功能，以下为常用的编辑功能:

1、功能键

- **退格键Backspace**：删除光标位置的前一个字符，光标左移，若已经到达命令首，则响铃告警。
- **左光标键←或<Ctrl+B>**：光标向左移动一个字符位置，若已经到达命令首，则响铃告警。
- **右光标键→或<Ctrl+F>**：光标向右移动一个字符位置，若已经到达命令尾，则响铃告警。

2、不完整关键字输入

- 设备支持不完整关键字输入，即在当前视图下，当输入的字符能够**匹配唯一**的关键字时，可以不必输入完整的关键字，例如:

![image-20240309214408651](https://img.yatjay.top/md/image-20240309214408651.png)

3、Tab键的使用:

- 如果与之匹配的关键字唯一，按下\<Tab>键，系统自动补全关键字，补全后，反复按\<Tab>关键字不变。

```shell
[Huawei] info-                #按下Tab键
[Huawei] info-center
```

- 如果与之匹配的关键字不唯一，反复按\<Tab>键可循环显示所有以输入字符串开头的关键字。

```shell
[Huawei] info-center log              #按下Tab键
[Huawei] info-center logbuffer    #继续按Tab键循环翻词[Huawei] info-center logfile
[Huawei] info-center loghost
```

- 如果没有与之匹配的关键字，按Tab键后，关键字不变。

```shell
[Huawei] info-center loglog             #输入错误的关键字，按下Tab键
[Huawei] info-center loglog
```

 ## 命令行在线帮助

- 用户在使用命令行时，可以使用在线帮助以获取实时帮助，从而无需记忆大量的复杂的命令。

- 命令行在线帮助可分为完全帮助和部分帮助，可通过输入“?”实现。

### 完全帮助

当用户输入命令时，可以使用命令行的完全帮助获取全部关键字和参数的提示。

```shell
<Huawei> ?
User view commands:
arp-ping               ARP-ping
autosave               <Group> autosave command group
Backup                   backup information
cd                           Change current directory
clear                       Clear
clock                       Specify the system clock
……
```

### 部分帮助

当用户输入命令时，如果只记得此命令关键字的开头一个或几个字符，可以使用命令行的部分帮助获取以该字符串开头的所有关键字的提示。

```shell
<Huawei> d?
debugging               <Group> debugging command groupdelete Delete a file
dialer                         Dialer
dir                              List files on a filesystem
display                       Display information
```

## 解读命令行的错误信息

用户键入的命令，如果通过语法检查，则正确执行，否则系统将会向用户报告错误信息。

![image-20240309215435869](https://img.yatjay.top/md/image-20240309215435869.png)

## undo命令的使用

在命令前加undo关键字，即为undo命令行。undo命令行一般用来恢复缺省情况、禁用某个功能或者删除某项配置。以下为参考案例:

- 使用undo命令行恢复缺省情况

```shell
<Huawei> system-view
[Huawei] sysname Server
[Server] undo sysname
[Huawei]
```

- 使用undo命令禁用某个功能

```shell
<Huawei> system-view
[Huawei] ftp server enable
[Huawei] undo ftp server
```

- 使用undo命令删除某项设置

```shell
[Huawei]interface g0/0/1
[Huawei-GigabitEthernet0/0/1]ip address 192.168.1.1 24
[Huawei-GigabitEthernet0/0/1]undo ip address
```

## 快捷键

用户可以使用设备中的快捷键，完成对命令的快速输入，从而简化操作。

系统中的快捷键分成两类，自定义快捷键和系统快捷键。

### 自定义快捷键

![image-20240309220105782](https://img.yatjay.top/md/image-20240309220105782.png)

### 系统快捷键

![image-20240309220121278](https://img.yatjay.top/md/image-20240309220121278.png)

## 例题

![image-20240309220139998](https://img.yatjay.top/md/image-20240309220139998.png)