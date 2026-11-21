--TEST--
ZookeeperConfig destruction preserves the client connection
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    die('skip ZooKeeper extension is not loaded');
if (!class_exists('ZookeeperConfig'))
    die('skip Class ZookeeperConfig is not defined');
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');

for ($i = 0; $i < 100; ++$i) {
    $config = $client->getConfig();
    unset($config);
}

$config = $client->getConfig();
var_dump(is_string($config->get()));
unset($config);
$client->close();
echo "client closed\n";
?>
--EXPECT--
bool(true)
client closed
