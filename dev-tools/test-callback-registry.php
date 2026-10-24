<?php
$passed = zookeeper_test_registry_checks();
echo 'registry invariants: ', $passed ? 'PASS' : 'FAIL', PHP_EOL;

$methods = array('__construct', 'connect', 'get', 'exists', 'getChildren', 'addAuth', 'setWatcher');
if (class_exists('ZookeeperConfig')) {
    $methods[] = 'ZookeeperConfig::get';
}
foreach ($methods as $method) {
    $client = new Zookeeper();
    $original = static function ($type, $state, $path) {};
    if ($method === 'setWatcher') {
        $client->connect('localhost:2181', $original);
    } elseif ($method !== 'connect' && $method !== '__construct') {
        $client->connect('localhost:2181');
    }
    zookeeper_test_exhaust_registry($client);
    $before = zookeeper_test_registry_state($client);
    $callback = static function () {};
    $reference = WeakReference::create($callback);
    $rejected = false;
    try {
        if ($method === '__construct' || $method === 'connect') {
            $client->$method('localhost:2181', $callback);
        } elseif ($method === 'addAuth') {
            $client->addAuth('digest', 'php:zookeeper', $callback);
        } elseif ($method === 'setWatcher') {
            $client->setWatcher($callback);
        } elseif ($method === 'ZookeeperConfig::get') {
            $client->getConfig()->get($callback);
        } else {
            $client->$method('/zookeeper', $callback);
        }
    } catch (ZookeeperException $exception) {
        $rejected = $exception->getCode() === 5997;
        unset($exception);
    }
    unset($callback);
    $after = zookeeper_test_registry_state($client);
    $ok = $rejected && $reference->get() === null && $before === $after;
    printf("%s registration failure: %s\n", $method, $ok ? 'PASS' : 'FAIL');
    $passed = $passed && $ok;
    if ($method !== '__construct' && $method !== 'connect') {
        $client->close();
    }
    unset($client, $original);
}
exit($passed ? 0 : 1);
