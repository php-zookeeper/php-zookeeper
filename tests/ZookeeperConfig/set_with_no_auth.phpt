--TEST--
ZookeeperConfig::set() with no auth
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
$zkConfig = $client->getConfig();
try {
    $zkConfig->set("server.1=localhost:2888:3888:participant;0.0.0.0:2181");
} catch (ZookeeperAuthenticationException $e) {
    fprintf(STDOUT, "%s\n%d", $e->getMessage(), $e->getCode());
}

--EXPECT--
not authenticated
-102
