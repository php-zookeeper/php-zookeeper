---
name: php-zookeeper-release
description: Use when preparing or completing a php-zookeeper version release, updating release metadata, or validating its PEAR tgz distribution.
---

# php-zookeeper 发版

以经用户确认的版本、经验证的 PEAR 源码包和明确的 Git 提交为同一发布对象。使用中文沟通；本技能只适用于当前项目。

## 1. 确认发布基线

- 阅读项目指引；按用户知识库约定查询既有构建资料，核验后再复用其中的环境。
- 检查 `git status --short --branch`、远端默认分支、分支与 tags；刷新远端引用，阅读上个正式 tag 之后的改动。已有未提交改动时先厘清归属，保留用户工作。
- 根据改动建议版本号：仅修复通常升 patch，新增兼容功能通常升 minor；**先取得用户对具体版本号的确认，再更新版本文件或创建该版本的分支**。先检查本地和远端同名 tag，不能覆盖已有发布。
- 识别实际主分支。若用户说 `main` 而仓库只有 `master`，说明差异并确认目标；不要自行创建或改名主分支。会话中已确认的版本、目标和操作授权直接沿用。
- 从已同步的目标主分支创建 `release/v<版本>`，沿用 Git Flow。干净工作区可直接切分支；需要 worktree 时仅放在项目内，被沙箱拒绝后请求提权，不换目录或退回当前工作区绕过。

技能被加载不等于获得推送或发布授权。若用户已经授权完整发版流程，持续执行至合并推送；若只要求准备或验证，完成对应范围后保留结果。

## 2. 同步发布元数据

| 文件 | 更新内容 |
| --- | --- |
| `ChangeLog` | 将本轮 `Unreleased` 归入 `Version <版本>`，覆盖上个 tag 以来实际改动并保留历史 |
| `php_zookeeper.h` | `PHP_ZOOKEEPER_VERSION` |
| `package.xml` | 顶层 release 版本、实际发布日期、stability、notes，并在 changelog 首部添加本轮记录 |

发布日期按用户时区获取；保留历史日期和说明。`package.xml` 的 API 版本独立于 release 版本，没有 API 变更时保持原值；不要全局替换历史版本。再搜索其他生效的内部版本常量，核对说明与代码一致，执行 `git diff --check`。

## 3. 准备并记录构建依赖

优先复用可用环境，核对 PHP CLI、phpize、php-config、PHP 头文件的版本/API 与 NTS/ZTS 一致。确认 PEAR、C 编译器、make、autoconf、libtool、libzookeeper 头文件及运行库可用。

**每次验证必须记录 libzookeeper 的来源类型、仓库或下载地址、发行版/镜像、具体软件包或源码版本、实际使用路径，以及模块报告的库版本。** 不要仅写“使用本机库”。复用历史目录也需要重新核验。

本机缺少工具时，按 [references/toolchain.md](references/toolchain.md) 选择构建环境。日志和校验清单放在项目已忽略的 `reports/release-<版本>/`；工具链、构建产物及 tgz 不进入源码提交。确认忽略规则有效，不自动扩大忽略范围。

## 4. 用 PEAR 打包并检查内容

从已完成元数据更新的项目根目录执行 `pear package package.xml`，保存命令、完整日志和退出码。分发产物为 `zookeeper-<版本>.tgz`，不得用 `git archive` 或普通 tar 代替 PEAR 打包。

在解包前检查归档，保存 SHA256 与逐文件清单：

1. 从源 `package.xml` 的嵌套 contents/dir/file 推导完整文件集合，不写死文件数量。
2. 确认归档路径恰好为根 `package.xml` 与 `zookeeper-<版本>/` 下声明的文件；拒绝重复路径、绝对路径、`..`、链接及额外构建产物。
3. 逐文件核对包内内容与发布源码的字节、包内 manifest 的 MD5，并记录 SHA256。核对 `config.m4` 所需源码和头文件没有漏列。
4. 比较包内和源 manifest 的版本、API、日期、稳定性、notes、contents 与本轮历史。PEAR 会重写 XML 格式并填充校验和，按语义比较元数据，不要求两份 XML 字节相同。

## 5. 从解压目录构建

必须从**这一个 tgz** 解压到全新空目录，构建期间不引用仓库内未打包的源码。以下示例从项目根目录运行，假定已选择匹配的工具链；shell 使用 `set -euo pipefail`，相对路径先转绝对路径：

