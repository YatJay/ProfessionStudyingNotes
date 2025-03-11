## 7-5 UOS Linux用户和组管理

### Linux用户和组管理

Linux系统中最重要的是<font color="red">超级用户</font>，即根用户**root，UID=0**(实际上，Linux是以UID来标记是否为根用户的，只要UID=0，不论用户名为什么，都是超级用户。在网络安全防护中，可以将root用户的UID改为非0，则即使黑客拿到root账号，仍然无法执行高权限操作)

用户管理配置文件

1. `/etc/passwd`：每个用户在该文件中都有一行对应记录，<font color="red">所有用户都可读</font>

   - 分为7个域，记录了这个用户的基本属性，格式如下：

   ```bash
   用户名：加密的口令：用户ID：组ID：用户的全名或描述：登录目录：登录shell
   ```
   (由于此文件所有用户都可读，**现在的**Linux系统一般在passwd文件中不存放加密的用户口令，而是仅仅存放于shadow文件中，shadow只有root能读)

2. `/etc/shadow`：<font color="red">只有超级用户root能读</font>，该文件包含了系统中的所有用户及其口令等相关信息，分成9个域：
   
   1. 用户登录名;
   2. **用户加密后的口令**（**若为空，表示该用户不需口令即可登录；若为*号，表示该账号被禁止**）：
   3. 从1970 年1月1日至口令最近一次被修改的天数;
   4. 口令在多少天内不能被用户修改;
   5. 口令在多少天后必须被修改;
   6. 口令过期多少天后用户账号被禁止;
   7. 口令在到期多少天内给用户发出警告;
   8. 口令自1970年1月1日起被禁止的天数；
   9. 保留

### Linux用户和用户组管理

#### useradd

命令格式：<font color="red">useradd [-选项] username</font>

- `-d`：指定用于取代默认/home/username的用户主目录，如`useradd -d /home/test user1`
- `-g`：用户所属用户组的组名或组ID，例：`adduser -g test user1`
- `-s`：指定用户登录shell，默认为`/bin/bash`
- `-u` ：即uid，指定用户的UID，它必须是唯一的，且大于499

#### passwd

passwd用来操作用户的密码

命令格式：passwd [-选项] username 

- `-l`：锁定口令，即**禁用账号**
- `-u`：口令解锁
- `-d`：使账号无口令
- `-f`：强迫用户下次登录时修改口令

#### userdel

<font color="red">userdel</font>最常用的选项参数是`-r`，它的作用是<font color="red">把用户和用户的主目录一起删除</font>

命令`userdel-r test`效果：

1. 删除`/etc/passwd`文件中此用户的记录
2. 删除`/etc/group`文件中该用户的信息
3. 删除用户的主目录
4. 删除用户所创建的或属于此用户的文件

#### groupadd groupdel

```bash
[root@uos～]#groupdel group1  #运行后将从系统中删除组group1
```

### 例题

#### 例题1

![image-20250311191239596](https://img.yatjay.top/md/20250311191239636.png)



#### 例题2

![image-20250311191247303](https://img.yatjay.top/md/20250311191247336.png)

