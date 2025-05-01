<?php

interface  AbstractFile  {
    public function display($indent = 0);
    //public function getSize():int;
    public function getFileExtension():string;
    //public function getFilePath():string;
}