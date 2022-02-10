# scanner对象

之前我们学的基本语法中我们并没有实现程序和人的交互，但是Java给我们提供了这样一个工具类，我们可以获取用户的输入。

java.util.Scanner是Java5的新特征，我们可以通过Scanner类来获取用户的输入 下-

## 基本语法:

```java
Scanner s = new Scanner(System.in);
```

通过Scanner类的next()与nextLine()方法获取输入的字符串，在读取前我们一般需要使用hasNext() 与hasNextLine()判断是否还有输入的数据。

next():

1. 一定要读取到有效字符后才可以结束输入。
2. 对输入有效字符之前遇到的空白,next()方法会自动将其去掉。
3. 只有输入有效字符后才将其**后面输入的空白作为分隔符或者结束符**。
4. **next()不能得到带有空格的字符串。**

nextLine():

1. 以Enter为结束符,也就是说nextLine()方法返回的是**输入回车之前**的所有字符。
2. 可以获得空白。