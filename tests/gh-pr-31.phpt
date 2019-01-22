--TEST--
php-zookeeper/php-zookeeper's PR #31
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) {
    echo 'skip ZooKeeper extension is not loaded';
}
?>
--FILE--
<?php
function create($key)
{
    $store = new \Zookeeper('127.0.0.1:2181');
    $store->create(
        $key,
        '',
        [[ 'perms' => \Zookeeper::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone' ]],
        \Zookeeper::EPHEMERAL
    );
}

create('/' . uniqid());
create('/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' . uniqid());

echo 'OK';

--EXPECT--
OK
