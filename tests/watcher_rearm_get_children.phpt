--TEST--
Should keep firing a getChildren() watcher that is re-armed from its own callback
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$path = '/watcher_rearm_get_children';
$acl = array(array('perms' => Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone'));

$client = new Zookeeper('localhost:2181');
$writer = new Zookeeper('localhost:2181');
$client->create($path, '0', $acl);

// Every time the watcher fires, re-arm it from inside the callback. The callback data for each one-shot watcher is
// released after it fires, so doing this many times exercises that cleanup path over and over.
$fired = 0;
$watcher = function ($type, $state, $node) use (&$watcher, &$fired, $client) {
    if ($type === Zookeeper::CHILD_EVENT) {
        $fired++;
        $client->getChildren($node, $watcher);
    }
};
$client->getChildren($path, $watcher);

for ($i = 1; $i <= 100; $i++) {
    $writer->create("$path/child-$i", "", $acl);
    $deadline = microtime(true) + 5;
    while ($fired < $i && microtime(true) < $deadline) {
        zookeeper_dispatch();
        usleep(1000);
    }
}

echo $fired, PHP_EOL;
--CLEAN--
<?php
$client = new Zookeeper('localhost:2181');
$path = '/watcher_rearm_get_children';
if ($client->exists($path)) {
    foreach ($client->getChildren($path) as $child) {
        $client->delete("$path/$child");
    }
    $client->delete($path);
}
--EXPECT--
100

