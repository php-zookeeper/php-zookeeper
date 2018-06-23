--TEST--
Should construct
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper();
echo get_class($client);
--EXPECT--
Zookeeper
