--TEST--
ZooKeeper strict sessions accept an existing empty session
--INI--
session.save_handler=zookeeper
session.save_path=localhost:2181
session.use_strict_mode=1
session.use_cookies=0
session.cache_limiter=
session.gc_probability=0
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) die('skip ZooKeeper extension is not loaded');
if (PHP_VERSION_ID < 80600) die('skip Requires PHP 8.6 session validation callbacks');
?>
--FILE--
<?php
ob_start();
$client = new Zookeeper('localhost:2181');
$ids = array();
try {
    session_start();
    $empty = session_id();
    $ids[] = $empty;
    $_SESSION = array();
    session_write_close();
    var_dump($client->exists('/php-sessid/' . $empty) !== false);

    session_id($empty);
    session_start();
    $ids[] = session_id();
    var_dump(session_id() === $empty, $_SESSION === array());
    session_write_close();
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
    foreach (array_unique($ids) as $id) {
        $path = '/php-sessid/' . $id;
        if ($client->exists($path)) $client->delete($path);
    }
}
ob_end_flush();
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
