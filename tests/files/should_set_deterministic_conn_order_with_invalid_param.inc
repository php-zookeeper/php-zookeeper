<?php
$client = new Zookeeper('localhost:2181');
$client->setDeterministicConnOrder(array());
