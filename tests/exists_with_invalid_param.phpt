--TEST--
Should retrieve error when set invalid data in second parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/exists_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::exists(): Argument #2 ($watcher_cb) must be a valid callback or null, no array or string given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->exists('/test', 1)
#1 {main}
  thrown in %s on line %d
