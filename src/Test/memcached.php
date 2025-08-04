<?php
$server = 'memcached';

$memcache = new Memcache;
$isMemcacheAvailable = $memcache->connect($server);

if ($isMemcacheAvailable) {
    $aData = $memcache->get('data');
    if ($aData) {
        echo '<h2>Data from Cache:</h2>';
        print_r($aData);
    } else {
        $aData = array(
            'me' => 'you',
            'us' => 'them',
        );
        echo '<h2>Fresh Data:</h2>';
        print_r($aData);
        $memcache->set('data', $aData, 0, 300);
    }
}
