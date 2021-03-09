--TEST--
Throw error when set watcher with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/throw_error_when_set_watcher_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::setWatcher() expects parameter %s to be a valid callback, array must have exactly two members in %s on line %d
