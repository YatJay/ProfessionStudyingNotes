## 7-3 UOS Linux网络配置

### UOS网络配置文件

#### /etc/sysconfig/network-script/ifcfg-ensxxx

网卡配置文件：`/etc/sysconfig/network-script/ifcfg-ensxxx`

```bash
TYPE=Ethernet     # 网络接口类型
BOOTPROTO=static     #静态地址
 DEFROUTE=yes     #是否将该接口设置为默认路由
IPV4 FAILURE FATAL=no
IPV6 INIT=yes     #是否支持 IPv6
IPV6 AUTOCONF=yes
IPV6 DEFROUTE=yes
IPV6 FAILURE FATAL=no
NAME=ens32     #网卡名称，不同版本的Linux网卡命名略有不同
ONBOOT=yes
IPADDR=10.0.10.20     #IP地址
PREFIX=24     #子网掩码
GATEWAY=10.0.10.254     #网关
DNS1=61.134.1.4     #DNS地址
```

配置完成后，需要重启网络服务。可使用以下两条命令：

- UOS中默认使用NetworkManager服务管理网络，使用`nmcli connection reload`重启网络服务：

  ```bash
  root@admin-PC:~#nmcli connection reload
  ```

  - nmcli：即Network Manager Command Line，NetworkManager命令行
  - connection reload：网络连接重启

- 也可以手动安装`network.service`服务，使用`systemctl restart network`命令重启网络服务，建议使用NetworkManager服务管理网络：

  ```bash
  root@admin-PC:~#systemctl restart network
  ```

  - systemctl：即System Control，系统控制
  - restart network：重启网络

#### /etc/hostname

`/etc/hostname`系统主机名文件

#### /etc/hosts

`/etc/hosts`包含IP地址和主机名之间的映射，还包含主机别名，别名是可选项。

```bash
127.0.0.1 pc1 localhost      #IP地址127.0.0.1，主机名pc1，别名localhost
192.168.0.2 pc2    #IP地址127.0.0.1，主机名pc1，没有别名
```

#### /etc/host.conf

`/etc/host.conf`指定客户机<font color="red">域名解析顺序</font>，下面为该文件内容：

```bash
order hosts, bind     # roder表示顺序：表示先查hosts文件，在查bind软件(Bind是Berkeley Internet Name Domain Service的简写，它是一款实现DNS服务器的开放源码软件)
multi on     #启用多重解析，利于负载均衡
```

#### /etc/resolv.conf

/etc/resolv.conf指定客户机<font color="red">域名搜索顺序</font>和<font color="red">DNS服务器地址</font>.

```bash
Seach test.edu.cn     # 先在test.edu.cn这个域里面找，找不到的话按下列域名服务器定制搜索
nameserver 114.114.114.114     #首选DNS服务器(中国电信)
nameserver 8.8.8.8     #备用DNS服务器(谷歌)
```

### UOS网络配置命令

#### nmcli命令

nmcli命令。nmcli是<font color="red">NetworkManager服务的命令行管理工具</font>。

支持简写：connection可简写成con或c，modify可以简写成mod或m,可以使用tab键补全命令执行。

connection网络连接常用配置命令有：

- `nmcli connection show`：显示网络连接信息
- `nmcli connection up/down ens32`：激活或停用ens32接口的网络连接
- `nmcli connection modify ens32 ipv4.addresses 10.0.10.10/32` ：设置IP地址
- `nmcli connection modify ens32 ipv4.gateway 10.0.10.254` ：设置网关
- `nmcli connection modify ens32 ipv4.dns 8.8.8.8` ：设置DNS地址
- `nmcli connection up ens32` ：激活新的配置，使上述命令配置生效

#### ifconfig网络接口设置

`[root@localhost~]# ifconfig interface-name ip-address upldown`

`[root@localhost~]# ifconfig ens32 10.1.1.1 netmask 255.255.255.0 up`：给接口配置IP地址和掩码，并up启用

`[root@localhost~]#ifconfig ens32`：显示接口配置，结果如下所示

```bash
inet 10.1.1.1 netmask 255.255.255.0  # 表示该接口的IP地址和掩码
ether 00:20:57:95:23:ce txqueuelen 1000 (Ethernet)  # 表示该接口的MAC地址
```

`ifconfig`：查看系统中所有接口的网络配置

