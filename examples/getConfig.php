<?php

Zookeeper::setDebugLevel(Zookeeper::LOG_LEVEL_DEBUG);
$zk = new Zookeeper('localhost:2181');
$conf = $zk->getConfig();
$r = $conf->get(function() {
	echo "Bingo!" . PHP_EOL;
});
var_dump($r);

$r = $conf->set("server.0=localhost:2888:3888:participant;2181");
var_dump($r);

while(1) {
	zookeeper_dispatch();
	sleep(1);
}
