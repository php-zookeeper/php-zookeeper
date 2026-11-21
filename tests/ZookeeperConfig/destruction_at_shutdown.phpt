--TEST--
ZookeeperConfig can be destroyed at request shutdown
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    die('skip ZooKeeper extension is not loaded');
if (!class_exists('ZookeeperConfig'))
    die('skip Class ZookeeperConfig is not defined');
?>
--FILE--
<?php
$config = new ZookeeperConfig();
echo "request completed\n";
?>
--EXPECT--
request completed
