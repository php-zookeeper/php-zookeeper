--TEST--
Should retrieve error recv timeout with invalid parameter
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
if (version_compare(PHP_VERSION, "8.0.0") >= 0) {
    echo "skip PHP version should not greater than 8.0.0";
}
?>
--FILE_EXTERNAL--
files/retrieve_error_recv_timeout_with_invalid_param.inc
--EXPECTF--
Warning: Zookeeper::getRecvTimeout() expects exactly %d parameters, %d given in %s on line %d
