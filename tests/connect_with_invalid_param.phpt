--TEST--
Should connect the Zookeeper with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/connect_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::connect(): Argument #%d ($watcher_cb) must be a valid callback or null, no array or string given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->connect('1', 10)
#1 {main}
  thrown in %s on line %d
