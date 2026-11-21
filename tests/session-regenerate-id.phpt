--TEST--
ZooKeeper session ID regeneration preserves data and handles the old node
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
?>
--FILE--
<?php
ob_start();
$client = new Zookeeper('localhost:2181');
$ids = array();
try {
    foreach (array(false, true) as $deleteOld) {
        session_id('');
        session_start();
        $old = session_id();
        $ids[] = $old;
        $_SESSION['value'] = 123;
        session_write_close();

        session_id($old);
        session_start();
        var_dump(session_regenerate_id($deleteOld));
        $new = session_id();
        $ids[] = $new;
        var_dump($new !== $old, $_SESSION['value']);
        session_write_close();
        var_dump(($client->exists('/php-sessid/' . $old) !== false) === !$deleteOld);

        session_id($new);
        session_start();
        var_dump($_SESSION['value']);
        session_write_close();
    }
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
int(123)
bool(true)
int(123)
bool(true)
bool(true)
int(123)
bool(true)
int(123)
