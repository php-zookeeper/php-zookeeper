--TEST--
Get state should throw error when define parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_state_with_param.inc
--EXPECTF--
Warning: Zookeeper::getState() expects exactly %d parameters, %d given in %s on line %d
