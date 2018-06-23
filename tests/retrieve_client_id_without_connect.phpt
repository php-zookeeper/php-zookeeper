--TEST--
Should get client id without_connect
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper();
try {
    $client->getClientId();
} catch(ZookeeperConnectionException $zce) {
    printf("%s\n%d", $zce->getMessage(), $zce->getCode());
}
--EXPECTF--
Zookeeper->connect() was not called
5998