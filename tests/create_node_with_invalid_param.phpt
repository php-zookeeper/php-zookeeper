--TEST--
Should throw error when create node with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/create_node_with_invalid_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::create(): Argument #2 ($value) must be of type ?string, array given in %s:%d
Stack trace:
#0 %s(%d): Zookeeper->create('/test5', Array)
#1 {main}
  thrown in %s on line %d
