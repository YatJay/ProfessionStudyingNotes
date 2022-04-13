# NIO概述

## 程序读取数据模型

### JVM读取数据的模型

![image-20220329091012199](https://img.yatjay.top/md/202203290910419.png)

在JDK4中引入了NIO，可以最大限度满足Java程序的IO需求

java.nio包，定义了各种与Buffer相关的类

java.nio.channel包，包含与Channel、Selector相关的类

java.nio.charset包，与字符集相关的类

在NIO中you三大核心组件：Channel、Buffer、Selector

## NIO与传统IO的区别

传统的IO面向流的，每次可以从流中读取一个或多个字节，只能向后读取，不能向前移动

NlO是面向缓冲区的，把数据读到一价缓油区中，可以在缓冲区中向前/向后移动，增加了程序的灵活性

在NIO中，所有的数组都需要通过Channel传输，通道可以将一块数据映射到内存中，Channel是双向的，不仅可以读取数据，还能保存数据，程序不能直接读写Channel通道，Channel只与Buffer缓冲区交互

![image-20220329091823226](https://img.yatjay.top/md/202203290918271.png)

IO流是线程阻塞的，在调用read()/write()读写数据时,线程阻塞,直到数据读取完毕或者数据完全写入，在读写过程中,线程不能做其他_的任务;

NIO是不是线程阻塞的，当线程从Channel中读敢数据时，如果通道中没有可用的数据，线程不阻塞，可以做其他的任务

# Buffer

## Buffer的常用

Buffer缓冲区实际上就是一个数组，把数组的内容与信息包装成一个Buffer对象,它提供了一组访问这些信息的方法

### 缓冲区的重要属性

capacity容量,是指缓冲区可以存储多少个数据．容量在创建Buffer缓冲区时指定大小，创建后不能再修改.如果缓冲区满了，需要清空后才能继续写数据

position表示当前位置，即缓冲区写入/读取的位置.刚刚创建Buffer对象后，position初始化为0，写入一个数据，position就向后移动一个单元，它的最大值是capacity - 1。当Buffer从写模式切换到读模式，position会被重置为0。从Buffer的开始位置读取数据,每读一个数据postion，就向后移动一个单元

limit上限，是指第一个不能被读出或写入的位置。 limit上限后面的单元既不能读也不能写。在 Buffer缓冲区的写模式下，limit表示能够写入多少个数据；在读取模式下，limit表示最多可以读取多少个数据

mark标记，设置一个标记位置，可以调用mark()方法,把标记就设置在position位置，当调用reset()方法时，就把postion，设置为mark标记的位置

0<=mark <= position <= limit <= capacity

![image-20220329092528804](https://img.yatjay.top/md/202203290925862.png)

## Buffer的常用API

在NIO中关键的 Buffer有:ByteBuffer, CharBuffer,DoubleBuffer,FloatBuffer, IntBuffer,LongBuffer, ShortBuffer。这些Buffer覆盖了能够通过/o发送的所有基本类型: byte, char,double ,float, int, long, short等。实际上使用较多的是ByteBufer,CharBuffer

每个缓冲区类都有一午静态方法 allocate(capacity)可以创建一个指定容量的缓冲区

都有一个put()方法用子向缓冲区中存储数据，get()方法用于从缓冲区中读取数据

当缓冲区中还有未读完的数据，可以调用compact()方法进行压缩，将所有未读取的数据复制到 Buffer的起始位置，把position 设置到最后一个未读元素的后面。limit属性设置为capacity

capacity()方法返回缓冲区的大小

hasRemaining()判断当前position'后面是否还有可处理的数据，即判断postion.与limit之间是否还有数据可处理

limit()，返回limit上限的位置

mark(),设置缓冲区的标志位置，这个值只能在0~oosition之间。以后可以通过reset()返回到这个位置

position()返回position当前位置

remaining()返回当前position位置与limit之间的数据量

reset()方法可以将position设置为mark标志

rewind(),将position设置为o，取消mark标志位

clear()清空缓冲区，仅仅是修改position标志为o，设置limit 为capacity，缓冲区中数据还是存在的

flip()方法可以把缓冲区由写模式切换到读模式。先将limit 设置为position位置，再把position设置为0

# Channel







# Selector