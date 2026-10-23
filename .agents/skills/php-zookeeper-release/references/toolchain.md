# 发版验证工具链

仅在工具缺失、版本混用或使用非系统前缀时阅读本页。环境选择取决于当次可用条件，不固定主机、PHP/ZooKeeper 版本或镜像。

## 优先顺序

1. 已有且版本一致的本机构建工具。
2. 已获授权、可访问的容器或远端构建环境；先查权限和工具版本。远端打包时核对传入源码 SHA，并取回同一个通过验证的 tgz。
3. 项目内隔离工具链：从可信软件仓库下载软件包，解包到 `reports/release-<版本>/toolchain/root/` 使用；无需为了发版自动安装系统软件。

旧知识库中的 SSH 地址、Docker socket、prefix 或路径只作为线索；使用前重新检查。网络、目录或 socket 被权限阻止时，走正常提权流程，不绕过限制。

## Ubuntu 软件包方式

- 从配置的仓库确认发行版、源 URL/镜像与候选版本，使用 `apt-cache policy`、`apt-get -s --no-install-recommends install ...` 查依赖。
- 使用 `apt-get download` 将所需软件包下载到项目隔离目录，再用 `dpkg-deb -x` 解压。模拟 install 只显示相对系统状态缺少的依赖；在隔离环境中还需检查“系统已安装”但实际不可访问的组件。
- PHP 通常需要同一版本的 CLI、dev、common、XML 模块和 php-pear；编译工具包括 GCC、make、autoconf、libtool、pkg-config、shtool 及实际依赖。按所选发行版确定包名，不把 PHP 版本写死。
- libzookeeper 通常需要配套的 `libzookeeper-mt-dev` 与 `libzookeeper-mt2`。记录源地址、suite/component、具体包版本、下载文件校验和、解压前缀。镜像地址与上游软件包身份分别记录。
- 通过 `php --ri`、`ldd` 或等价方法确认最终模块加载的是预期库；仅设置 configure 路径不足以证明运行时没有加载另一份库。

## 非系统前缀的实际问题

| 现象 | 核验与处理 |
| --- | --- |
| PHP 找不到共享库 | `ldd` 查缺失库，为该构建设置 `LD_LIBRARY_PATH` |
| phpize 找不到 headers/build/shtool/libtool | 检查脚本内绝对 `/usr` 路径，仅调整隔离副本的 prefix 和辅助文件路径 |
| autoconf 找不到 Perl 模块、宏或 trailer.m4 | 检查 `autom4te_perllibdir`、`AC_MACRODIR`、`AUTOM4TE`、`M4`、`trailer_m4` 是否指向隔离工具链 |
| GCC 显示版本正常但不能执行 cc1 | 查看 config.log 和 `gcc -print-prog-name=cc1`；下载匹配版本的 compiler frontend（Ubuntu 常由 `cpp-<主版本>-x86-64-linux-gnu` 提供） |
| PEAR 找不到 PHP 或 PEAR.php/XML | 检查 `PHP_PEAR_PHP_BIN`、`PHP_PEAR_INSTALL_DIR`、隔离 php.ini 和 XML 扩展 |
| configure 编译测试失败 | 从解包目录的 config.log 定位；先证明是工具链问题，再调整环境，不能盲目更改项目源码 |

在报告目录保存可复用的 `env.sh` 与配置；环境变量均使用工具预期含义，任务自定义变量使用 `release_` 前缀，不改写 HOME/CODEX_HOME。仅重定位工具链脚本，不补丁修改被测包的源码。

例如，自定义 prefix 下的实际构建可使用：

```bash
./configure --enable-zookeeper \
  --with-php-config="$release_prefix/bin/php-config" \
  --with-libzookeeper-dir="$release_prefix" \
  --with-libdir=lib/x86_64-linux-gnu
```

这里的 libdir 仅适用于对应的 amd64 multiarch 布局；其他平台使用实际库目录。日志保留工具链问题与最终成功结果的区别，不隐藏编译诊断或未执行的测试。
