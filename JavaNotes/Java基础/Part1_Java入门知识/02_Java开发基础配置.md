# JDK下载与安装

## 卸载JDK

1. 删除Java的安装目录

2. 删除JAVA_HOME

3. 删除path下关于Java的目录

4. java -version

## 安装JDK

1. 百度搜索JDK8，找到下载地址

2. 同意协议

3. 下载电脑对应的版本

4. 双击安装]JDK

5. 记住安装的路径

6. 配置环境变量(自行百度)
   1. 我的电脑-->右键-->属性.
   2. 环境变量-->JAVA_HOME
7. 测试JDK是否安装成功
   1. 打开cmd
   2. `java -version` 查看版本

# JDK目录介绍

| 目录    | 含义                                        |
| ------- | ------------------------------------------- |
| bin     | 可执行文件                                  |
| include | Java底层是C语言，引入一些C语言头文件        |
| jre     | Java运行时环境                              |
| lib     | Java类库，其下的src.zip存放一些Java资源文件 |

# HelloWorld及简单语法规则

1. 随便新建一个文件夹，存放代码

2. 新建一个Java文件

   1. 文件后缀名为.java
   2. 新建Hello.java 

3. 编写代码

   ```java
   public class Hello{ //定义一个类，类名为Hello
       public static void main(String[] args){  //main方法——程序的主方法
           System.out.print("Hello,World!");
       }
   }
   ```

4. 编译.java文件：命令`javac Hello.java`，会生成一个class文件
5. 运行.class文件：命令`java Hello`

![](https://gitee.com/YatJay/image/raw/master/img/202202061522778.png)

## 注意事项：

1. 每个单词的大小不能出现问题，Java是大小写敏感的

2. 尽量使用英文避免输出乱码;

3. 文件名和类名必须保证一致，并且首字母大写

# 编译型和解释型

Java既有编译型特点，又有解释型特点

![](https://gitee.com/YatJay/image/raw/master/img/202202061531528.png)

# 使用IDEA开发 

略
