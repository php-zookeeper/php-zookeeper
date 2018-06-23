--TEST--
Should retrieve children with watcher callback
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');

if ($client->exists('/retrieve_children_with_watcher_callback')) {
    $client->delete('/retrieve_children_with_watcher_callback');
}
echo count($client->getChildren('/', function() {
	var_dump(func_get_args());
})), PHP_EOL;
usleep(100000);

$client->create('/retrieve_children_with_watcher_callback', null, array(
    array(
        'perms' => Zookeeper::PERM_ALL,
        'scheme' => 'world',
        'id'    => 'anyone'
    )
), 2);
usleep(100000);

zookeeper_dispatch();
--EXPECTF--
%d
array(3) {
  [0]=>
  int(4)
  [1]=>
  int(3)
  [2]=>
  string(1) "/"
}
