--TEST--
Set should throw error with invalid paramater
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/set_should_throw_error_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught ArgumentCountError: Zookeeper::set() expects at least 2 arguments, 1 given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->set(Array)
#1 {main}
  thrown in %s on line %d
