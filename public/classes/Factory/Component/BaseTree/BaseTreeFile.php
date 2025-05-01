<?php

class BaseTreeFile implements FileComponent, AbstractFile {
    private $name;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );
        echo $spaces. "- " . $this->name . '<br />';
    }

    public function getFileExtension():string
    {
        $arFile = explode(".", $this->name);
        return array_pop($arFile);
    }
}
