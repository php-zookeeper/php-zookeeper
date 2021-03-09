--TEST--
Should retrieve error recv timeout with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_recv_timeout_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught ArgumentCountError: Zookeeper::getRecvTimeout() expects exactly 0 arguments, 1 given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->getRecvTimeout(Array)
#1 {main}
  thrown in %s on line %d
