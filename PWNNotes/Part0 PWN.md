# Part0 PWN？

## 概述

### PWN是什么

PWN的核心在于二进制漏洞的利用和挖掘

- 破解、利用成功（程序的二进制漏洞)

- 攻破（设备、服务器)

- 控制（设备、服务器)

### exploit、payload、shellcode

- exploit：用于攻击的脚本与方案

- payload：攻击载荷，是的目标进程被劫持控制流的数据

- shellcode：调用攻击目标的shell的代码

  - shell是一个提供用户与操作系统进行交互的命令行接口

  - 用户和操作系统的交互接口

    - Windows我们常用GUI图形化接口
    - Linux常用shell接口

  - 一个Terminal是一个终端，一个终端可以运行多个shell

    [**【shell】常用的几种shell解释器：sh,bash,zsh,ash,csh**](https://blog.51cto.com/u_15614325/5272825)

    pwn的目的是远程服务器

### PWN和web漏洞的区别

PWN的漏洞来源于编译成机器码的二进制程序当中的漏洞

web的漏洞来源于语言本身的设计缺陷，如PHP

### 什么是二进制程序

即可执行文件

Linux 下标准的可执行文件格式是**ELF**，等效于Windows下的exe文件

## 一次简单的PWN-hack

CTF的PWN的常规题目：

比赛方在服务器的某个端口挂上一个二进制的服务，对应一个二进制程序。

参赛者能够拿到相同的该二进制程序，能够本地运行

访问远程服务器端口可以发现和本地运行该程序一模一样

组委会会给参赛者相同的二进制程序，参赛者针对该二进制程序寻找漏洞，找出漏洞之后，利用该漏洞对远程目标服务器发起攻击拿到shell，进而控制服务器，以及拿到该服务器上的flag文件

### PWN题目解答一般步骤

Linux的file工具（命令）查看二进制文件格式，可以看到该文件为32位的Linux可执行文件

![image-20230914204927452](https://img.yatjay.top/md/image-20230914204927452.png)

使用IDA分析该文件的二进制反汇编代码，IDA还支持反汇编成为C语言源代码

![image-20230914205459968](https://img.yatjay.top/md/image-20230914205459968.png)

漏洞挖掘：在C语言代码中寻找漏洞：内存泄漏、栈溢出等

![image-20230914210019278](https://img.yatjay.top/md/image-20230914210019278.png)

编写漏洞利用程序：

- 借用Python的pwn模块；
- 构造恶意数据payload并发送；
- 使用Python的交互函数与服务器进行交互并寻找flag

![image-20230914205908354](https://img.yatjay.top/md/image-20230914205908354.png)

![image-20230914205841483](https://img.yatjay.top/md/image-20230914205841483.png)

