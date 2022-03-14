## 找开源项目的一些途径

### 中文

- https://github.com/trending/

- https://github.com/521xueweihan/HelloGitHub

- https://github.com/ruanyf/weekly

- https://www.zhihu.com/column/mm-fe

### 英文

- https://github.com/explore

- https://changelog.com/weekly/archive

## 特殊的查找资源小技巧
常用前缀后缀 
- 找百科大全 awesome xxx

- 找例子 xxx sample

- 找空项目架子 xxx starter / xxx boilerplate 

- 找教程  xxx tutorial

## Git的三个核心概念
### 提交commit

git是一种版本控制软件，可以把一个人乃至多个人代码的变更以提交的形式做一个存储，代码写崩的时候可以找回历史记录

 ### 仓库repository

项目的源文件夹是一个本地仓库，也可以将项目提交到云仓库github、码云

### 分支branch

多人同时在开发同一个项目，可以开辟不同的分支，各写各的，最后完成合并

## git常用命令

### 基本命令

- 克隆仓库：git clone git地址

- 初始化仓库：git init 

- 添加文件到暂存区：git add -A 

- 把暂存区的文件提交到仓库：git commit -m 

### 提交历史、工作区回滚、撤销提交

- 查看提交的历史记录：git log --stat 
- 工作区回滚：git checkout filename
- 撤销最后一次提交：git reset HEAD^1 

### 分支

- 以当前分支为基础新建分支：git checkout -b branchname 
- 列举所有的分支：git branch 
- 单纯地切换到某个分支：git checkout branchname 
- 删掉特定的分支：git branch -D branchname 
- 合并分支：git merge branchname 
- 放弃合并：git merge --abort 

### 远程仓库：

- 添加远程仓库：git remote add origin 地址
- 本地仓库改名：git branch -M main
- 上传代码：git push -u origin main
- 推送当前分支最新的提交到远程：git push 
- 拉取远程分支最新的提交到本地：git pull	