--TEST--
Should add auth
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');
echo $client->addAuth('test', 'test');
--EXPECT--
1
