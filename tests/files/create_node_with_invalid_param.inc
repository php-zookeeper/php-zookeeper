<?php
$client = new Zookeeper('localhost:2181');
$client->create('/test5', array());
