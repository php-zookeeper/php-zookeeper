<?php

$zk = new Zookeeper('localhost:2181');
$conf = $zk->getConfig();
$r = $conf->get(function() {
	echo "Bingo!" . PHP_EOL;
});
var_dump($r);

while(1) {
	zookeeper_dispatch();
	sleep(1);
}
