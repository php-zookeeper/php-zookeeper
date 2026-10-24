--TEST--
The first watcher on a failed operation releases its registry reference before close
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    die('skip ZooKeeper extension is not loaded');
if (!class_exists('WeakReference'))
    die('skip WeakReference requires PHP 7.4');
?>
--FILE--
<?php
foreach (array('get', 'getChildren', 'exists') as $method) {
    $client = new Zookeeper('localhost:2181');
    $watcher = static function ($type, $state, $node) {};
    $reference = WeakReference::create($watcher);
    $failed = false;
    try {
        $client->$method('invalid-path', $watcher);
    } catch (ZookeeperException $exception) {
        $failed = true;
        // Exception arguments can hold the watcher independently of the registry.
        unset($exception);
    }
    unset($watcher);
    printf("%s: failed=%d, %s\n", $method, $failed,
        $reference->get() === null ? 'released' : 'retained');
    $client->close();
}
?>
--EXPECT--
get: failed=1, released
getChildren: failed=1, released
exists: failed=1, released
