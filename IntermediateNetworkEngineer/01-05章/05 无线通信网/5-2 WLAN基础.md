# 5-2 WLAN基础

## 移动通信基础

### 移动通信的发展历程（了解）

- 1G：模拟信号蜂窝网络
- 2G及之后：数字信号蜂窝网络

![image-20230925210429233](https://img.yatjay.top/md/image-20230925210429233.png)

蜂窝网络通信使用**频分复用**技术，如下图所示，与A相邻的信道没有和A同频率的，远处不与A直接相邻的地方才有和A同频率的，实现相同频率的区域隔离，从而实现信道的频分复用

![image-20230925210508670](https://img.yatjay.top/md/image-20230925210508670.png)

### 移动通信的制式（了解）

![image-20230925210645268](https://img.yatjay.top/md/image-20230925210645268.png)

注：4G时代中国移动只有TD-LTE，其他2家有TD-LTE和FDD-LTE两种

> 工信部让中国移动在3G时代强推TD-SCDMA(大唐)，方案不成熟导致中国移动在3G时代丢失第一宝座

## 802.11标准（经常考选择）

### WLAN/802.11/WiFi的区别与联系

- WLAN(Wireless Local Area Network)：无线局域网，组成无线局域网的技术有很多，如蓝牙、红外、基于802.11协议的WLAN（即WiFi）

- WiFi：即**基于802.11协议的WLAN**。所有802.11产品都会经过WiFi联盟测试，通过测试之后贴WiFi标志

- 802.11标准共计7个协议，教材上面只有5个，即下表中的前面5个

### 802.11标准详细

务必要记住各个802.11标准的**工作频段**，**不重叠信道**个数

- **工作频段**：特殊的，802.11a工作在5.8Ghz频段；802.11n工作在2.4Ghz/5.8Ghz两个频段
- **非重叠信道**：2.4Ghz的都是3个非重叠信道；5.8Ghz的都是5个非重叠信道；2.4Ghz/5.8Ghz两个频段的是3 + 5 = 8个非重叠信道
- 调制技术：最新的n、ac和ax使用了MIMO技术(802.11n也用了，表格中没写)使得速率大幅提高
- 物理发送速率：下表中该栏标准支持的协商速率，黄色标注表示支持的最大协商速率

![image-20230925210900718](https://img.yatjay.top/md/image-20230925210900718.png)

## WLAN的网络分类（偶尔考选择）

WLAN有2种类型的网络

- 基础设施网络(Infrastructure Networking)：通过无线接入点AP接入
- **特殊网络(Ad Hoc Networking)**：不通过无线AP，仅通过计算机之间的自组建网络，如军用/寝室打局域网游戏（考点，后续讲解）
- 分布式系统：通过一台服务器把很多个无线AP集中控制起来，形成大的无线网络

![image-20230925211716703](https://img.yatjay.top/md/image-20230925211716703.png)

![image-20230925211922409](https://img.yatjay.top/md/image-20230925211922409.png)

### 例题

![image-20230925212004238](https://img.yatjay.top/md/image-20230925212004238.png)

解析：

- 802.11/802.11b/802.11g都仅支持2.4Ghz
- 802.11n支持2.4Ghz/5.8Ghz两个频段
- 802.11a仅支持5.8Ghz频段，无法与其他标准采用的频段兼容

## WLAN通信技术

无线网主要使用三种通信技术：**红外线、扩展频谱和窄带微波技术**（记住名字即可，**使用最多的是扩展频谱技术**，扩频技术主要有2种）

**扩展频谱通信**：将信号散布到更宽的带宽上以**减少发送阻塞和干扰**的机会

WLAN主要使用的有2种**扩展频谱技术**：

- 频率跳动扩频FHSS
- 直接序列扩展频谱DSSS（**记住直接序列扩展频谱DSSS通过伪随机模式产生器生成伪随机数来实现扩频**）

![image-20230925212218248](https://img.yatjay.top/md/image-20230925212218248.png)

### 例题

![image-20230925212538771](https://img.yatjay.top/md/image-20230925212538771.png)

解析：

- A：通信范围取决于功率，功率越大则范围越大；
- B、D：**扩展频谱通信**：将信号散布到更宽的带宽上以**减少发送阻塞和干扰**的机会，根通信保密无关

![image-20230925212328720](https://img.yatjay.top/md/image-20230925212328720.png)

解析：

- A：无线网主要使用三种通信技术：**红外线、扩展频谱和窄带微波技术**，扩频和红外通信技术是并列关系
- B：描述的是直接序列扩频，比较正确
- C：带宽不能一直不断扩大，受到物理限制
- D：频率许可证由国家法律规定