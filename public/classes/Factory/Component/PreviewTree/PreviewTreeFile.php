<?php

class PreviewTreeFile implements FileComponent, AbstractFile {
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
