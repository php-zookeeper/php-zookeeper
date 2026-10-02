--TEST--
Closing during dispatch cancels queued watchers and preserves another client's queued events
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$root = '/close_pending_' . getmypid();
$acl = array(array('perms' => Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone'));
$writer = new Zookeeper('localhost:2181');
foreach (array('', '/start', '/first', '/second', '/other') as $suffix) {
    $writer->create($root . $suffix, 'initial', $acl);
}
$driver = new Zookeeper('localhost:2181');
$client = new Zookeeper('localhost:2181');
$other = new Zookeeper('localhost:2181');
$closed = false;
$canceled = 0;
$survived = false;
$client->get($root . '/first', function () use ($client, &$closed) {
    $client->close();
    $closed = true;
});
$client->get($root . '/second', function () use (&$canceled) { $canceled++; });
$other->get($root . '/other', function () use (&$survived) { $survived = true; });
$driver->get($root . '/start', function () use ($writer, $root) {
    // Native producers queue these events while the dispatch reentrancy guard is set.
    foreach (array('/first', '/second', '/other') as $suffix) {
        $writer->set($root . $suffix, 'changed');
    }
    usleep(200000);
});
$writer->set($root . '/start', 'changed');
$deadline = microtime(true) + 5;
while ((!$closed || !$survived) && microtime(true) < $deadline) {
    zookeeper_dispatch();
    usleep(1000);
}
echo 'closed: ', $closed ? 'yes' : 'no', PHP_EOL;
echo 'canceled callbacks: ', $canceled, PHP_EOL;
echo 'other client: ', $survived ? 'yes' : 'no', PHP_EOL;
$driver->close();
$client->close();
$other->close();
foreach (array('/start', '/first', '/second', '/other', '') as $suffix) {
    $writer->delete($root . $suffix);
}
$writer->close();
?>
--EXPECT--
closed: yes
canceled callbacks: 0
other client: yes
