--TEST--
Should create generic node
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
	echo 'skip ZooKeeper extension is not loaded';
}
?>
--FILE--
<?php
$client = new Zookeeper('localhost:2181');
$path = '/test_create_generic_node';

if ($client->exists($path)) {
	$client->delete($path);
}

echo $client->create($path, null);

$acl = $client->getAcl($path);
echo $acl[1][0]['perms'] === Zookeeper::PERM_ALL;
echo $acl[1][0]['scheme'];
echo $acl[1][0]['id'];
--EXPECTF--
/test_create_generic_node1worldanyone
