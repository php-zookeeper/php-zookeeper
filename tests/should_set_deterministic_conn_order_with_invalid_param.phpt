--TEST--
Test should set deterministic conn order with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/should_set_deterministic_conn_order_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::setDeterministicConnOrder(): Argument #1 ($trueOrFalse) must be of type bool, array given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper::setDeterministicConnOrder(Array)
#1 {main}
  thrown in %s on line %d
