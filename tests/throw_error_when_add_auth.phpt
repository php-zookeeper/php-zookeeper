--TEST--
Should throw error when add auth
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/throw_error_when_add_auth.inc
--EXPECTF--
Fatal error: Uncaught ArgumentCountError: Zookeeper::addAuth() expects at least 2 arguments, 1 given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->addAuth(Array)
#1 {main}
  thrown in %s on line %d
