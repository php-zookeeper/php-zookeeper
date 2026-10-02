--TEST--
The first failed watched operation and default watcher release their callable
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) echo 'skip ZooKeeper extension is not loaded';
if (!class_exists('WeakReference')) echo 'skip WeakReference requires PHP 7.4';
?>
--FILE--
<?php
foreach (array('get', 'getChildren', 'exists') as $method) {
    $client = new Zookeeper('localhost:2181');
    $callback = function ($type, $state, $path) {};
    $weak = WeakReference::create($callback);
    $failed = false;
    try {
        // An invalid path must fail even for exists(), which accepts ZNONODE.
        $client->$method('invalid/path', $callback);
    } catch (ZookeeperException $e) { $failed = true; }
    unset($callback);
    echo $method, ': ', $failed && $weak->get() === null ? 'released' : 'retained', PHP_EOL;
    $client->close();
}
$callback = function ($type, $state, $path) {};
$weak = WeakReference::create($callback);
$client = new Zookeeper('localhost:2181', $callback);
$client->close();
unset($callback);
echo 'default: ', $weak->get() === null ? 'released' : 'retained', PHP_EOL;
?>
--EXPECT--
get: released
getChildren: released
exists: released
default: released
