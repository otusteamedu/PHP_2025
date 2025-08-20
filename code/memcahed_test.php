<?php

$bWorking = false;

try {
    $obMemcached = new Memcached();
    $obMemcached->addServer('memcached', 11211);
    $bWorking = true;
    
    $sVersionInfo = $obMemcached->getVersion();
    $sServerVersion = reset($sVersionInfo);
    
} catch (Exception $e) {
    $bWorking = false;
    $sErrorMessage = $e->getMessage();
}

if ($bWorking) {
    $obMemcached->set('php_test', 'Memcached версии ' . $sServerVersion . ' работает!');
    echo $obMemcached->get('php_test');
} else {
    echo "Memcached не работает! ";
    echo "Ошибка подключения: " . $sErrorMessage;
}

?>

<script>
setTimeout(function() {window.location.href = '/'; }, 3000);
</script>
