--TEST--
ZooKeeper strict sessions accept stored IDs and replace unknown IDs
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
    $stored = session_id();
    $ids[] = $stored;
    $_SESSION['value'] = 123;
    session_write_close();

    session_id($stored);
    session_start();
    var_dump(session_id() === $stored, $_SESSION['value']);
    $candidate = session_create_id();
    var_dump(is_string($candidate) && $candidate !== $stored);
    $_SESSION['value'] = 456;
    session_write_close();

    session_id($stored);
    session_start();
    var_dump($_SESSION['value']);
    session_write_close();

    $unknown = 'php86-unknown-' . bin2hex(random_bytes(8));
    $ids[] = $unknown;
    session_id($unknown);
    session_start();
    $ids[] = session_id();
    var_dump(session_id() !== $unknown, $_SESSION === array());
    session_write_close();
    var_dump($client->exists('/php-sessid/' . $unknown));
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
int(123)
bool(true)
int(456)
bool(true)
bool(true)
bool(false)
