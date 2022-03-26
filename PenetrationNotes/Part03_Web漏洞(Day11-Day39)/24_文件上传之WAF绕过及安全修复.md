**前言：**了解原理之后，尝试使用所学知识进行绕过waf。以及了解文件上传漏洞的安全修复

![](https://img.yatjay.top/md/202203261044419.png)

# 涉及知识

## 上传参数名解析——明确哪些东西能修改

![](https://img.yatjay.top/md/202203012014037.png)

| 参数名              | 含义                                              | 是否可更改   |
| ------------------- | ------------------------------------------------- | ------------ |
| Content-Disposition | 表单接收的类型如form-data                         | 一般可更改   |
| name                | 表单参数值，参数值由上传表单input标签的name值决定 | 不能更改     |
| **filename**        | **上传文件的文件名**                              | **可以更改** |
| Content-Type        | 文件MIME                                          | 可视情况更改 |

### 常见绕过方法——以安全狗为例

#### 数据溢出—防匹配(xxx…)

大量垃圾数据缓冲溢出(Content-Disposition,filename等)

上传文件时修改数据包，向其中填充大量垃圾数据，WAF在匹配黑白名单时就会受影响以至于匹配出错，因而导致非法的文件上传成功

填充垃圾数据

![](https://img.yatjay.top/md/202203012034117.png)

填充垃圾数据后，绕过WAF上传PHP脚本成功

![](https://img.yatjay.top/md/202203012034236.png)

#### 符号变异—防匹配(‘  “  ;)

观察数据包中的上述几项数据的写法

```http
Content-Disposition: form-data; name="upload_file"; filename="1.jpg"
Content-Type: image/jpeg
```

可见其中存在冒号，分号，双引号等标点符号。

引号：一般没有带双引号的是数据包自带的内容，带上双引号的是不固定的，是程序开发者指定的，一半表示的是一个字符串，若不括起来则可能是一个变量名。

分号：分号代表一个字段的截止，filename字段之后没有分号

基于此我们猜测安全狗检测文件后缀名的机制：安全狗只检查双引号内的参数值，看一对双引号内的后缀是否合法，基于此，我们尝试如下绕过方法：

| 绕过写法                                                     | 绕过示例             | 绕过原理                                                     | 绕过结果 |
| ------------------------------------------------------------ | -------------------- | ------------------------------------------------------------ | -------- |
| 不写引号                                                     | filename=x.php       | 安全狗只检查双引号内的参数值                                 | 可以绕过 |
| 改为单引号括起                                               | filename='x.php'     | 安全狗只检查双引号内的参数值                                 | 不能绕过 |
| 去掉一半双引号使之不能闭合                                   | filename="x.php      | 安全狗可能会误认为filename值为`"x.php `，找不到一对双引号内的参数值，从而也就匹配不到参数值中的后缀名 | 可以绕过 |
| 去掉一半双引号使之不能闭合                                   | filename=x.php"      | 可能安全狗是从最后一个双引号开始匹配，发现最末的两个单引号之间的参数值存在非法字符或后缀 | 不能绕过 |
| 上述猜测从最后一个双引号开始检查，可以构造一对双引号内的参数值 | filename="asdf"x.php | 安全狗只检查引号内的内容，上传文件名为`x.php                 | 可以绕过 |

#### 数据截断—防匹配(%00  ;  换行)

| 截断方法 | 截断示例                                                   | 截断原理                                                     | 绕过结果                               |
| -------- | ---------------------------------------------------------- | ------------------------------------------------------------ | -------------------------------------- |
| 分号截断 | filename=“a.jpg;.php”;                                     | （分号;和斜杠/可以绕过）分号表示一个语句的结束，安全狗匹配至此就不匹配后面内容，数据包处理还是将整体当做文件名 | 成功上传a.jpg;.php                     |
| 斜杠截断 | filename=“xxx/x.php”或filename=“/x.php”                    | 如上                                                         | 成功上传x.php                          |
| %00截断  | filename=“a.php%00.jpg”                                    | 类似绕过过滤代码时的%00截断                                  | 绕过失败，成功上传a.php%00.jpg（无用） |
| 换行截断 | filename= <br/>" <br/>x <br/>. <br/>p <br/>h <br/>p <br/>" | 安全狗的匹配代码中就变成了x\n.\np\nh\np，从而绕过安全狗匹配规则，数据包在后台处理时能够识别 | 上传成功文件名为x.php                  |

#### 重复数据—防匹配(参数多次)

安全狗的过滤代码有可能是单次过滤、多次过滤、循环过滤、递归过滤，因此可以把文件名这个参数多写几次，当过滤次数有限时，可能绕过过滤机制，如以下写法

- filename=“Content-Disposition: form-data; name=“upload_file”;x.php” -> 成功上传x.php（借助白名单） 

- filename=“x.jpg”;filename=“x.jpg”;…filename=“x.php”;

## 文件上传安全修复方案

1. 后端验证：采用服务端验证模式，避免只使用前端过滤方法

2. 后缀检测：基于黑名单，白名单过滤

3. MIME 检测：基于上传自带类型检测 

4. 内容检测：文件头，完整性检测 

5. 自带函数过滤：参考 uploadlabs 中的过滤函数 

6. 自定义函数过滤：function check_file(){} 

7. WAF 防护产品：宝塔，阿里云盾，安全公司产品等

# 演示案例

## WAF绕过简单演示

#### 0. 配置演示环境

以靶场upload-labs第二关进行演示。

开启安全狗的上传防护:

![img](https://img.yatjay.top/md/202203021058852.png)

第2关上传一个php文件,被安全狗检测到且被拦截:

![img](https://img.yatjay.top/md/202203021059388.png)

php加上空格"ph p",可上传，但无法解析

![img](https://img.yatjay.top/md/202203021059727.png)

#### 1. 数据溢出

- filename的内容进行溢出

![img](https://img.yatjay.top/md/202203021059768.png)

filename的内容进行溢出虽然可以绕过WAF但是无法将php文件上传至服务器

![img](https://img.yatjay.top/md/202203021059090.png)

- name与filename之间进行溢出

![img](https://img.yatjay.top/md/202203021059906.png)

可绕过WAF但是无法上传php文件

![img](https://img.yatjay.top/md/202203021059147.png)

- 大量垃圾数据后加“;”进行分隔

Content-Disposition与name之间的垃圾数据加上分号可绕过安全狗。

![img](https://img.yatjay.top/md/202203021100280.png)

可成功上传php文件

![img](https://img.yatjay.top/md/202203021100798.png)

name与filename之间的垃圾数据也可绕过。

![img](https://img.yatjay.top/md/202203021100272.png)

![img](https://img.yatjay.top/md/202203021100630.png)

#### 2. 符号变异

   可用payload:

```plain
filename=" xx.php
filename="x x.php
filename=' xx.php
filename='x x.php
```

- filename的内容用单引号括起来(被拦截)

![img](https://img.yatjay.top/md/202203021100387.png)

- filename去掉最后一个双引号(被拦截)

![img](https://img.yatjay.top/md/202203021100732.png)

- filename去掉最后一个双引号，再加上分号(上传成功，但后缀不能被解析)

![img](https://img.yatjay.top/md/202203021100147.png)

- filename去掉最后一个双引号，在文件名后缀前任意位置加空格(可绕过WAF，可上传，可行)

![img](https://img.yatjay.top/md/202203021100944.png)

- 单引号与双引号一致
- 只在末尾加双引号(被拦截)

![img](https://img.yatjay.top/md/202203021100574.png)

- 文件名前加双引号或单引号(无法解析)

文件名前加双引号或单引号可绕过waf，也可上传，但是无法解析。

![img](https://img.yatjay.top/md/202203021101652.png)

- 文件名中加入图片后缀提前用分号截断

#### 3. 数据截断

可用payload

```plain
filename="x.jpg;shell.php"
filename="x.jpg;shell.php
filename='x.jpg;shell.php
filename="shell.php%00xx.jpg" 注意%00要编码
```

- 文件名中加入图片后缀提前用分号截断(可行)

原因是防护软件只检测前面的部分，一旦正确就放行，不再检测后面的

前三个payload均可绕过

![img](https://img.yatjay.top/md/202203021101021.png)

- 文件名中用%00url编码截断(可行)

![img](https://img.yatjay.top/md/202203021101613.png)

#### 4. 数据换行截断

​     可用payload

```plain
x.ph
p

x.p
h
p

x.
p
h
p

x
.
p
h
p
```

- 直接在数据包中进行换行操作(可行)

![img](https://img.yatjay.top/md/202203021101860.png)



![img](https://img.yatjay.top/md/202203021101725.png)



![img](https://img.yatjay.top/md/202203021101036.png)

#### 5. 重复数据（参数多次）

- 重复filename

前面的filename为可接受的文件格式，最后一个为php文件格式，前面的重复多次，可绕过。

![img](https://img.yatjay.top/md/202203021101948.png)

- filename中配合其他参数

配合Content-Disposition

![img](https://img.yatjay.top/md/202203021101574.png)

配合Content-Type

![img](https://img.yatjay.top/md/202203021101061.png)

#### 6. 配合目录命名绕过

​     "/"与";"配合绕过

​    payload:

```plain
filename="/jpeg;x.php"
filename="/jpeg;/x.php"
```

![img](https://img.yatjay.top/md/202203021102305.png)



![img](https://img.yatjay.top/md/202203021102316.png)

#### 

#### 7. FUZZ字典配合

手工测试的话有点麻烦，可以借助写好的字典配合BP进行批量测试，先在本地测试，然后在真实环境进行测试，以防封IP。

```plain
https://github.com/TheKingOfDuck/fuzzDicts
https://github.com/fuzzdb-project/fuzzdb
```

借助fuzzDicts的php字典进行测试。

首先将拦截的数据包发送至Intruder

![img](https://img.yatjay.top/md/202203021102982.png)

清除所有变量

![img](https://img.yatjay.top/md/202203021102955.png)

将filename的值设置为变量

![img](https://img.yatjay.top/md/202203021102237.png)

payload加载字典

![img](https://img.yatjay.top/md/202203021102540.png)

开始攻击

![img](https://img.yatjay.top/md/202203021102640.png)

不一定能成功，成败与否的关键是字典的好坏

# 涉及资源

https://github.com/fuzzdb-project/fuzzdb

https://github.com/TheKingOfDuck/fuzzDicts

fuzz及爆破字典：https://github.com/Ivan52/fuzz_dict