<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/FileAdapter.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/TxtAdapter.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Adapter/HtmlAdapter.php');

require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/FileComponent.php');

require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/FileAbstractFactory.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/Abstract/AbstractFile.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/Abstract/AbstractFolder.php');

require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFile.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFolder.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/BaseTree/BaseTreeFactory.php');

require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFile.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFolder.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/classes/Factory/Component/PreviewTree/PreviewTreeFactory.php');


$storgeDirectory = $_SERVER['DOCUMENT_ROOT'].'/storage/';


//TODO добавить в класс APP
function buildTree($path, $mode = 'base') {
    $directoryName = basename($path);

    if ($mode == 'base') {
        $treeFactory = new BaseTreeFactory();
        $directory = $treeFactory->createTreeFolder($directoryName);
    } else if ($mode == 'preview') {
        $treeFactory = new PreviewTreeFactory();
        $directory = $treeFactory->createTreeFolder($directoryName, $path);
    }

    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $directory->add(buildTree($fullPath, $mode));
        } else {
            //TODO сюда подключить адаптер и создавать экземляр нужного типа файла
            //$directory->add(new File($item, $fullPath));
            $file = $treeFactory->createTreeFile($item, $fullPath);
            $directory->add($file);
        }
    }

    return $directory;
}

$rootPath = $storgeDirectory;
$tree = buildTree($rootPath, 'preview');
$tree->display();

exit();

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