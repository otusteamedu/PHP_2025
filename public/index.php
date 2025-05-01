<?php

require_once '../vendor/autoload.php';

use App\Classes\App;

$commandsNameSpace = 'App\\Classes\\Commands\\';
$argv = $_SERVER['argv'] ?? [];

try {
    $app = new App($commandsNameSpace, $argv);
    $app->run();
}

catch (Exception $e) {
    print_r($e->getMessage());
}


//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/FileAdapter.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/TxtAdapter.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/HtmlAdapter.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/FileComponent.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/FileAbstractFactory.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/Abstract/AbstractFile.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/Abstract/AbstractFolder.php');
//
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFile.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFolder.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFactory.php');
//
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFile.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFolder.php');
//require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFactory.php');
//
//
//$storgeDirectory = $_SERVER['DOCUMENT_ROOT'].'/storage/';
//
//
////TODO добавить в класс APP




//$rootPath = $storgeDirectory;
//$tree = buildTree($rootPath, 'preview');
//$tree->display();


//
//exit();
//
function pr_debug($var)
{
    //if ($_REQUEST['deb'] == 'Y') {
    $bt = debug_backtrace();
    $bt = $bt[0];
    ?>
    <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
        <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?= $bt["file"] ?>
            [<?= $bt["line"] ?>]
        </div>
        <?
        if ($var === 0) {
            echo '<pre>пусто</pre>';
            var_dump($var);
        } else {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
        ?>
    </div>
    <?
    //exit();
    //}
}


?>