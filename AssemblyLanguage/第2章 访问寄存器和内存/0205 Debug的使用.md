# 0205 Debug的使用

## Debug是什么

Debug是DOS系统中的著名的调试程序，也可以运行在windows系统实模式下。

使用Debug程序，可以查看CPU各种寄存器中的内容、内存的情况，并且在机器指令级跟踪程序的运行！

  ![image-20230903001217868](https://img.yatjay.top/md/image-20230903001217868.png)

## Debug能做什么

用R命令：查看、改变CPU寄存器的内容

用D命令：查看内存中的内容

用E命令：改变内存中的内容

用U命令：将内存中的机器指令翻译成汇编指令

用A命令：以汇编指令的格式在内存中写入机器指令

用T命令：执行机器指令  

## 启动Debug

注意启动Dosbox需要重新进行挂载

```shell
mount c: d:\xxx\masm
```

而后切换Dosbox的默认Z盘到C盘

```shell
c:
```

![image-20230903001906274](https://img.yatjay.top/md/image-20230903001906274.png)

## 用R命令：查看、改变CPU寄存器的内容

**R :查看寄存器内容，注意最后一行显示的是CS:IP的指令内容**

![image-20230903002110167](https://img.yatjay.top/md/image-20230903002110167.png)

**R 寄存器名：改变指定寄存器内容**

![image-20230903002212781](https://img.yatjay.top/md/image-20230903002212781.png)

注意

- 这里改变指定寄存器内容，默认为十六进制数
- r后面可以不跟空格子

## 用D命令：查看内存中的内容

**D ：列出预设地址内存处的128个字节的内容**

Dosbox预设地址内存为073F

![image-20230903003728054](https://img.yatjay.top/md/image-20230903003728054.png)

**D 段地址:偏移地址 ：列出内存中指定地址处的内容**

![image-20230903003902886](https://img.yatjay.top/md/image-20230903003902886.png)

**D 段地址:偏移地址 结尾偏移地址 ：列出内存中指定地址范围内的内容**  

如限制只显示前16位

![image-20230903003958413](https://img.yatjay.top/md/image-20230903003958413.png)

## 用E命令：改变内存中的内容

**E 段地址:偏移地址 数据1 数据2 ...：直接修改内存中内容**

![image-20230903004253988](https://img.yatjay.top/md/image-20230903004253988.png)

**E 段地址:偏移地址：逐个询问式修改**

- 空格 表示 接受，并继续修改

- 回车 表示 结束修改  

![image-20230903004444026](https://img.yatjay.top/md/image-20230903004444026.png)

## 用U命令：将内存中的机器指令翻译成汇编指令

二进制数据在内存中可以看作数据，也可看作指令

已知

![image-20230903004849166](https://img.yatjay.top/md/image-20230903004849166.png)

我们尝试将这些机器码写入一段内存当中，再使用u命令将这些数据读取为汇编指令

- **e命令将数据写入指定内存**

![image-20230903005208226](https://img.yatjay.top/md/image-20230903005208226.png)

- **d命令查看是否正确写入**

![image-20230903005222042](https://img.yatjay.top/md/image-20230903005222042.png)

- **u 地址：将内存中的数据读取为汇编指令**

![image-20230903005804649](https://img.yatjay.top/md/image-20230903005804649.png)

## 用A命令：以汇编指令的格式在内存中写入机器指令

已知

![image-20230903010024868](https://img.yatjay.top/md/image-20230903010024868.png)

我们尝试将这些汇编代码写入一段内存当中，再使用d命令将查看这些二进制数据，再使用u命令将这些二进制数据读取为汇编代码

- 实际工作中，汇编指令写到段地址为CS，偏移地址为IP的内存中，Dosbox中上述寄存器内存放地址如下图所示

![image-20230903010439891](https://img.yatjay.top/md/image-20230903010439891.png)

- **a 地址：写入汇编指令**

![image-20230903010635756](https://img.yatjay.top/md/image-20230903010635756.png)

- **d 地址：查看内存中数据**

![image-20230903010846965](https://img.yatjay.top/md/image-20230903010846965.png)

- **u 地址：将二进制数据读取为汇编代码**

![image-20230903010923464](https://img.yatjay.top/md/image-20230903010923464.png)

## 用T命令：执行机器指令

**t 执行CS:IP处的指令**，IP偏移地址会自动跳转至下一条指令

```assembly
mov ax, 0123H
mov bx, 0003H
mov ax, bx
add ax, bx
```

**t 执行CS:IP处的指令**，IP偏移地址会自动跳转至下一条指令，逐条执行

![image-20230903011526100](https://img.yatjay.top/md/image-20230903011526100.png)

## 用Q命令：退出Debug

![image-20230903011651601](https://img.yatjay.top/md/image-20230903011651601.png)