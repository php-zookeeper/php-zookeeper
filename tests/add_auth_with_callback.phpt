--TEST--
Should add auth and trigger completion callback
--SKIPIF--
<?php
// vim: set ts=4 sts=4 sw=4 noet:
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

for ($i=0; $i<10 && !$cbTriggered; ++$i) {
	usleep(100000);
	if (version_compare(PHP_VERSION, "7.1.0") < 0)
		zookeeper_dispatch();
}
?>
--EXPECT--
1
array(1) {
  [0]=>
  int(-115)
}
