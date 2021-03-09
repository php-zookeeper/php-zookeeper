--TEST--
Set acl should throw erro with invalid param
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/setacl_should_throw_error_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::setAcl() expects exactly %d parameters, %d given in %s on line %d
