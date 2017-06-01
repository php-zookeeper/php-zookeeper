--TEST--
Should add auth and trigger completion callback
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'Zookeeper extension is not loaded'
};
--FILE--
<?php
$client = new Zookeeper('localhost:2181');
$client->setLogStream(fopen('/dev/null', 'w'));
echo $client->addAuth('test', 'test', function() {
	var_dump(func_get_args());
}), PHP_EOL;
usleep(100000);
zookeeper_dispatch();
--EXPECT--
1
array(1) {
  [0]=>
  int(-115)
}
