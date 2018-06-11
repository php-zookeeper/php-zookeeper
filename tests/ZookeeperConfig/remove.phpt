--TEST--
ZookeeperConfig::remove();
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'ZooKeeper extension is not loaded'
}
if (!class_exists('ZookeeperConfig'))
    echo 'Skipping';
?>
--FILE--
<?php
$client = new Zookeeper();
$client->connect('localhost:2181');
$client->addAuth('digest', 'timandes:timandes');
$zkConfig = $client->getConfig();
$zkConfig->set("server.1=localhost:2888:3888:participant;0.0.0.0:2181,server.2=localhost:2889:3889:participant;0.0.0.0:2182");

$zkConfig->remove("2");
echo $zkConfig->get();
--EXPECTF--
server.1=localhost:2888:3888:participant;0.0.0.0:2181
version=%x
