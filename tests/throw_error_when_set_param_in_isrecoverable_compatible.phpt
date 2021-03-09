--TEST--
Throw error when set parameter in isRecoverable method
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/throw_error_when_set_param_in_isrecoverable.inc
--EXPECTF--
Warning: Zookeeper::isRecoverable() expects exactly %d parameters, %d given in %s on line %d
