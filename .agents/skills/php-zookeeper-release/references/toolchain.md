# Release Validation Toolchain

Read this reference only when tools are missing, versions are mixed, or a non-system prefix is used. Choose the environment based on current availability; do not hardcode a host, PHP/ZooKeeper version, or mirror.

## Preference order

1. Existing local build tools with consistent versions.
2. An authorized, accessible container or remote build environment. Check permissions and tool versions first. When packaging remotely, verify the transferred sources' SHA and retrieve the exact tgz that passed validation.
3. An isolated toolchain inside the project: download packages from a trusted repository and extract them under `reports/release-<version>/toolchain/root/`. A release does not require automatically installing system packages.

Treat SSH addresses, Docker sockets, prefixes, and paths in old knowledge-base records as leads. Recheck them before use. If permissions block network, directory, or socket access, use the normal elevation process rather than bypassing restrictions.

## Ubuntu package approach

- Confirm the distribution, source URL/mirror, and candidate versions from configured repositories. Inspect dependencies with `apt-cache policy` and `apt-get -s --no-install-recommends install ...`.
- Use `apt-get download` to download required packages into the project's isolated directory, then extract them with `dpkg-deb -x`. A simulated install lists only dependencies missing relative to the system state. An isolated environment also needs checks for components marked as installed but actually inaccessible.
- PHP typically needs matching CLI, dev, common, and XML packages, plus php-pear. Build tools include GCC, make, autoconf, libtool, pkg-config, shtool, and their actual dependencies. Determine package names from the selected distribution instead of hardcoding a PHP version.
- libzookeeper typically needs matching `libzookeeper-mt-dev` and `libzookeeper-mt2` packages. Record the source URL, suite/component, exact package versions, downloaded file checksums, and extraction prefix. Record mirror addresses separately from upstream package identity.
- Use `php --ri`, `ldd`, or equivalent inspection to confirm that the final module loads the intended library. A configure path alone does not prove that a different copy is not loaded at runtime.

## Non-system prefix issues

| Symptom | Verification and remedy |
| --- | --- |
| PHP cannot find shared libraries | Use `ldd` to identify missing libraries and set `LD_LIBRARY_PATH` for this build |
| phpize cannot find headers/build/shtool/libtool | Inspect hardcoded `/usr` paths; adjust only the isolated scripts' prefix and auxiliary paths |
| autoconf cannot find Perl modules, macros, or trailer.m4 | Check that `autom4te_perllibdir`, `AC_MACRODIR`, `AUTOM4TE`, `M4`, and `trailer_m4` point into the isolated toolchain |
| GCC reports its version but cannot execute cc1 | Inspect config.log and `gcc -print-prog-name=cc1`; download the matching compiler frontend, commonly provided on Ubuntu by `cpp-<major>-x86-64-linux-gnu` |
| PEAR cannot find PHP or PEAR.php/XML | Check `PHP_PEAR_PHP_BIN`, `PHP_PEAR_INSTALL_DIR`, the isolated php.ini, and XML extensions |
| configure compiler checks fail | Diagnose from config.log in the extracted source directory; establish that the toolchain is responsible before changing the environment, rather than blindly editing project sources |

Save a reusable `env.sh` and configuration in the report directory. Use environment variables according to their tools' intended meanings. Prefix custom task variables with `release_`; do not repurpose HOME or CODEX_HOME. Relocate only toolchain scripts, without patching the package sources being tested.

For example, a build using a custom prefix can run:

```bash
./configure --enable-zookeeper \
  --with-php-config="$release_prefix/bin/php-config" \
  --with-libzookeeper-dir="$release_prefix" \
  --with-libdir=lib/x86_64-linux-gnu
```

This libdir applies only to the corresponding amd64 multiarch layout. Use the actual library directory on other platforms. Keep toolchain failures distinct from final successful results in the logs; do not hide compiler diagnostics or tests that were not run.
