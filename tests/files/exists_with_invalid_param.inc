<?php
$client = new Zookeeper('localhost:2181');
$client->exists('/test', 1.0);
