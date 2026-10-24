--TEST--
The first one-shot watcher releases its registry reference before close, including after reconnect
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    die('skip ZooKeeper extension is not loaded');
if (!class_exists('WeakReference'))
    die('skip WeakReference requires PHP 7.4');
?>
--FILE--
<?php
$acl = array(array('perms' => Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone'));
$writer = new Zookeeper('localhost:2181');

foreach (array('get', 'exists', 'getChildren') as $method) {
    $path = '/issue68_first_' . $method;
    if ($writer->exists($path)) {
        foreach ($writer->getChildren($path) as $child) {
            $writer->delete($path . '/' . $child);
        }
        $writer->delete($path);
    }
    $writer->create($path, '0', $acl);
    $client = new Zookeeper('localhost:2181');

    for ($round = 0; $round < 2; $round++) {
        $fired = 0;
        $watcher = static function ($type, $state, $node) use (&$fired) {
            $fired++;
        };
        $reference = WeakReference::create($watcher);
        $client->$method($path, $watcher);
        if ($method === 'getChildren') {
            $writer->create($path . '/child-' . $round, '', $acl);
        } else {
            $writer->set($path, (string) ($round + 1));
        }
        $deadline = microtime(true) + 5;
        while (!$fired && microtime(true) < $deadline) {
            zookeeper_dispatch();
            usleep(1000);
        }

        // Keep this strong reference until dispatch returns: this test isolates
        // registry cleanup from destruction of the callable's final reference.
        unset($watcher);
        printf("%s/%d: fired=%d, %s\n", $method, $round, $fired,
            $reference->get() === null ? 'released' : 'retained');
        $client->close();
        if ($round === 0) {
            $client->connect('localhost:2181');
        }
    }
    foreach ($writer->getChildren($path) as $child) {
        $writer->delete($path . '/' . $child);
    }
    $writer->delete($path);
}
$writer->close();
?>
--EXPECT--
get/0: fired=1, released
get/1: fired=1, released
exists/0: fired=1, released
exists/1: fired=1, released
getChildren/0: fired=1, released
getChildren/1: fired=1, released
