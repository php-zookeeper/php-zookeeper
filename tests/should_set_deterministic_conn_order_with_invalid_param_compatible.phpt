--TEST--
Test should set deterministic conn order with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/should_set_deterministic_conn_order_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::setDeterministicConnOrder() expects parameter %d to be boo%s, array given in %s on line %d
