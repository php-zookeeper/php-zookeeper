# PHP ZooKeeper Extension

[![ext-zookeeper](https://img.shields.io/github/actions/workflow/status/php-zookeeper/php-zookeeper/ext.yaml?logo=github&style=flat-square&label=ext-zookeeper)](https://github.com/php-zookeeper/php-zookeeper/actions/workflows/ext.yaml)
[![tests](https://img.shields.io/github/actions/workflow/status/php-zookeeper/php-zookeeper/test.yaml?logo=github&style=flat-square&label=tests)](https://github.com/php-zookeeper/php-zookeeper/actions/workflows/test.yaml)
[![codecov](https://img.shields.io/codecov/c/github/php-zookeeper/php-zookeeper/master.svg?style=flat-square)](https://codecov.io/gh/php-zookeeper/php-zookeeper)
[![Release](https://img.shields.io/github/v/release/php-zookeeper/php-zookeeper?display_name=tag&style=flat-square)](https://github.com/php-zookeeper/php-zookeeper/releases)
[![License](https://img.shields.io/badge/License-PHP-blue.svg?style=flat-square)](https://github.com/php-zookeeper/php-zookeeper/blob/master/LICENSE)
[![DeepWiki](https://img.shields.io/badge/DeepWiki-visit-brightgreen?style=flat-square)](https://deepwiki.com/php-zookeeper/php-zookeeper)

This extension uses libzookeeper library to provide API for communicating with
ZooKeeper service.

ZooKeeper is an Apache project that enables centralized service for maintaining
configuration information, naming, providing distributed synchronization, and
providing group services.



## Requirements

- [ZooKeeper C Binding](https://zookeeper.apache.org/) (>= 3.4)
- [PHP](http://www.php.net/) (>= 7.0)
  - [PHP official supported versions](https://www.php.net/supported-versions.php)



## Install

### 1.Compile ZooKeeper C Binding

```shell
$ ./configure --prefix=/path/to/zookeeper-c-binding
$ make
$ sudo make install
```

As of ZooKeeper 3.5.0, after unpacking source tarball, the following command should be executed before above-metioned steps:

```shell
$ autoreconf -if
```

As of ZooKeeper 3.5.9, the following command should be executed before `autoreconf -if`:

```shell
$ ant compile_jute
```

As of ZooKeeper 3.6.0, `ant` will fail because of missing `build.xml`. That file and two other files can be found in source tarball of `3.5.9`:

```shell
$ cd apache-zookeeper-3.5.9
$ cp build.xml ivy* ../apache-zookeeper-3.6.2
```

As of ZooKeeper 3.7.0, after unpacking source tarball, first execute the following command with Java 8 to build jute files:

```shell
$ mvn compile
```

Then navigate to the C client directory and generate the configure file:

```shell
$ cd zookeeper-client/zookeeper-client-c
$ autoreconf -if
```

After that, you can proceed with the standard build steps mentioned above.



### 2.Compile PHP ZooKeeper Extension

```shell
$ phpize
$ ./configure --with-libzookeeper-dir=/path/to/zookeeper-c-binding
$ make
$ sudo make install
```



## Examples

```php
<?php
$zc = new Zookeeper();
$zc->connect('localhost:2181');
var_dump($zc->get('/zookeeper'));
?>
```



## Working with other extensions

### 1.Swoole

```php
Swoole\Async::set([
    'enable_signalfd' => false, // See: https://github.com/swoole/swoole-src/issues/302
]);

$zk = new Zookeeper('localhost:2181');

Swoole\Process::signal(SIGTERM, function() {
        echo "TERM" . PHP_EOL;
        Swoole\Event::exit();
    });
Swoole\Event::wait();
```



## For Developers

* Install [EditorConfig](https://editorconfig.org/) to your IDE.

### Branches

* master: Main branch.
* 0.5.x: The last branch which still supports PHP 5.x.



## Resources

- [Document](https://secure.php.net/manual/en/book.zookeeper.php)
- [PECL Page](https://pecl.php.net/package/zookeeper)
- [Zookeeper](https://zookeeper.apache.org/)
- [PHP Zookeeper Recipes](https://github.com/Gutza/php-zookeeper-recipes)
- [PHP Zookeeper Admin](https://github.com/Timandes/zookeeper-admin)

