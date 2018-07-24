--TEST--
Should add auth and trigger completion callback
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
	echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php
$fh = fopen('/dev/null', 'w');
$client = new Zookeeper('localhost:2181');
$client->setLogStream($fh);
$cbTriggered = false;
echo $client->addAuth('test', 'test', function() use(&$cbTriggered) {
	var_dump(func_get_args());
	$cbTriggered = true;
}), PHP_EOL;

for ($i=0; $i<3 && !$cbTriggered; ++$i) {
	usleep(100000);
	zookeeper_dispatch();
}
?>
--EXPECT--
1
array(1) {
  [0]=>
  int(-115)
}
