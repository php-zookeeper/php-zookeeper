--TEST--
A watcher can close and reconnect its own client while its callable remains alive
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$path = '/close_from_watcher_' . getmypid();
$acl = array(array('perms' => Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone'));
$writer = new Zookeeper('localhost:2181');
$writer->create($path, 'initial', $acl);
$client = new Zookeeper('localhost:2181');
// Use a second registration so PHP 8's first-key bug cannot mask dispatch cleanup.
$client->exists($path, function () {});
$finished = false;
$callback = function ($type, $state, $node) use ($client, &$finished) {
    $token = new stdClass();
    $token->value = 'alive';
    $client->close();
    $client->connect('localhost:2181');
    echo 'callback returned: ', $token->value, PHP_EOL;
    $finished = true;
};
$client->get($path, $callback);
unset($callback);
$writer->set($path, 'changed');
$deadline = microtime(true) + 5;
while (!$finished && microtime(true) < $deadline) {
    zookeeper_dispatch();
    usleep(1000);
}
echo 'finished: ', $finished ? 'yes' : 'no', PHP_EOL;
echo 'reconnected: ', $client->exists($path) ? 'yes' : 'no', PHP_EOL;
$client->close();
$writer->delete($path);
$writer->close();
?>
--EXPECT--
callback returned: alive
finished: yes
reconnected: yes
