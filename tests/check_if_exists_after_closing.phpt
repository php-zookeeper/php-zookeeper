--TEST--
Should check if node exists after closing
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'ZooKeeper extension is not loaded'
};
--FILE--
<?php
$client = new Zookeeper();
$client->connect('localhost:2181');
$client->close();
try {
    $client->exists('/test1');
} catch(ZookeeperConnectionException $zce) {
    printf("%s\n%d", $zce->getMessage(), $zce->getCode());
}
--EXPECTF--
Zookeeper->connect() was not called
5998