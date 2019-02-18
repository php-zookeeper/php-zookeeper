--TEST--
Dispatch nothing
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'skip ZooKeeper extension is not loaded';
}
?>
--FILE--
<?php
Zookeeper::dispatch();
echo 'Nothing';
--EXPECTF--
Nothing
