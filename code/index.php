<?php

declare(strict_types=1);

require_once 'CheckMail.php';

use Dkeruntu\OtusHomeWorkFiveCheckMail\CheckMail;

$obCheckMail = new CheckMail();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $arError["bError"] = false;

    $sInput = htmlspecialchars($_POST['user_input'] ?? '');

    $arCheckMethods = [
        'CheckEmpty',
        'ChekcStreln', 
        'CheckDogCount',
        'ChekcValid',
        'CheckDomain',
        'CheckMX'
    ];

    foreach ($arCheckMethods as $sMethod) {
        if (!$arError['bError']) {
            $arResult = $obCheckMail->$sMethod($sInput);
            
            if ($arResult['bError']) {
                $sResult = $arResult['bErrorDiscription'];
                break; 
            }
        }
    }


    if($arError['bError']){
        $sResult = "Ошибка: " .  $arError["bErrorDiscription"];
    }

}

?>

<!DOCTYPE html>
<html>
<body>

    <form method="POST" action="">
        <label for="user_input">Введите адрес:</label>
        <input type="text" id="user_input" name="user_input" 
               value="<?php echo isset($_POST['user_input']) ? htmlspecialchars($_POST['user_input']) : ''; ?>">
        
        <button type="submit" name="submit">Проверить</button>
    </form>

    <?php if (isset($sResult)): ?>
    <div style="margin-top: 20px;">
        <h3>Результат проверки:</h3>
        <p><strong>Ввод:</strong> <?php echo $sInput; ?></p>
        <p><strong>Итог:</strong> <?php echo $sResult; ?></p>
    </div>
    <?php endif; ?>
</body>
</html>