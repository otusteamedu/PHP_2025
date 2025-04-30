<?php

/***
This function will read the full structure of a directory. It's recursive becuase it doesn't stop with the one directory, it just keeps going through all of the directories in the folder you specify.

http://www.codingforums.com/showthread.php?t=71882
 ***/

//TODO читаем и реализуем: https://refactoring.guru/ru/design-patterns/composite/php/example
//https://www.youtube.com/watch?v=ZCNQ7xsed58
//https://habr.com/ru/articles/149570/


$storgeDirectory = $_SERVER['DOCUMENT_ROOT'].'/storage/';


interface FileComponent {
    public function display($indent = 0);
}

interface FileHandler {
    public function getFilePreview():string;
}


class TxtFile implements FileComponent, FileHandler {
    private $name;
    private $filePath;

    public function __construct(string $name, string $filePath) {
        $this->name = $name;
        $this->filePath = $filePath;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );
        echo $spaces. "- " . $this->name . '<br />';
    }

    public function getFilePreview():string
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException('File not found');
        }
        $content = file_get_contents($this->filePath);
        return substr($content, 0, 50);
    }
}


//class HtmlFile implements FileComponent {
//    private $name;
//
//    public function __construct($name) {
//        $this->name = $name;
//    }
//
//    public function display($indent = 0) {
//        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );
//        echo $spaces. "- " . $this->name . '<br />';
//    }
//}

class Folder implements FileComponent {
    private $name;
    private $children = [];

    public function __construct($name) {
        $this->name = $name;
    }

    public function add(FileComponent $component) {
        $this->children[] = $component;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );

        echo $spaces . "<strong>" . $this->name . '</strong><br />';
        foreach ($this->children as $child) {
            $child->display($indent + 2);
            if ($child instanceof FileHandler) {
                echo $spaces;
                echo $child->getFilePreview();
                echo '<br />';
            }
        }
    }
}

function buildTree($path) {
    $directory = new Folder(basename($path));

    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $directory->add(buildTree($fullPath));
        } else {
            //TODO сюда подключить адаптер и создавать экземляр нужного типа файла
            $directory->add(new TxtFile($item, $fullPath));
        }
    }

    return $directory;
}

$rootPath = $storgeDirectory;
$tree = buildTree($rootPath);
$tree->display();






//$file = "/var/www/app/public/storage//games/strategy/land_lords.txt";
//$file = "/var/www/app/public/storage/games/strategy/start_wars.html";
//$preview = getFilePreview($file, 50);
//vardump($preview);
//getDirectory($storgeDirectory);

exit();



//interface FileHandlerInterface
//{
//    public function getFilePreview(): string;
//}
//
//class FileHandler implements FileHandlerInterface
//{
//    private string $file;
//    private int $length;
//
//    public function __construct(string $file, int $length)
//    {
//        $this->file = $file;
//        $this->length = $length;
//    }
//
//    public function getFilePreview():string
//    {
//        if (!file_exists($this->file)) {
//            throw new RuntimeException('File not found');
//        }
//        $content = file_get_contents($this->file);
//        return substr($content, 0, $this->length);
//    }
//}

function getFilePreview(string $file, int $length)
{
    if (!file_exists($file)) {
        throw new RuntimeException('File not found');
    }
    $content = file_get_contents($file);

    $fileExtension = getFileExtension($file);

//    if ($fileExtension == 'html') {
//        $content = strip_tags($content);
//    }

    return substr($content, 0, $length);
}

function getFileExtension(string $file):string
{
    $arFile = explode(".", $file);
    return array_pop($arFile);
}


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