![image-20250310194003847](https://img.yatjay.top/md/20250310194003901.png)

#### route配置路由的命令

![image-20250310193934666](https://img.yatjay.top/md/20250310193934702.png)

命令格式：`Route [add|del] [-net]-host] target [netmask Nm] [gw GW] [dev]`

通过route命令配置路由，如下所示：

- 添加一条网络路由：`route add -net 10.0.20.0 netmask 255.255.255.0 dev ens32`

  - 解析：增加一条路由，目标为10.0.20.0/24网络的请求<font color="red">由ens32网络接口转发</font>，使用route命令配置的路由在主机重启或者网卡重启后就失效了。
  - **`route add`**：这是命令的基本形式，表示添加一条新的路由规则。

  - **`-net 10.0.20.0`**：指定了目标网络的IP地址。在这个例子中，目标网络是 `10.0.20.0`。这意味着所有发往 `10.0.20.0` 网络的数据包都将遵循这条路由规则。

  - **`netmask 255.255.255.0`**：定义了目标网络的子网掩码。这里的子网掩码是 `255.255.255.0`，意味着这是一个 `/24` 子网，即该网络包含从 `10.0.20.1` 到 `10.0.20.254` 的可用IP地址。

  - **`dev ens32`**：指定了数据应该通过哪个网络接口（device）发送。在这个例子中，数据将通过名为 `ens32` 的网络接口发送。网络接口名称可能会有所不同，取决于你的系统配置和网络硬件。



- 添加一条主机路由：`route add -host target 192.168.168.119 gw 192.168.168.1`：
  - 解析：主机路由专门用于指定到达单个特定主机的路由。
  - **`route add`**：这是命令的基本形式，表示添加一条新的路由规则。
  - **`-host 192.168.168.119`**：指定了目标主机的IP地址。这意味着这条路由规则只适用于发往 `192.168.168.119` 这个具体主机的数据包。
  - **`gw 192.168.168.1`**：指定了网关（gateway）的IP地址。这里的网关是 `192.168.168.1`，意味着所有发往 `192.168.168.119` 的数据包将通过这个网关转发出去。

- IPv6中配置默认路由：`netsh interface ipv6 add route :/0 "local" 网关地址`
  - **`netsh interface ipv6 add route`**：这是命令的基本形式，表示在 IPv6 网络接口上添加一条路由规则。
  - **`::/0`**：这是一个网络前缀，表示默认路由。IPv6 地址 `::/0` 表示所有的 IPv6 地址（即 0 到 128 位的所有可能地址），相当于 IPv4 中的 `0.0.0.0/0`。这意味着这条路由适用于所有目标地址。
  - **`"local"`**：这指定了你要为其添加路由的网络接口名称。在这个例子中，假设 `"local"` 是你的网络接口名称。实际使用时，你需要替换为你的具体网络接口名称，如 `"Ethernet"` 或 `"Wi-Fi"`。
  - **`网关地址`**：这是你希望使用的网关（下一跳）的 IPv6 地址。你需要将这个占位符替换为实际的网关地址，例如 `fe80::1` 或者其他的 IPv6 地址。

#### ip命令

##### 显示网卡信息

![image-20250310193811540](https://img.yatjay.top/md/20250310193811588.png)

##### 配置IP地址

![image-20250310193818643](https://img.yatjay.top/md/20250310193818668.png)

##### 查看路由

![image-20250310193825065](https://img.yatjay.top/md/20250310193825104.png)

##### 添加静态路由

![image-20250310193831856](https://img.yatjay.top/md/20250310193831899.png)

上述`route`、`ip`命令增加一条路由，目标为10.0.20.0/24网络的请求由ens32网络接口转发，使用`ip`、 `route`命令配置的路由<font color="red">在主机重启或者网卡重启后就失效</font>。若要不失效，则需要在`etc`目录下修改相应的配置文件

#### netstat网络查询命令

netstat网络查询命令

- `-a`：显示所有连接的信息，包括正在侦听的
- `-i`：显示已配置网络设备的统计信息
- `-c`：持续更新网络状态（每秒一次）直到被人中止
- `-r`：<font color="red">显示内核路由表</font>
- `-n`：<font color="red">以数字格式</font>(即IP地址)而不是已解析的名称显示远程和本地地址

![image-20250310193635329](https://img.yatjay.top/md/20250310193635361.png)

### 例题

#### 例题1

![image-20250310202814652](https://img.yatjay.top/md/20250310202814703.png)

解析：hosts文件格式是：`IP  主机名  别名`，别名可以为空。

#### 例题2

![image-20250310202821795](https://img.yatjay.top/md/20250310202821843.png)

解析：`ifconfig eth0 192.168.1.1 mask 255.255.255.0 up`：为接口eth0配置IP地址、掩码并up启用。

注意：不同Linux版本中网卡名称有区别，有的叫`ethxxx`，有的叫`enoxxx`，有的叫`ensxxx`

#### 例题3

![image-20250310202830762](https://img.yatjay.top/md/20250310202830813.png)

解析：`ipconfig`是Windows命令

#### 例题4

![image-20250310202839328](https://img.yatjay.top/md/20250310202839373.png)

解析：命令格式：`netsh interface ipv6 addroute :/0"local" 网关地址`

- **`netsh interface ipv6 add route`**：这是命令的基本形式，表示在 IPv6 网络接口上添加一条路由规则。
- **`::/0`**：这是一个网络前缀，表示默认路由。IPv6 地址 `::/0` 表示所有的 IPv6 地址（即 0 到 128 位的所有可能地址），相当于 IPv4 中的 `0.0.0.0/0`。这意味着这条路由适用于所有目标地址。
- **`"local"`**：这指定了你要为其添加路由的网络接口名称。在这个例子中，假设 `"local"` 是你的网络接口名称。实际使用时，你需要替换为你的具体网络接口名称，如 `"Ethernet"` 或 `"Wi-Fi"`。
- **`网关地址`**：这是你希望使用的网关（下一跳）的 IPv6 地址。你需要将这个占位符替换为实际的网关地址，例如 `fe80::1` 或者其他的 IPv6 地址。

