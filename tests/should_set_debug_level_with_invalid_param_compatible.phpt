--TEST--
Test should set debug level with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/should_set_debug_level_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::setDebugLevel() expects parameter %d to be %s, array given in %s on line %d
