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

class File implements FileComponent {
    private $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );
        echo $spaces. "- " . $this->name . '<br />';
    }
}

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
            $directory->add(new File($item));
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





function getDirectory( $path = '.', $level = 0 ) {

    $ignore = array( 'cgi-bin', '.', '..' );
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.

    $dh = @opendir( $path );
    // Open the directory to the handle $dh

    while( false !== ( $file = readdir( $dh ) ) ){
        // Loop through the directory

        if( !in_array( $file, $ignore ) ){
            // Check that this file is not to be ignored

            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) );
            // Just to add spacing to the list, to better
            // show the directory tree.

            if( is_dir( "$path/$file" ) ){
                // Its a directory, so we need to keep reading down...

                echo "<strong>$spaces $file</strong><br />";
                getDirectory( "$path/$file", ($level+1) );
                // Re-call this same function but on a new directory.
                // this is what makes function recursive.
            } else {

                vardump("$path/$file");
                break;

                echo "$spaces $file<br />";
                // Just print out the filename
            }

        }

    }

    closedir( $dh );
    // Close the directory handle

}

function getFilePreview(string $file, int $length)
{
    if (!file_exists($file)) {
        throw new RuntimeException('File not found');
    }
    $content = file_get_contents($file);

    $fileExtension = getFileExtension($file);

    if ($fileExtension == 'html') {
        $content = strip_tags($content);
    }

    return substr($content, 0, $length);
}

function getFileExtension(string $file):string
{
    $arFile = explode(".", $file);
    return array_pop($arFile);
}


function vardump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}


?>