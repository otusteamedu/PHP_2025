<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Highloadblock as HL;
CModule::IncludeModule('highloadblock');
Loc::loadMessages(__FILE__);

class telegram_bot_access extends CModule
{
    public $MODULE_ID = 'telegram.bot.access';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $errors;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage('TELEGRAM_BOT_ACCESS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('TELEGRAM_BOT_ACCESS_MODULE_DESC');
        $this->PARTNER_NAME = Loc::getMessage('TELEGRAM_BOT_ACCESS_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('TELEGRAM_BOT_ACCESS_PARTNER_URI');
    }

    public function DoInstall()
    {
        global $APPLICATION;
        
        if ($this->isVersionD7()) {
            RegisterModule($this->MODULE_ID);
            
            if (!$this->createHighloadBlock()) {
                $APPLICATION->ThrowException($this->errors);
                return false;
            }
            
            $this->InstallFiles();
            $this->InstallEvents();
        } else {
            $APPLICATION->ThrowException(Loc::getMessage('TELEGRAM_BOT_ACCESS_INSTALL_ERROR_VERSION'));
            return false;
        }
        
        
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        
       
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . '/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin',
            true, true
        );
        return true;
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        return true;
    }

    public function InstallEvents() { return true; }
    public function UnInstallEvents() { return true; }

    private function isVersionD7()
    {
        return CheckVersion(Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    private function createHighloadBlock()
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();
        
        // Создаем HL-блок
        $result = HL\HighloadBlockTable::add([
            'NAME' => 'TelegramBotAccess',
            'TABLE_NAME' => 'telegram_bot_access'
        ]);
        
        if (!$result->isSuccess()) {
            $this->errors = implode(', ', $result->getErrorMessages());
            return false;
        }
        
        $hlBlockId = $result->getId();
        
        // Создаем поля
        $userField = new CUserTypeEntity();
        
        $fields = [
            [
                'ENTITY_ID' => 'HLBLOCK_' . $hlBlockId,
                'FIELD_NAME' => 'UF_USER_ID',
                'USER_TYPE_ID' => 'integer',
                'XML_ID' => 'USER_ID',
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL' => ['ru' => 'ID пользователя'],
                'LIST_COLUMN_LABEL' => ['ru' => 'ID пользователя'],
                'LIST_FILTER_LABEL' => ['ru' => 'ID пользователя']
            ],
            [
                'ENTITY_ID' => 'HLBLOCK_' . $hlBlockId,
                'FIELD_NAME' => 'UF_USER_FIO',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'USER_FIO',
                'SORT' => 200,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL' => ['ru' => 'ФИО пользователя'],
                'LIST_COLUMN_LABEL' => ['ru' => 'ФИО пользователя'],
                'LIST_FILTER_LABEL' => ['ru' => 'ФИО пользователя']
            ],
            [
                'ENTITY_ID' => 'HLBLOCK_' . $hlBlockId,
                'FIELD_NAME' => 'UF_TELEGRAM_LOGIN',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => 'TELEGRAM_LOGIN',
                'SORT' => 300,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'EDIT_FORM_LABEL' => ['ru' => 'Логин Telegram'],
                'LIST_COLUMN_LABEL' => ['ru' => 'Логин Telegram'],
                'LIST_FILTER_LABEL' => ['ru' => 'Логин Telegram']
            ],
            [
                'ENTITY_ID' => 'HLBLOCK_' . $hlBlockId,
                'FIELD_NAME' => 'UF_DATE_CREATE',
                'USER_TYPE_ID' => 'datetime',
                'XML_ID' => 'DATE_CREATE',
                'SORT' => 400,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'N',
                'DEFAULT_VALUE' => ['TYPE' => 'NOW'],
                'EDIT_FORM_LABEL' => ['ru' => 'Дата добавления'],
                'LIST_COLUMN_LABEL' => ['ru' => 'Дата добавления']
            ]
        ];
        
        foreach ($fields as $field) {
            $userField->Add($field);
        }
        
        return true;
    }
}
?>