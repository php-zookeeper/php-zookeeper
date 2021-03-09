--TEST--
Throw error when set parameter in isRecoverable method
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/throw_error_when_set_param_in_isrecoverable.inc
--EXPECTF--
Fatal error: Uncaught ArgumentCountError: Zookeeper::isRecoverable() expects exactly 0 arguments, 1 given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->isRecoverable('t')
#1 {main}
  thrown in %s on line %d
