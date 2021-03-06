# 原理

网站Web应用都有一些文件上传功能，比如文档、图片、头像、视频上传，当上传功能的实现代码没有严格校验上传文件的后缀和文件类型，此时攻击者就可以上传一个webshell到一个Web可访问的目录上，并将恶意文件传递给如PHP解释器去执行，之后就可以在服务器上执行恶意代码，进行数据库执行、服务器文件管理，服务器命令执行等恶意操作。还有一部分是攻击者通过Web服务器的解析漏洞来突破Web应用程序的防护

# 产生条件



# 危害

文件可以自定义，可以成为webshell，通过文件上传来上传网站后门，直接获取网站权限，属于高危漏洞。上传漏洞与SQL注入或 XSS相比 , 其风险更大 。可以获取数据库信息，可以对服务器提权，获取内网权限

# 分类



# 检测

## 检测方法

### 黑盒查找

黑盒查找即对方网站情况事先不知道，需要目录、文件扫描获取

#### 网站后台

进入网站后台不一定获得网站权限，可以从后台获取网站权限(后台权限不等于网站权限)

百度——后台拿webshell即从后台权限拿到网站webshell

#### 会员中心

通过图片上传

#### 文件扫描 

使用工具扫描出后台路径

### 白盒查找 

白盒查找即事先知道网站源码，进行审计

通过代码分析到上传漏洞，查找文件上传功能



## 检测位置

网站后台

会员中心

文件扫描

# 利用

## 利用目的



## 利用思路——区分文件上传类型

