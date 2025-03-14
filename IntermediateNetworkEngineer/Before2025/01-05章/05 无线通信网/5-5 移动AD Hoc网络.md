# 5-5 移动AD Hoc网络

## AD Hoc简介

这是一种特殊的组网架构，是由802.11协议定义的

802.11定义AD Hoc网络是**由无线移动节点组成的对等网，无需网络基础设施**（即不需要无线AP支持，仅通过各个节点主机支持即可）的支持，每个节点**既是主机，又是路由器**，是一种**MANNET(Mobile Ad Hoc Network)网络**。

Ad Hoc是拉丁语，具有”即兴，临时”的意思

寝室局域网打游戏、军事使用临时网络即为这种网络

![image-20230925225506475](https://img.yatjay.top/md/image-20230925225506475.png)

## MANET网络的特点（偶尔考选择）

- 网络拓扑结构动态变化的，**不能使用传统路由协议**
- 无线信道提供的带宽较小，信号衰落和噪声干扰的影响却很大
- 无线终端携带的电源能量有限
- 容易招致网络窃听、欺骗、拒绝服务等恶意攻击的威胁

## 例题

![image-20230925225820324](https://img.yatjay.top/md/image-20230925225820324.png)

解析：Ad Hoc临时组网，不能直接使用传统路由协议

![image-20230925225841042](https://img.yatjay.top/md/image-20230925225841042.png)

解析：看到DV即Distance Vector距离矢量