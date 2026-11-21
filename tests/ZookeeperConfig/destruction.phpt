--TEST--
ZookeeperConfig destruction releases object properties safely
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    die('skip ZooKeeper extension is not loaded');
if (!class_exists('ZookeeperConfig'))
    die('skip Class ZookeeperConfig is not defined');
?>
--FILE--
<?php
$config = new ZookeeperConfig();
unset($config);

class ConfigPayload
{
    public static $destroyed = 0;

    public function __destruct()
    {
        ++self::$destroyed;
    }
}

class ConfigWithPayload extends ZookeeperConfig
{
    public $payload;
}

for ($i = 0; $i < 100; ++$i) {
    $config = new ConfigWithPayload();
    $config->payload = new ConfigPayload();
    unset($config);
}

echo ConfigPayload::$destroyed, " properties destroyed\n";
?>
--EXPECT--
100 properties destroyed
