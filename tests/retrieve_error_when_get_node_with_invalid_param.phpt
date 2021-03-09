--TEST--
Should get Zookeeper node with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_when_get_node_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::get(): Argument #1 ($path) must be of type string, array given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->get(Array)
#1 {main}
  thrown in %s on line %d
