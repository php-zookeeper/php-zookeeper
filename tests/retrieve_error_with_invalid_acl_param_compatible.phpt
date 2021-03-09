--TEST--
Should retrieve error when set invalid acl parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_with_invalid_acl_param.inc
--EXPECTF--
Warning: Zookeeper::getAcl() expects parameter %d to be string, array given in %s on line %d
