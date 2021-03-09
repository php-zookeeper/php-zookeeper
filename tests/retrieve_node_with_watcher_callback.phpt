--TEST--
Should get Zookeeper node with watcher callback
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
class Test extends Zookeeper
{
    private $path = '';

    public function watcher($i, $type, $key)
    {
        $this->exists($this->path, array($this, 'watcher'));
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }
}

$test = new Test('127.0.0.1:2181');

$path = '/test-node-' . uniqid();
$test->create($path, null);

$test->setPath($path);
echo gettype($test->get($path, array($test, 'watcher')));

$test->delete($path);

--EXPECT--
NULL
