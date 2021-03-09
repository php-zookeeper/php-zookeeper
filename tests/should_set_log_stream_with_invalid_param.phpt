--TEST--
Test should set log stream with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/should_set_log_stream_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught ArgumentCountError: Zookeeper::setLogStream() expects exactly 1 argument, 2 given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->setLogStream('/tmp/log.log', '')
#1 {main}
  thrown in %s on line %d
