<?php

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