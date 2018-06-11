--TEST--
Zookeeper::getConfig()
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'ZooKeeper extension is not loaded'
};
--FILE--
<?php
$client = new Zookeeper();
$client->connect('localhost:2181');
$zkConfig = $client->getConfig();
$rc = new ReflectionClass($zkConfig);
echo $rc->getName();
--EXPECT--
ZookeeperConfig
