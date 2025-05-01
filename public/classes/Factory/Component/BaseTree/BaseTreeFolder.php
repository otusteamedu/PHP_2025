<?php

class BaseTreeFolder implements FileComponent, AbstractFolder {
    private $name;
    private $children = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function add(FileComponent $component) {
        $this->children[] = $component;
    }

    public function display($indent = 0) {
        $spaces = str_repeat( '&nbsp;', ( $indent * 4 ) );
        echo $spaces . "<strong>" . $this->name . '</strong> ';
        echo '<br />';
        foreach ($this->children as $child) {
            $child->display($indent + 2);
        }
    }
}