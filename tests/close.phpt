--TEST--
Should connect to the ZooKeeper and close it
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'ZooKeeper extension is not loaded'
};
--FILE--
<?php
$client = new Zookeeper();
$client->connect('localhost:2181');
echo gettype($client->close());
--EXPECT--
NULL
