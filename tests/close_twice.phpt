--TEST--
Closing a client that is already closed or never connected is a no-op
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper();
var_dump($client->close());
$client->connect('localhost:2181');
var_dump($client->close());
var_dump($client->close());
echo 'done', PHP_EOL;
?>
--EXPECT--
NULL
NULL
NULL
done
