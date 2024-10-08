对比nslookup命令几个参数的含义，并以表格方式呈现

下面是`nslookup`命令几个常用参数的含义，以及以表格形式的展示：

| 参数                  | 含义                                                         |
| --------------------- | ------------------------------------------------------------ |
| `-querytype` 或 `-qt` | 指定查询的DNS记录类型，例如A（地址记录）、MX（邮件交换记录）、NS（名称服务器记录）等。 |
| `-debug`              | 启用调试模式，提供更详细的DNS查询过程信息，包括DNS服务器的交互细节。 |
| `-timeout`            | 设置查询超时时间，单位通常是秒，用于控制DNS查询等待响应的时间限制。 |
| `-server`             | 指定要查询的DNS服务器地址，如果不指定，则使用系统默认的DNS服务器。 |
| `-help` 或 `/?`       | 显示命令的帮助信息，列出所有可用的参数及其简要说明。         |

### 参数使用示例

- **查询A记录**:
  ```shell
  nslookup -querytype=A example.com
  ```

- **查询MX记录**:
  ```shell
  nslookup -querytype=MX example.com
  ```

- **启用调试模式**:
  ```shell
  nslookup -debug google.com
  ```

- **指定DNS服务器查询**:
  ```shell
  nslookup example.com 8.8.8.8
  ```

- **查看帮助信息**:
  ```shell
  nslookup -help
  ```

请注意，不同的操作系统或`nslookup`版本可能支持不同的参数和语法，上述表格和示例基于通用的`nslookup`命令用法。



介绍nslookup的qt参数值的含义，并以表格方式呈现

`nslookup`命令的`-qt`（或`-querytype`）参数用于指定DNS查询的记录类型。下面是`nslookup`中一些常见`qt`参数值及其含义的表格展示：

| 参数值 | 含义                                                         |
| ------ | ------------------------------------------------------------ |
| A      | 地址记录，查询域名对应的IPv4地址。                           |
| AAAA   | 地址记录，查询域名对应的IPv6地址。                           |
| MX     | 邮件交换记录，查询域名的邮件服务器信息及其优先级。           |
| NS     | 名称服务器记录，查询负责解析该域名的DNS服务器地址。          |
| CNAME  | 别名记录，查询域名的规范名称（canonical name），即别名指向的真实域名。 |
| PTR    | 反向记录，从IP地址解析对应的域名，主要用于IPv4（PTR记录）或IPv6（AAAA-PTR）。 |
| SOA    | 起始授权机构记录，查询域的DNS管理信息，包括主要和辅助DNS服务器等。 |
| TXT    | 文本记录，查询与域名关联的任意文本信息，常用于SPF记录、DKIM验证等。 |
| SRV    | 服务记录，查询特定服务的TCP或UDP服务的优先级、权重、端口号和目标主机。 |

这个表格总结了`nslookup`命令中最为常用的查询类型参数，每个参数值对应DNS系统中一种特定类型的资源记录，有助于在不同场景下获取所需的DNS信息。