![img](https://img.yatjay.top/md/202203251657222.png)

1. 是什么文件格式，服务器就会按照什么文件格式解析，比如.jpg中插入.php脚本代码，执行jpg图片时无法触发脚本代码；
2. 有解析漏洞时，就能实现任意文件格式的解析，比如jpg中插入php脚本代码，若存在文件解析漏洞，执行jpg图片时可以触发脚本代码；

## 利用方法(绕过方法)

### 常见验证

- 后缀名验证
- 文件类型验证：MIME信息即数据包中的Content-Type信息
- 文件头验证：内容头信息，文件头是直接位于文件中的一段数据,是文件的一部分

### 前端验证绕过

如果是前端代码对上传文件后缀名进行过滤，有以下2种方法绕过

第一个思路：处理掉前端验证代码

1. 前端删除这段代码即可，推荐此方法
2. 使用插件禁用前端js脚本的执行，不推荐，有可能禁用掉其他有用的js脚本

第二个思路：利用burp抓包进行修改,将后缀直接改成php即可实现上传脚本文件操作

**注意：**burp在有些情况下是抓不到包，这里抓的到是因为代码中提交的部分。比如使用js代码，它是在本地运行，不会将数据发送到服务器上。

### 后端验证绕过

#### 黑名单绕过

- MIMIE类型验证绕过：抓包后修改Content-Type值为合法类型
- 特殊解析后缀绕过：.php5等后缀解析成php从而绕过黑名单
- `.htaccess`解析绕过：黑名单没有过滤`.htaccess`文件，先上传一个`.htaccess`文件，接下来文件名中含有`cimer`的文件都会被当做php文件来解析，比如上传`cimer.jpg`文件，访问该jpg文件，会被当做PHP文件来解析
- 大小写绕过：黑名单中的文件名后缀做了扩充，但是却在提取文件后缀名后**没有进行强制小写转换**，此时可以通过文件名后缀大小写的变化绕过黑名单
- 空格绕过：对上传文件的后缀名**没有进行首尾去空格**的操作，因此可以上传一个`.php +`的文件(+表示前面是一个空格，实际不写)。需要注意，Windows中如果文件名最后存在空格，系统会默认删除，因此我们上传时先使用burp抓包，在文件名后添加空格，这样才能成功绕过。
- 点绕过：对文件名后缀处理时，**缺少删除文件末尾的点的操作**，因此在上传一个文件名末尾是`·`的文件就能绕过黑名单。**注意**：Windows中如果文件名最后存在`.`，系统不能创建，因此我们上传时先使用burp抓包，在文件名后添加`.`，这样才能成功绕过。上传到Windows服务器保存后，又会自动删除文件名最后的`.`，Linux服务器会保留`.`。
- ::$DATA绕过：**在window的时候如果文件名+"::$DATA"会把::$DATA之后的数据当成文件流处理,不会检测后缀名，且保持::$DATA之前的文件名，他的目的就是不检查后缀名，例如:上传时的文件名为"phpinfo.php::$DATA"，Windows会自动去掉末尾的::$DATA变成"phpinfo.php"**。注意：Windows中文件名最后不允许特殊符号存在，系统不能创建，因此我们上传时先使用burp抓包，在文件名后添加`::$DATA`，这样才能成功绕过。
- 点空格绕过：
- 双后缀名绕过：代码中**只使用了一次替换为空而不是循环替换，即只将首次出现且在黑名单中的文件名后缀形式替换为空**，因此可以使用双写后缀名进行绕过

#### 白名单绕过

- %00截断绕过：即在url中%00表示ascll码中的0 ，而ascii中0作为特殊字符保留，表示字符串结束，所以**当url中出现%00时就会认为读取已结束**。注意：%00截断的条件1. %00需要PHP版本小于5.3.4     2. 打开php的配置文件php-ini，将magic_quotes_gpc设置为Off

#### 内容逻辑数组绕过

略

### WAF绕过方法

#### 垃圾数据溢出——影响WAF匹配

上传文件时修改数据包，向其中填充大量垃圾数据，WAF在匹配黑白名单时就会受影响以至于匹配出错，因而导致非法的文件上传成功

1. filename的内容进行溢出
2. name(表单名)与filename(文件名)之间进行溢出
3. 大量垃圾数据后加“;”进行分隔：Content-Disposition与name之间的垃圾数据加上分号可绕过安全狗。
4. name与filename之间的垃圾数据也可绕过。

#### 符号变异(‘  “  ;)——防匹配

我们猜测安全狗检测文件后缀名的机制：安全狗只检查双引号内的参数值，看一对双引号内的后缀是否合法，基于此，我们尝试如下绕过方法：

1. 不写引号：`filename=x.php`
2. 改为单引号括起：`filename='x.php'`
3. 去掉一半双引号使之不闭合：`filename="x.php`
4. 去掉一半双引号使之不闭合：`filename=x.php"`
5. 构造一对双引号内的参数值，令其合法：`filename="asdf"x.php`

#### 数据截断(%00  ;  换行)——防匹配

WAF的过滤代码也是使用编程语言来编写，一些通用的代码中的截断符号(比如%00、;、换行)能够使得匹配语句被截断，从而影响匹配结果

1. 分号截断：filename=“a.jpg;.php”;
2. 斜杠截断：filename=“xxx/x.php”或filename=“/x.php”
3. %00截断：filename=“a.php%00.jpg”
4. 换行截断：filename= <br/>" <br/>x <br/>. <br/>p <br/>h <br/>p <br/>"

#### 重复数据(参数多次)——防匹配

安全狗的过滤代码有可能是单次过滤、多次过滤、循环过滤、递归过滤，因此可以把文件名这个参数多写几次，当过滤次数有限时，可能绕过过滤机制，如以下写法

- filename=“Content-Disposition: form-data; name=“upload_file”;x.php” -> 成功上传x.php（借助白名单） 
- filename=“x.jpg”;filename=“x.jpg”;…filename=“x.php”;

### 配合Fuzz字典

手工测试的话有点麻烦，可以借助写好的字典配合BP进行批量测试，先在本地测试，然后在真实环境进行测试，以防封IP。

不一定能成功，成败与否的关键是字典的好坏

# 修复与防御方案

1. 后端验证：采用服务端验证模式，避免只使用前端过滤方法

2. 后缀检测：基于黑名单，白名单过滤

3. MIME 检测：基于上传自带类型检测 

4. 内容检测：文件头，完整性检测 

5. 自带函数过滤：参考 uploadlabs 中的过滤函数 

6. 自定义函数过滤：function check_file(){} 

7. WAF 防护产品：宝塔，阿里云盾，安全公司产品等