```bash
release_version='<用户已确认的版本>'
release_root="$PWD"
release_report="$release_root/reports/release-$release_version"
mkdir -p "$release_report"
release_unpack=$(mktemp -d "$release_report/unpacked.XXXXXX")
tar -xzf "$release_root/zookeeper-$release_version.tgz" -C "$release_unpack"
cd "$release_unpack/zookeeper-$release_version"
phpize > "$release_report/phpize.log" 2>&1
./configure --enable-zookeeper > "$release_report/configure.log" 2>&1
make -j2 > "$release_report/make.log" 2>&1
RELEASE_EXPECTED_VERSION="$release_version" php -n \
  -d "extension=$PWD/modules/zookeeper.so" -r '
  if (!extension_loaded("zookeeper") ||
      phpversion("zookeeper") !== getenv("RELEASE_EXPECTED_VERSION") ||
      !class_exists("Zookeeper")) { exit(1); }
  echo "PASS: zookeeper ", phpversion("zookeeper"), PHP_EOL;
' > "$release_report/smoke.log" 2>&1
php -n -d "extension=$PWD/modules/zookeeper.so" --ri zookeeper \
  > "$release_report/module-info.log" 2>&1
cd "$release_root"
```

非系统前缀时为 configure 显式添加匹配的 `--with-php-config`、`--with-libzookeeper-dir` 和适用的 `--with-libdir`；为动态加载设置对应运行库路径，记录全部实际参数。

逐阶段检查退出码和日志；失败时停止打 tag、推送和合并。不能修改解包源码、关闭编译错误后将原包宣称为通过。需要修复发布源码时，重新打包、解包并验证。

基础验收为包完整性、phpize/configure/make 和模块加载/版本检查。服务集成测试、ZTS 和多版本矩阵按当次要求及可用环境执行，报告明确区分已测与未测；不能用编译通过代替这些结果。

## 6. 固定发布提交、保留包、推送 tag

- 检查最终 diff，以英文 Conventional Commit 提交，例如 `chore(release): prepare <版本>`；提交消息禁止包含 `Co-Authored-By`。
- 记录提交 SHA，重新逐文件比较包内源码与该提交（含源 `package.xml` 元数据）。验证后任何打包输入变化都会使原验证失效，必须重打包重验；只修改 Git 提交信息且文件内容相同则无需重编译。
- 把通过验证的原始 tgz 留在项目根目录，保留 SHA256 和日志，不将唯一副本留在待清理的临时目录。
- 再核对远端 tag 状态，创建指向发布提交的 annotated tag `v<版本>`。获得对应操作授权后，优先 `git push --atomic -u origin <发布分支> refs/tags/<tag>`。远端不支持 atomic 时分别推送并记录每一步结果；部分成功时先查明远端状态再续做，不自动删除或覆盖远端引用。
- 不 force-push，不移动既有发布 tag。tag 冲突或来源不明时暂停解决；已完成的同一发布从经核实的断点继续。

## 7. 合并回确认的主分支

tag 和发布分支成功推送后，刷新主分支并检查它是否自发布分支创建后前进。若有并发更新，先评估冲突及其对包和验证的影响，不能把旧包验证结果套到变更后的合并结果。

切回已确认的主分支，采用 `--no-ff` 合并，提交消息例如 `chore(release): merge <版本> into <目标分支>`。无并发改动时用 `git diff --exit-code <tag> HEAD` 证明合并后树与发布树相同，再复核包与提交、SHA256 和模块版本。存在新增代码时完成相应构建与测试后再推进。

推送主分支，使用 `git ls-remote` 核对远端主分支、发布分支、annotated tag 及其解引用提交；确认本地跟踪状态和工作区。推送被拒绝时检查远端变化，不强推。默认保留发布分支及验证包，不额外创建 GitHub Release、上传 PECL 或删除文件。

## 交付记录

最终记录必须包含：

- 版本/API/日期、发布提交 SHA、发布分支、tag 指向和最终主分支 SHA。
- tgz 的绝对路径、SHA256、清单/源码对应关系及验证日志路径。
- PHP/PEAR/编译器版本、NTS/ZTS、libzookeeper 来源与版本、安装前缀、实际构建参数。
- 各验证阶段结果、已执行测试与覆盖边界、远端核验和未完成步骤。

常见错误：按口头 `main` 合并到不存在的分支；同步修改历史/API 版本；在源码工作区编译代替解包验证；包验证后又改发布文件；从历史日志推断当前依赖；把未推送成功的步骤报为完成。
