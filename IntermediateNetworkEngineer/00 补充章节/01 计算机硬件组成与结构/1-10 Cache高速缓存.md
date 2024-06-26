# 1-10 Cache高速缓存

## Cache(高速缓冲存储器)

高速缓冲存储器是位于主存与CPU(寄存器在CPU内部)之间的一级存储器，由静态存储芯片(SRAM)组成，容量比较小但速度比主存高得多，接近于CPU的速度。但其成本更高，因此Cache的容量要比内存小得多，一般只有几MB。Cache存储了频繁访问内存的数据。

![image-20240401221610314](https://img.yatjay.top/md/image-20240401221610314.png)

![image-20240331101128444](https://img.yatjay.top/md/image-20240331101128444.png)

## (1)Cache原理、命中率、失效率

- 使用Cache改善系统性能的主要依据是程序的局部性原理，使用缓存的目的是改善性能，提高访问速度

  - 时间局部性：存储频繁访问内存的数据
  - 空间局部性：若访问了A指令，下一时间极有可能访问A附近的指令

- 命中率、失效率

  - 如果Cache的访问命中率为h，那么通常1-h就是Cache的失效率

  - Cache的访问周期时间是t1，主存储器的访问周期时间是t2，则整个系统的平均访存时间就是：

    **$t_3=h * t_1+(1-h) * t_2$，意即命中时访问Cache，非命中时老老实实访问主存**

## 缓存命中率计算

某流水线计算机主存的读/写时间为100ns，有一个指令和数据合一的Cache，该Cache的读/写时间为10ns，取指令的命中率为98%，取数据的命中率为95%。在执行某类程序时，约有1/5指令需要存/取一个操作数。假设指令流水线在任何时候都不阻塞，则设置Cache后，每条指令的平均访存时间约为多少？

解析：

- 主存访问周期：100ns
- Cache访问周期：10ns
- Cache取指令命中率：98%
- Cache取数据命中率：95%
- 程序取操作数指令占比：1/5
- 程序取指令指令占比：4/5

每条指令平均访问时间 = 取指令平均访问时间 + 取数据平均访问时间

取指令平均访问时间 = 2% × 100ns + 98% x 10ns

取数据平均访问时间 = 1/5×(5% x 100ns +95% x 10ns)

(2% × 100ns + 98% x 10ns)+1/5×(5% x 100ns +95% x 10ns)=14.7ns

## (2)Cache存储器的映射机制

**(了解知道几种映射名称即可，近年考试概率极低)**

分配给Cache的地址存放在一个相联存储器（CAM）中。CPU发生访存请求时会先让CAM判断所要访问的数据是否在Cache中，如果命中就直接使用。这个判断的过程就是Cache地址映射，这个速度应该尽可能快。常见的映射方法有：（**纯硬件实现，不需要用户编程实现**）即将内存地址映射到Cache当中去

1. 直接映射
2. 全相联映射
3. 组相联映射

### 直接映射

- 是一种多对一的映射关系，通过行号取余的方式将多个内存块映射到一个Cache，但一个主存块只能够复制到Cache的一个特定位置上去

- Cache的行号i和主存的块号j有函数关系：

  i = j % m（其中m为Cache总行数）

![image-20240331101430137](https://img.yatjay.top/md/image-20240331101430137.png)

### 全相联映射

将主存中任一主存块能映射到Cache中任意行（主存块的容量等于Cache行容量）。根据主存地址不能直接提取Cache页号，而是需要将主存块标记与Cache各页的标记逐个比较，直到找到标记符合的页（访问Cache命中），或者全部比较完后仍无符合的标记（访问Cache失败）。

![image-20240331101542002](https://img.yatjay.top/md/image-20240331101542002.png)

主存块标记与Cache各页的标记逐个比较，所以这种映射方式速度很慢，失掉了高速缓存的作用，这是全相联映射方式的最大缺点。如果让主页标记与各Cache标记同时比较，则成本太高。

### 组相联映射

是前两种方式的折中方案。它将Cache中的块再分成组，各组之间是直接映像，而组内各块之间则是全相联映像，主存地址=区号+组号+组内块号+块内地址号

![image-20240331101839249](https://img.yatjay.top/md/image-20240331101839249.png)

## (3)Cache淘汰算法(了解)

当Cache数据已满，并且出现未命中情况时，就要淘汰一些老的数据，更新一些新的数据进入Cache。选择淘汰哪些数据的方法就是淘汰算法。常见的方法有三种：

- 随机淘汰算法
- 先进先出淘汰算法（FIFO）
- 最近最少使用淘汰算法(LRU)
- 其中平均命中率最高的是LRU算法。

## (4)Cache存储器的写操作(了解)

在使用Cache时，需要保证其数据与主存一致，因此在写Cache时就要考虑**与主存间的同步**问题，通常使用以下三种方法：

- 写直达：当Cache写命中时，Cache与主存同时发生写修改。
- 写回：当CPU对Cache写命中时，只修改Cache的内容而不立即写入主存，当此行被换出才写回主存
- 标记法：
  - 数据进入Cache后，有效位  置为1；
  - 当CPU对该数据修改时，数据只写入主存并将该有效位  置为0。
  - 当要从Cache中读取数据时要测试其有效位，若为1则直接从Cache中取数，否则从主存中取数。


## 例题

![image-20240331102047082](https://img.yatjay.top/md/image-20240331102047082.png)

解析：主存的五大主要组成部件：

1. 地址寄存器

2. 数据寄存器

3. 存储体(最核心的部分)

4. 控制线路

5. 地址译码电路

![image-20240331102056369](https://img.yatjay.top/md/image-20240331102056369.png)

解析：Cache与内存之间的映射完全由硬件自动完成，不需要编程实现

![image-20240331102107702](https://img.yatjay.top/md/image-20240331102107702.png)

解析：按字编址32bit/word，可寻址范围即2G内存容量能够存放多少个word：2GB/32bit = 512M个word

![image-20240331102128822](https://img.yatjay.top/md/image-20240331102128822.png)

解析：相联存取按内容访问
