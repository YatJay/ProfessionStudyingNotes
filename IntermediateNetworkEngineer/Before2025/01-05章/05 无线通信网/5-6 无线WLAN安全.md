# 5-6 WLAN安全

保证WLAN安全的方式主要有以下5种（**名词和概念务必要记忆，尤其是3、4、5**）：

1. SSID访问控制：隐藏SSID，让不知道的人搜索不到（SSID即WiFi名称）

2. 物理地址过滤：在无线路由器设置MAC地址黑白名单

3. WEP认证和加密：

   1. 认证：**PSK预共享密钥认证（PSK即预共享秘钥，输入密码才能上网，不需要用户名）**
   2. 加密：**WEP协议使用RC4加密，是一种对称加密算法**（终端与无线AP的通信数据加密后传输）

4. WPA(802.11i**草案**)：WEP认证和加密是非常不安全的，非常容易破解，因此在802.11i的草案中就提出了一种更加安全的WLAN协议——WPA
   1. 认证：802.1x认证 （不仅要输密码，还要输入用户名）
   2. 加密：RC4(**增强**版的RC4）+ TKIP(引入了TKIP临时密钥完整协议，能够动态改变密钥）
   3. WPA能够实现完整性认证和防重放攻击

5. WPA2(802.11i**正式标准**)：在**WPA基础之上**，是针对WPA的优化

   1. 认证：802.1x认证 （不仅要输密码，还要输入用户名）
   2. 加密：**基于AES的CCMP协议**(WPA2标准不再使用不安全的RC4算法，也不强制使用TKIP(早期为兼容支持双模式，目前已经只使用AES-CCMP了))
   3. WPA2能够实现完整性认证和防重放攻击


> WPA2使用的加密算法是什么？
>
> WPA2使用的加密算法主要是AES（Advanced Encryption Standard），具体来说是AES在CCMP（Counter Mode with Cipher Block Chaining Message Authentication Code Protocol）模式下的应用。AES是一种对称密钥加密算法，以其高效性、安全性以及标准化程度高等特点，被广泛认可并作为高级加密标准在全球范围内使用。
>
> 在WPA2中，AES-CCMP提供以下功能：
>
> - **数据加密**：AES算法负责对无线网络中传输的数据进行加密，确保即使数据包在空中被拦截，其内容也无法被未经授权的第三方解读。
>
> - **完整性保护**：CCMP协议包含了消息认证码（Message Authentication Code, MAC）生成机制，用于验证数据在传输过程中是否遭到篡改。接收方通过对收到的数据执行同样的计算过程并与发送方提供的MAC进行对比，可以检测出数据的任何改动。
>
> - **重放防护**：CCMP还利用计数器（Counter）机制来防范重放攻击，即恶意用户捕捉并重新发送之前捕获的数据包以欺骗网络系统。每个数据包都携带一个唯一的序列号，只有当序列号符合预期递增规律时，接收方才会接受该数据包，从而防止重放攻击的发生。
>
> 因此，WPA2通过使用AES-CCMP加密算法，不仅实现了高强度的数据加密，还提供了数据完整性和防重放攻击的安全保障，显著提高了无线网络安全水平，相较于其前身WPA使用的TKIP（Temporal Key Integrity Protocol）加密算法，AES-CCMP在安全性上有着显著优势，是目前推荐的无线网络加密标准之一。

> WPA2中使用了RC4加密吗？
>
> 不，WPA2标准并未使用RC4（Rivest Cipher 4）加密算法。RC4曾被用于早期的无线安全协议WEP（Wired Equivalent Privacy），但因其存在诸多安全漏洞，尤其是在无线网络环境中表现出的脆弱性，已被认为是不安全的加密算法。
>
> WPA2作为WEP的继任者，旨在提供更强的安全保障，其核心加密算法是AES（Advanced Encryption Standard）搭配CCMP（Counter Mode with Cipher Block Chaining Message Authentication Code Protocol）。AES-CCMP提供了更高级别的数据保密性、完整性保护以及防重放攻击能力，与RC4相比，具有显著的安全优势。
>
> 因此，WPA2完全摒弃了RC4，转而采用AES作为标准的加密算法，以应对日益增长的无线网络安全需求。在部署和配置WPA2无线网络时，应确保仅启用AES加密，避免使用包括RC4在内的其他已被证明不安全的加密方法。

## 例题

![image-20230925230805454](https://img.yatjay.top/md/image-20230925230805454.png)

解析：如果B错的更加离谱，选择D也算对

![image-20230925230813024](https://img.yatjay.top/md/image-20230925230813024.png)

解析：答案给错，应选择D选项802.11i，WPA正是802.11i的草案

![image-20230925230818799](https://img.yatjay.top/md/image-20230925230818799.png)

解析：无线局域网WPA2加密方式是基于AES的CCMP

> WPA2中使用了TKIP吗？
>
> WPA2标准本身并不强制使用TKIP（Temporal Key Integrity Protocol）加密算法。实际上，WPA2主要是为了替换WPA中使用的TKIP，转而采用更为安全的AES（Advanced Encryption Standard）加密算法与CCMP（Counter Mode with Cipher Block Chaining Message Authentication Code Protocol）协议组合。
>
> 尽管如此，在WPA2的早期实施阶段以及为了兼容一些较老的无线设备，WPA2确实允许同时支持AES-CCMP和TKIP。这种双模运作模式（即WPA2 Mixed Mode）使得支持WPA2的接入点能够同时接纳使用AES-CCMP的新设备以及可能仅支持WPA-TKIP的老设备。这样做有助于在网络升级过渡期间保持一定的兼容性，但会牺牲一定的安全性，因为网络中最弱的加密链接往往会成为潜在攻击的目标。
>
> 随着时间的推移和技术的发展，鉴于TKIP存在已知的安全弱点且已被更强大的AES-CCMP所取代，业界普遍建议禁用WPA2中的TKIP支持，转而只使用AES-CCMP作为唯一的加密机制。现代无线网络设备和安全最佳实践通常会优先选用纯WPA2-AES模式（即仅使用AES-CCMP），以确保最高的安全等级。
>
> 综上所述，虽然WPA2标准理论上可以支持TKIP，但在实际部署和配置中，尤其是在考虑最新安全建议的情况下，通常不推荐或不再使用TKIP作为WPA2的加密算法。首选方案是仅启用AES-CCMP加密。

![image-20230925230829187](https://img.yatjay.top/md/image-20230925230829187.png)

解析：WPA2使用基于AES的CCMP作为加密方案

# 章节总结

- 无线局域网扩频技术FHSS/DSSS
- 无线频谱与信道2.4G/5G，中国分别有3个和5个不重叠信道
- CSMA/CA，隐藏节点，移动AdHoc(MANET)
- 无线安全:WEP、WPA、WPA2 AES/TPIK/CCMP