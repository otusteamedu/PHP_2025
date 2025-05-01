<?php

interface AbstractFolder {
    public function add(FileComponent $component);
    //public function getSize():int;
    public function display($indent = 0);
}