--TEST--
Should throw error when create node with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/create_node_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::create() expects parameter 2 to be string, array given in %s on line %d
