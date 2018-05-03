<?php

$zk = new Zookeeper('localhost:2181');
$conf = $zk->getConfig();
$r = $conf->get();
var_dump($r);
