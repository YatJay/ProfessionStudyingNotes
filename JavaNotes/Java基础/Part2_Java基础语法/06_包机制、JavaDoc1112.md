# 包机制

为了更好地组织类，Java提供了包机制，用于区别类名的命名空间。包的本质就是文件夹。

包语句的语法格式为:

```
package pkg1[. pkg2[. pkg3...]];
```

**一般利用公司域名倒置作为包名：如：com.baidu.www **

为了能够使用某一个包的成员，我们需要在Java程序中明确导入该包。使用"import”语句可完成此功能

```java
import package1[.package2....].(classname|*);

import com.baidu.www.*  //导入这个包下所有的类
```

# JavaDoc

javadoc命令是用来生成自己API文档的

## 参数信息

- @author 作者名

- @version版本号

- @since指明需要最早使用的jdk版本

- @param参数名

- @return返回值情况

- @throws异常抛出情况

作业：使用IDEA生成自己的Javadoc