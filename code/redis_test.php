<?php
$bWorking = false;

try {
    $obRedis = new Redis();
    $bWorking = $obRedis->connect('redis', 6379);
    
    if ($bWorking) {
        $arRedisInfo = $obRedis->info();
        $sRedisVersion = $arRedisInfo['redis_version'];
    }
    
} catch (Exception $e) {
    $bWorking = false;
    $sErrorMessage = $e->getMessage();
}

if ($bWorking) {
    $obRedis->set('php_test', 'Redis версии ' . $sRedisVersion . ' работает!');
    echo $obRedis->get('php_test');
    $obRedis->close();
} else {
    echo "Redis не работает! ";
    if (isset($sErrorMessage)) {
        echo "Ошибка подключения: " . $sErrorMessage;
    } else {
        echo "Не удалось подключиться к серверу Redis";
    }
}
?>

<script>
setTimeout(function() {window.location.href = '/'; }, 3000);
</script>
