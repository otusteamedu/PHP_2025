<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Telegram\Bot\Access\UserManager;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';

Loader::includeModule('telegram.bot.access');
Loader::includeModule('highloadblock');

$userManager = new UserManager();
$request = Application::getInstance()->getContext()->getRequest();

// // Обработка добавления пользователя
// if ($request->isPost() && $request->getPost('add_user') && check_bitrix_sessid()) {
//     $userId = (int)$request->getPost('user_id');
//     $userFio = trim($request->getPost('user_fio'));
//     $telegramLogin = trim($request->getPost('telegram_login'));
    
//     if ($userId > 0 && !empty($userFio) && !empty($telegramLogin)) {
//         $result = $userManager->addUser($userId, $userFio, $telegramLogin);
//         if ($result['success']) {
//             $message = 'Пользователь успешно добавлен';
//         } else {
//             $error = 'Ошибка: ' . $result['error'];
//         }
//     } else {
//         $error = 'Заполните все поля';
//     }
// }
// AJAX обработчик для получения данных пользователя
if ($request->isAjaxRequest() && $request->get('action') == 'get_user') {
    $userId = (int)$request->get('user_id');
    $user = \CUser::GetByID($userId)->Fetch();
    
    if ($user) {
        $result = [
            'success' => true,
            'data' => [
                'NAME' => trim($user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME']),
                'UF_TELEGRAM_LOGIN' => $user['UF_TELEGRAM_LOGIN'] ?? ''
            ]
        ];
    } else {
        $result = ['success' => false, 'error' => 'Пользователь не найден'];
    }
    
    header('Content-Type: application/json');
    echo Json::encode($result);
    die();
}

// Добавление пользователя
if ($request->isPost() && $request->getPost('user_selector') && check_bitrix_sessid()) {
    $userId = (int) str_replace('U', '', $request->getPost('user_selector'));
    
    if ($userId > 0) {
        $user = \CUser::GetByID($userId)->Fetch();
        if ($user) {
            $userFio = trim($user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME']);
            $telegramLogin = $user['UF_TELEGRAM_LOGIN'] ?? '';
            
            if (!empty($telegramLogin)) {
                $result = $userManager->addUser($userId, $userFio, $telegramLogin);
                if ($result['success']) {
                    $message = 'Пользователь добавлен';
                } else {
                    $error = $result['error'];
                }
            } else {
                $error = 'У пользователя не указан логин Telegram (поле UF_TELEGRAM_LOGIN)';
            }
        } else {
            $error = 'Пользователь не найден';
        }
    } else {
        $error = 'Выберите пользователя'.$userId;
    }
}


// Обработка удаления пользователя
if ($request->getQuery('delete') && check_bitrix_sessid()) {
    $recordId = (int)$request->getQuery('delete');
    $result = $userManager->deleteUser($recordId);
    
    if ($result['success']) {
        $message = 'Пользователь успешно удален';
    } else {
        $error = 'Ошибка: ' . $result['error'];
    }
    
    LocalRedirect('/bitrix/admin/settings.php?lang=ru&mid=telegram.bot.access&mid_menu=1');
}

// Обработка сохранения настроек API
if ($request->isPost() && $request->getPost('save_api_settings') && check_bitrix_sessid()) {
    $apiUrl = trim($request->getPost('api_url'));
    $apiToken = trim($request->getPost('api_token'));
    
    $userManager->updateApiSettings($apiUrl, $apiToken);
    $message = 'Настройки API сохранены';
}

// Получаем список пользователей
$users = $userManager->getAllUsers();

// Получаем текущие настройки API
$apiUrl = \Bitrix\Main\Config\Option::get('telegram.bot.access', 'api_url', '');
$apiToken = \Bitrix\Main\Config\Option::get('telegram.bot.access', 'api_token', '');

$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => "Разрешенные пользователи",
        "ICON" => "",
        "TITLE" => "Управление разрешенными пользователями Telegram бота"
    ],
    [
        "DIV" => "edit2",
        "TAB" => "Настройки API",
        "ICON" => "",
        "TITLE" => "Настройки внешнего API"
    ]
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?php if (isset($message)): ?>
    <?php CAdminMessage::ShowMessage(['MESSAGE' => $message, 'TYPE' => 'OK']); ?>
<?php endif; ?>

<?php if (isset($error)): ?>
    <?php CAdminMessage::ShowMessage(['MESSAGE' => $error, 'TYPE' => 'ERROR']); ?>
<?php endif; ?>

<form method="POST" action="">
    <?= bitrix_sessid_post(); ?>
    <input type="hidden" name="add_user" value="1">
    
    <?php $tabControl->BeginNextTab(); ?>
    
    <tr>
        <td width="40%">Выберите пользователя:</td>
        <td width="60%">
            <div id="user-selector-container" style="margin-bottom: 10px;">
                <?php
                // Используем стандартный компонент выбора пользователей
                $APPLICATION->IncludeComponent(
                    'bitrix:main.user.selector',
                    '',
                    [
                        'ID' => 'USER_SELECTOR',
                        'INPUT_NAME' => 'user_selector',
                        'LIST' => [],
                        'SHOW_EXTRANET_USERS' => 'N',
                        'EXTERNAL' => 'Y',
                        'USE_SYMBOLIC_ID' => 'Y',
                        'API_VERSION' => 3,
                        'SELECTOR_OPTIONS' => [
                            'context' => 'TELEGRAM_BOT_ACCESS',
                            'contextCode' => 'U',
                            'enableAll' => 'N',
                            'enableUsers' => 'Y',
                            'enableDepartments' => 'N',
                            'enableSonetgroups' => 'N',
                            'allowEmailInvitation' => 'N',
                            'allowSearchEmailUsers' => 'N',
                            'departmentSelectDisable' => 'Y',
                            'enableCrm' => 'N',
                            'addTabCrmContacts' => 'N',
                            'addTabCrmCompanies' => 'N',
                            'addTabCrmLeads' => 'N',
                            'addTabCrmDeals' => 'N',
                            'multiple' => 'N',
                            'useClientDatabase' => 'Y'
                        ]
                    ]
                );
                ?>
            </div>
            <div id="selected-user-info" style="display: none; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; margin-top: 10px;">
                <strong>Выбран пользователь:</strong>
                <span id="selected-user-name"></span>
                <br>
                <strong>Логин Telegram:</strong>
                <span id="selected-telegram-login"></span>
            </div>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="submit" value="Добавить в разрешенные" class="adm-btn-save" id="submit-btn">
        </td>
    </tr>
    
    <?php $tabControl->EndTab(); ?>
    
    <?php $tabControl->BeginNextTab(); ?>
    
    <tr>
        <td width="40%">URL API:</td>
        <td width="60%">
            <input type="text" name="api_url" value="<?= htmlspecialchars($apiUrl) ?>" style="width: 400px;">
        </td>
    </tr>
    <tr>
        <td>Токен API (если требуется):</td>
        <td>
            <input type="password" name="api_token" value="<?= htmlspecialchars($apiToken) ?>" style="width: 400px;">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="submit" name="save_api_settings" value="Сохранить настройки API" class="adm-btn-save">
        </td>
    </tr>
    
    <?php $tabControl->EndTab(); ?>
</form>
<script>
  const input = document.getElementById('user_selector');
    // const button = document.getElementById('authorize');

    // input.addEventListener('change', () => {
    //     alert(input.value);
    // })
</script>
<?php $tabControl->End(); ?>

<h2>Список разрешенных пользователей</h2>
<table class="internal" style="width: 100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>ID пользователя</th>
            <th>ФИО</th>
            <th>Telegram логин</th>
            <th>Дата добавления</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['ID'] ?></td>
                <td><?= $user['UF_USER_ID'] ?></td>
                <td><?= htmlspecialchars($user['UF_USER_FIO']) ?></td>
                <td><?= htmlspecialchars($user['UF_TELEGRAM_LOGIN']) ?></td>
                <td><?= $user['UF_DATE_CREATE'] ?></td>
                <td>
                    <a href="?lang=ru&mid=telegram.bot.access&mid_menu=1&delete=<?= $user['ID'] ?>&sessid=<?= bitrix_sessid() ?>" 
                       onclick="return confirm('Удалить пользователя?')">
                        Удалить
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
            <tr>
                <td colspan="6" style="text-align: center;">Нет данных</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
// AJAX обработчик для поиска пользователей
if ($request->isAjaxRequest() && $request->get('action') == 'search_user') {
    $search = trim($request->get('search'));
    $result = [];
    
    if (strlen($search) >= 2) {
        $filter = [
            'ACTIVE' => 'Y',
            '%NAME' => $search
        ];
        
        $rsUsers = \CUser::GetList(
            ($by = 'ID'),
            ($order = 'DESC'),
            $filter,
            ['SELECT' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL', 'UF_TELEGRAM_LOGIN']]
        );
        
        while ($user = $rsUsers->Fetch()) {
            $result[] = [
                'ID' => $user['ID'],
                'NAME' => trim($user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME']),
                'EMAIL' => $user['EMAIL'],
                'UF_TELEGRAM_LOGIN' => $user['UF_TELEGRAM_LOGIN'] ?? ''
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo Json::encode(['success' => true, 'data' => $result]);
    die();
}
?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php'; ?>