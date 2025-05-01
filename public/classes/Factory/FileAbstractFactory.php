<?php

//namespace Factory;

interface  FileAbstractFactory
{
    public function createFolder(): AbstractFolder;

    public function createFile(): AbstractFile;
}