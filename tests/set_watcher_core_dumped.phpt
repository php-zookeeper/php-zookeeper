--TEST--
Fix Segmentation fault (core dumped) after setWatcher()
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper('127.0.0.1:2181');

var_dump($client->setWatcher(function() {}));
var_dump($client->create('/test_core_dumped', 'test'));
--EXPECTF--
bool(true)
bool(true)
