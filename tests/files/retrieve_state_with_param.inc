<?php
$client = new Zookeeper('localhost:2181');
echo $client->getState('a');
