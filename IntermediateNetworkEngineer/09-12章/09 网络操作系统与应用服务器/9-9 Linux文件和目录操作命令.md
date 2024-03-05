# 9-9 Linux文件和目录操作命令(13个文件或目录操作命令，常考)

## 文件读取命令

### cat

cat命令：用来在屏幕上滚动显示文件的内容，cat命令也可以同时查看多个文件的内容，还可以用来合并文件

命令格式：cat [-选项] fileName [filename2] ... [fileNameN]

### more

more命令：如果文本文件比较长，一屏显示不完，这时可以使用more命令将文件内容分屏显示

### less

less命令：less命令的功能与more命令很相似，也是按页显示文件，不同的是less命令在显示文件时允许用户**可以向前或向后翻阅文件**。

按B键向前翻页显示;

按P键向后翻页显示;

输入百分比显示指定位置;

按Q键退出显示

## 文件操作命令

### cp

cp命令：复制文件，相当于复制粘贴

命令格式：`cp[-选项] sourcefileName | directory destfileName | directory`

- `a`：整个目录复制。它保留链接、文件属性，并递归地复制子目录
- `-f`：删除已经存在的目标目录中的同名文件且不提示，即强制覆盖掉目标目录中的重名文件

### mv

mv命令：移动文件

### rm

rm命令：删除文件

命令格式：`rm[-选项]fileName |directory`

`-f`：force 强制删除，忽略不存在的文件，从不给出提示。

`-r`：指示rm将参数中列出的全部目录和子目录均递归地删除。

删库跑路：rm -rf /*

## 目录操作命令

### mkdir和rmdir

创建/删除目录命令`mkdir` `rmdir`

### cd

切换目录命令`cd`

### pwd

显示当前目录命令`pwd `

示例：`[root@redhat-64-]      # pwd显示的路径名为/home/sun`

### ls

列目录命令`ls`

### chmod

文件访问权限命令`chmod `

示例：`chmod g+rw test.txt     # 为同一个用户组的用户添加对test.txt的读写权限`

### ln

文件链接命令`ln` 。ln 命令的功能是在文件之间创建链接，相当于快捷方式。

## 例题

![image-20230311153753392](https://img.yatjay.top/md/image-20230311153753392.png)

![image-20230311153802939](https://img.yatjay.top/md/image-20230311153802939.png)

![image-20230311153821799](https://img.yatjay.top/md/image-20230311153821799.png)

# Linux用户和组管理

## Linux用户和组管理

Linux系统中最重要的是超级用户，即根用户root UID=0

用户管理配置文件（偶尔考)

1. **/etc/passwd**每个用户在该文件中都有一行对应记录，该文件对所有用户都是可读的

- 分为7个域，记录了这个用户的基本属性，格式如下∶
  - 用户名∶
  - 加密的口令∶
  - 用户ID:
  - 组ID:
  - 用户的全名或描述
  - 登录目录:
  - 登录shell

2. **/etc/shadow**只有**超级用户root能读**的文件/etc/shadow,该文件包含了系统中的所有用户及其口令等相关信息，分成9个域:

![image-20230311154327377](https://img.yatjay.top/md/image-20230311154327377.png)