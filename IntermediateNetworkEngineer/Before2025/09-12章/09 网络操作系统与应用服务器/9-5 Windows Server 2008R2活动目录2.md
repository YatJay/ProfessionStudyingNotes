# 9-4 活动目录的安装配置

回顾：活动目录的安装

活动目录必须安装在**NTFS分区**，同时**必须安装DNS服务器**（而安装DNS服务又有一个限制，就是必须配置静态IP才可以安装DNS服务）。

- 命令安装∶开始→运行→dcpromo.exe命令，启动安装向导
- 图形化安装︰管理服务器→添加服务器角色

[AD域（active directory）基础概念](https://blog.csdn.net/qq_45502533/article/details/110534470)

[AD域的详细介绍](https://blog.csdn.net/fuhanghang/article/details/126497018?utm_medium=distribute.pc_relevant.none-task-blog-2~default~baidujs_baidulandingword~default-1-126497018-blog-110534470.235^v43^pc_blog_bottom_relevance_base9&spm=1001.2101.3001.4242.2&utm_relevant_index=2)

本节课程为实验实操，为一个普通服务器配置活动目录，并将一个用户加入域