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
    public function getSize():int;
}

interface FileAdapter {
    public function getFilePreview():string;
}

class File implements FileComponent {
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

    public function getSize():int
    {
        return filesize($this->filePath);
    }

    public function getFileExtension():string
    {
        $arFile = explode(".", $this->name);
        return array_pop($arFile);
    }


    public function getFilePath():string
    {
        return $this->filePath;
    }
}

class Folder implements FileComponent {
    private $name;
    private $fullPath;
    private $children = [];

    public function __construct(string $name, string $fullPath) {
        $this->name = $name;
        $this->fullPath = $fullPath;
    }

    public function add(FileComponent $component) {
        $this->children[] = $component;
    }

    public function getSize():int
    {
        $path = rtrim($this->fullPath, '/');
        $size = 0;
        $dir = opendir($path);
        if (!$dir) {
            return 0;
        }

        while (false !== ($file = readdir($dir))) {
            if ($file == '.' || $file == '..') {
                continue;
            } elseif (is_dir($path . $file)) {
                $size += dir_size($path . DIRECTORY_SEPARATOR . $file);
            } else {
                $size += filesize($path . DIRECTORY_SEPARATOR . $file);
            }
        }
        closedir($dir);
        return $size;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );

        echo $spaces . "<strong>" . $this->name . '</strong> ';
        $directorySizeInBytes = $this->getSize();
        echo ' ('.$directorySizeInBytes.' bytes)'.'<br />';


        foreach ($this->children as $child) {
            $sizeInBytes = $child->getSize();
            $child->display($indent + 2);
            if ($child instanceof File) {
                $fileExtension = $child->getFileExtension();
                if ($fileExtension == 'txt') {
                    $fileAdapter = new TxtAdapter($child);
                } elseif ($fileExtension == 'html') {
                    $fileAdapter = new HtmlAdapter($child);
                }

                $preview = $fileAdapter->getFilePreview();

                echo $spaces. $spaces. ' ('.$sizeInBytes.' bytes)'.'<br />';
                echo $spaces. $spaces. $preview .' '.$sizeInBytes.' bytes';
                echo '<br />';
            }
        }
    }
}


class TxtAdapter implements FileAdapter {
    private File $file;

    public function __construct(File $file) {
        $this->file = $file;
    }

    public function getFilePreview():string
    {
        $filePath = $this->file->getFilePath();
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found');
        }
        $content = file_get_contents($filePath);
        return substr($content, 0, 50);
    }
}


class HtmlAdapter implements FileAdapter {
    private File $file;

    public function __construct(File $file) {
        $this->file = $file;
    }

    public function getFilePreview():string
    {
        $filePath = $this->file->getFilePath();
        if (!file_exists($filePath)) {
            throw new RuntimeException('File not found');
        }
        $content = file_get_contents($filePath);
        $content = strip_tags($content);

        return substr($content, 0, 50);
    }
}

function buildTree($path) {
    $directoryName = basename($path);
    $directory = new Folder($directoryName, $path);

    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($fullPath)) {
            $directory->add(buildTree($fullPath));
        } else {
            //TODO сюда подключить адаптер и создавать экземляр нужного типа файла
            $directory->add(new File($item, $fullPath));
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