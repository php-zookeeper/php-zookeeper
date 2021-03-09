--TEST--
Test should set log stream with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/should_set_log_stream_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::setLogStream() expects exactly %d parameter, %d given in %s on line %d
