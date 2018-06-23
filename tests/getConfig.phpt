--TEST--
Zookeeper::getConfig()
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'skip ZooKeeper extension is not loaded';
}
if (!class_exists('ZookeeperConfig'))
    echo 'skip Class ZookeeperConfig is not defined';
?>
--FILE--
<?php
$client = new Zookeeper();
$client->connect('localhost:2181');
$zkConfig = $client->getConfig();
$rc = new ReflectionClass($zkConfig);
echo $rc->getName();
--EXPECT--
ZookeeperConfig
