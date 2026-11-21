--TEST--
ZooKeeper strict sessions replace expired IDs
--INI--
session.save_handler=zookeeper
session.save_path=localhost:2181
session.use_strict_mode=1
session.use_cookies=0
session.cache_limiter=
session.gc_probability=0
session.gc_maxlifetime=1
--SKIPIF--
<?php
if (!extension_loaded('zookeeper')) die('skip ZooKeeper extension is not loaded');
if (PHP_VERSION_ID < 80600) die('skip Requires PHP 8.6 session validation callbacks');
if (!function_exists('proc_open')) die('skip proc_open is unavailable');
?>
--FILE--
<?php
ob_start();
$client = new Zookeeper('localhost:2181');
$ids = array();
try {
    session_start();
    $expired = session_id();
    $ids[] = $expired;
    $_SESSION['value'] = 123;
    session_write_close();

    // Expiry uses request start time, so reopen in a fresh PHP process.
    sleep(2);
    $code = <<<'PHP'
session_id($argv[1]);
session_start();
var_dump(session_id() !== $argv[1], $_SESSION === array());
session_write_close();
session_start();
session_destroy();
PHP;
    $command = array(PHP_BINARY, '-n',
        '-d', 'extension=' . dirname(__DIR__) . '/modules/zookeeper.so',
        '-d', 'session.save_handler=zookeeper',
        '-d', 'session.save_path=localhost:2181',
        '-d', 'session.use_strict_mode=1',
        '-d', 'session.use_cookies=0',
        '-d', 'session.cache_limiter=',
        '-d', 'session.gc_probability=0',
        '-d', 'session.gc_maxlifetime=1',
        '-r', $code, $expired);
    $process = proc_open($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    echo stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    var_dump(proc_close($process), $errors);

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
int(0)
string(0) ""
