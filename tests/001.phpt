--TEST--
Check for zookeeper presence
--SKIPIF--
<?php
if (!extension_loaded('zookeeper'))
    echo 'skip ZooKeeper extension is not loaded';
?>
--FILE--
<?php 
echo "zookeeper extension is available";
?>
--EXPECT--
zookeeper extension is available
