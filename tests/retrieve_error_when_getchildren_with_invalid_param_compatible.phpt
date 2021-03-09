--TEST--
Should retrieve error when get children with invalid param
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_when_getchildren_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::getChildren() expects parameter %d to be string, array given in %s on line %d
