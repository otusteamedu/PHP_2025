<!DOCTYPE html>
<html>
<head>
    <title>Проверка скобок</title>
</head>
<body>
    <h1>Информация по контейнерам</h1>
   
    <div>
        <p>Балансировщик: <strong><?= $arContainerInfo['balancer'] ?></strong></p>
        <p>Бекенд: <strong><?= $arContainerInfo['backend'] ?></strong></p>
    </div>

    <h1>Информация по обработке</h1>

    <?php if ($arValidationResult['bError']): ?>
        <?php if ($sInputString !== null): ?>
            <p>Метод: <?= $this->obHttpWorker->getRequestMethod() ?></p>
            <p>На вход поступило: <?= $sInputString ?></p>
        <?php endif; ?>
        <div>
            <h2>Обнаружены ошибки:</h2>
                <p><?= $arValidationResult['bErrorDescription'] ?></p>
        </div>
    <?php else: ?>
        <p>На вход поступило: <?= $sInputString ?></p>
        <div>
            <h2>Все окей</h2>
            <p>Скобки расставлены правильно</p>
        </div>
    <?php endif; ?>
  

</body>
</html>