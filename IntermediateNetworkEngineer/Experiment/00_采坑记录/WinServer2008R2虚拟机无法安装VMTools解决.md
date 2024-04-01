# Windows 2008R2 安装VMware tools失败问题解决

来源：https://blog.csdn.net/Dream_chang/article/details/124457192

环境：Windows 2008 R2虚拟机

点击winter键，打开控制面板

![img](https://img.yatjay.top/md/fa42215a018b4beaaee63997f0df91ba.png)

打开检查防火墙状态

![img](https://img.yatjay.top/md/b3f67291649b49b18375ab0c2470c8d7.png)

点击高级设置

![img](https://img.yatjay.top/md/10dcffd6337047b185d60fc4523a3cc1.png)

点击Windows防火墙属性



![img](https://img.yatjay.top/md/4d629f99e9ca4fb5951356803619f2dc.png)

按下图所示，将1，2，3，所示的防火墙状态关闭，然后点击4确定即可

![img](https://img.yatjay.top/md/085316f96fdf4987af286d2a365715a6.png) 

关闭所有打开的页面

点击桌面的Windows服务器管理器

![img](https://img.yatjay.top/md/b2243b3ef9454804abf30141b373ca14.png)

找到配置IE ESC，点进去

![img](https://img.yatjay.top/md/e921cdf3eef241eabaadc4e3418681b5.png) 

根据下图1，2将其关闭，然后点击确定

![img](https://img.yatjay.top/md/86a7c791433e4c2b9aeba149a9ec5dca.png)

 这样基础的下载是拦截的防火墙就设置完毕了

接下来需要下载火狐，来下载需要的Windows更新包

打开[IE浏览器](https://so.csdn.net/so/search?q=IE浏览器&spm=1001.2101.3001.7020)

![img](https://img.yatjay.top/md/dc453ac290d54eadb0877c88448d35a4.png)

出现系此页面点击否即可

![img](https://img.yatjay.top/md/fa02e3b17b2f4fbe9159dd03fc576421.png)

 在此输入火狐，然后回车

![img](https://img.yatjay.top/md/2b13649effc94b329dc2537b18ae95cd.png)

点击官方下载这个

![img](https://img.yatjay.top/md/9ae5a9774e514d48a7863929ddfd8fb6.png)

 点击保存![img](https://img.yatjay.top/md/8b26f3f95ea8494fab679d4f1d9b7d2c.png)

 在你的文件下会生成一个如下图所示的文件

![img](https://img.yatjay.top/md/46c87a1acd624caf9c66628c6635be17.png)

点击安装运行

![img](https://img.yatjay.top/md/cd592bff8de84fb69a4282c57187ab81.png)

会弹出以下一个错误提示，点击确定，会返回火狐的下载界面；

![img](https://img.yatjay.top/md/c0c33804c15143519494e6e0d657a1d7.png)

这是会有一个文件下载的警告，显示大小为51.2MB，点击保存

![img](https://img.yatjay.top/md/6fa7306a862741fc933a9f6c95e77075.png)

保存完成后，查看你的文件夹，如下图所示，点击full-latest这个安装

![img](https://img.yatjay.top/md/46a03391d2d04da698c10b6c8980dfd4.png)

这时火狐就安装完成了，桌面会有一个火狐的图表，如下所示

![img](https://img.yatjay.top/md/2ba1688fad7f44709ce47a33498340ce.png) 

在这一栏输入网址[(https://www.catalog.update.microsoft.com/search.aspx?q=kb4474419）](https://www.catalog.update.microsoft.com/search.aspx?q=kb4474419)

![img](https://img.yatjay.top/md/f0c66fe0ba6e4c06ad298fdf86614cee.png)

 弹出以下界面，找到以下箭头所示的文件，点击Download进行下载

![img](https://img.yatjay.top/md/3eb33bbe5daa436d872ef23dfd9d83e1.png)

![img](https://img.yatjay.top/md/0e52317f7bf245a08fa27332ab6d65a1.png)

按箭头所示方向点击下载

![img](https://img.yatjay.top/md/3225a005910c45adbb47c22255519d91.png)

 在浏览器会看到下载的进度

![img](https://img.yatjay.top/md/7618521603914201a2e2792b0e790e96.png)

 下载完成后，找到所在的文件夹，点击安装

![img](https://img.yatjay.top/md/33546a2ebf774ecd953e6c2f0250820c.png)

 点是

![img](https://img.yatjay.top/md/15b236f18ab14d6c9c9d1469858b3593.png)

安装完成后会有立即启动，点击即可

![img](https://img.yatjay.top/md/2bda2a39a33945e0b91a9e3a75714f31.png)

然后在虚拟机的这一栏，点击安装VMware Tools，会有大概10s的等待时间

![img](https://img.yatjay.top/md/9b2202badf5440469f78f2db88fcb2c0.png)

点击箭头所示的进行安装

![img](https://img.yatjay.top/md/f6b0755ead1d47c99409c975ca81f189.png)

下一步

![img](https://img.yatjay.top/md/3b3d095de45549a2a3b7f1399cd00cc2.png)

典型安装

![img](https://img.yatjay.top/md/9cab20f5066e458cbcb072696f91ca82.png)

 点击安装

![img](https://img.yatjay.top/md/afd918ed69fe45efbd45cd4b7f1b731d.png)

![img](https://img.yatjay.top/md/3b92d2ffae4a4d73959cff27c513e4d2.png)

接下来就可以正常使用虚拟机和本机的文件共享了；如果需要在本机和虚拟机上建立共享文件夹，可以私信我；

这个是我在做实验的过程中遇到问题后解决的，在我的主机上有效，我朋友的几台机子试了也有效。

