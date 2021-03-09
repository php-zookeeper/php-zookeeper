--TEST--
Should retrieve error when set invalid acl parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") < 0) {
    echo "skip PHP version should be at least 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_with_invalid_acl_param.inc
--EXPECTF--
Fatal error: Uncaught TypeError: Zookeeper::getAcl(): Argument #1 ($path) must be of type string, array given in %s:%s
Stack trace:
#0 %s(%s): Zookeeper->getAcl(Array)
#1 {main}
  thrown in %s on line %s
