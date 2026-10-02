--TEST--
The first one-shot watcher releases its callable before close, including after reconnect
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) echo 'skip ZooKeeper extension is not loaded';
if (!class_exists('WeakReference')) echo 'skip WeakReference requires PHP 7.4';
?>
--FILE--
<?php
$path = '/first_watcher_cleanup_' . getmypid();
$acl = array(array('perms' => Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone'));
$writer = new Zookeeper('localhost:2181');
$writer->create($path, 'initial', $acl);

foreach (array('exists', 'get', 'getChildren', 'reconnect') as $method) {
    $client = new Zookeeper('localhost:2181');
    if ($method === 'reconnect') {
        $client->close();
        $client->connect('localhost:2181');
    }
    $fired = false;
    $callback = function ($type, $state, $node) use (&$fired) { $fired = true; };
    $weak = WeakReference::create($callback);
    $operation = $method === 'reconnect' ? 'exists' : $method;
    $client->$operation($path, $callback);
    if ($operation === 'getChildren') {
        $writer->create($path . '/child', 'value', $acl);
    } else {
        $writer->set($path, $method);
    }
    $deadline = microtime(true) + 5;
    while (!$fired && microtime(true) < $deadline) {
        zookeeper_dispatch();
        usleep(1000);
    }
    unset($callback);
    echo $method, ': ', $fired && $weak->get() === null ? 'released' : 'retained', PHP_EOL;
    $client->close();
    if ($operation === 'getChildren') $writer->delete($path . '/child');
}
$writer->delete($path);
$writer->close();
?>
--EXPECT--
exists: released
get: released
getChildren: released
reconnect: released
