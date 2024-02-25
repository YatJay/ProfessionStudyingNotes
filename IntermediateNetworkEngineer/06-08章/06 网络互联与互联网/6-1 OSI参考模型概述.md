# 6-1 OSI七层模型

## OSI网际互联

### OSI概念

OSI(Open System Interconnect,开放系统互联参考模型)，是由**ISO(国际标准化组织）定义**的。它是个灵活的、稳健的和可互操作的模型，并不是协议，而是一个伟大的模型，常用来分析和设计网络体系结构。

### OSI模型的目的

规范不同系统的互联标准，使两个不同的系统能够较容易的通信，而不需要改变底层硬件或软件的逻辑。

### OSI模型分为七层

从下到上分别为∶物理层、数据链路层、网络层、传输层、会话层、表示层、应用层。

### 知识扩展

几个极易混淆的缩写

| 英文                                                      | 说明                                                         |
| --------------------------------------------------------- | ------------------------------------------------------------ |
| **OSI** ( Open System Interconnect )                      | 开放系统互联参考模型                                         |
| **ISO**( International Organization for Standardization ) | 国际标准化组织                                               |
| **IOS** ( Internetwork Operating System )                 | 思科网络操作系统「华为的网络操作系统叫VRP、华三的网络操作系统叫Comware、锐捷的网络操作系统叫RGOS] |
| iOS( iPhone Operating System )                            | 苹果移动操作系统「2010年获得思科名称授权」                   |

#### 常见国际标准化组织

| 中文名                                            | 英文名                                              | 说明                                                         |
| ------------------------------------------------- | --------------------------------------------------- | ------------------------------------------------------------ |
| 国际标准化组织(ISO)                               | （International Organization for Standardization)   | 标准化领域中的一个国际组织,，定义很多领域的国际标准          |
| 电子电气工程师协会(IEEE)                          | (Institute of Electrical and Electronics Engineers) | 定义一些二层标准，如以太网                                   |
| 美国国家标准局(ANSI)                              | (American National Standards Institute)             |                                                              |
| 电子工业协会(EIA/TIA )                            | (Electronic Industries Association)                 | 定义一些底层东西                                             |
| 国际电信联盟( ITU )                               | (International Telecommunication Union)             | 运营商相关标准制定，如运营商级别的以太网、城域网技术标准     |
| 国际互联网工程任务组/INTERNET工程任务委员会(IETF) | (The Internet Engineering Task Force)               | **网络层**的很多标准由其定义，**RFC文档**即由此机构定义，如OSPF，RFC1918当中定义了私有地址，很多人是思科的工程师 |

## OSI的优点

- 将网络的通信过程划分为小一些、简单一些的部件，有助于各个部件的开发、设计和故障排除通过网络组件的标准，允许多个供应商进行开发

- 通过定义在模型的每一层实现的功能，鼓励产业的标准化

- 允许各种类型的网络硬件和软件互相通信

- 防止对某一层所做的改动影响到其他的层，这样有利于开发

