<?php
$bWorking = false;

try {$obPdo = new PDO('mysql:host=db;dbname=mydb', 'user', 'password');
    $obPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bWorking = true;
    $sVersion = $obPdo->getAttribute(PDO::ATTR_SERVER_VERSION);

} catch (PDOException $e) {
    $bWorking = false;
    $sErrorMessage = $e->getMessage();

}

if ($bWorking) {
    echo "MySQL версии " . $sVersion . " работает! ";
} else {
    echo "База данных не работает! ";
    echo "Ошибка подключения к БД: " . $sErrorMessage;
}
?>

<script>
setTimeout(function() {window.location.href = '/'; }, 3000);
</script>
