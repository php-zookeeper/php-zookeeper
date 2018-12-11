# PHP ZooKeeper Extension

[![Build Status](https://img.shields.io/travis/php-zookeeper/php-zookeeper/master.svg?style=flat-square)](https://travis-ci.org/php-zookeeper/php-zookeeper)
[![Coveralls](https://img.shields.io/coveralls/php-zookeeper/php-zookeeper.svg?style=flat-square)](https://coveralls.io/r/php-zookeeper/php-zookeeper?branch=master)

This extension uses libzookeeper library to provide API for communicating with
ZooKeeper service.

ZooKeeper is an Apache project that enables centralized service for maintaining
configuration information, naming, providing distributed synchronization, and
providing group services.



## Requirements

- [ZooKeeper C Binding](https://zookeeper.apache.org/) (>= 3.4)
- [PHP](http://www.php.net/) (>= 7.0)



## Install

### 1.Compile ZooKeeper C Binding

```shell
$ ./configure --prefix=/path/to/zookeeper-c-binding
$ make
$ sudo make install
```



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



## Resources

- [Document](https://secure.php.net/manual/en/book.zookeeper.php)
- [PECL Page](https://pecl.php.net/package/zookeeper)
- [Zookeeper](https://zookeeper.apache.org/)
- [PHP Zookeeper Recipes](https://github.com/Gutza/php-zookeeper-recipes)
- [PHP Zookeeper Admin](https://github.com/Timandes/zookeeper-admin)

