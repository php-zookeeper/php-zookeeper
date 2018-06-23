--TEST--
Should retrieve bool if is recoverable
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');
echo $client->isRecoverable();
--EXPECT--
1
