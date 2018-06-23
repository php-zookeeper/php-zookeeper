--TEST--
Should get Zookeeper state
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');
echo $client->getState();
--EXPECTF--
%d
