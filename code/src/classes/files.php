<?php
namespace Classes;

use Classes\my_function;

class files
{
    private $file;
    private $type;
    private $size;

    public function __construct($file)
    {
        $this->file = $file;
        $this->size = filesize($this->file);
        $this->type = filetype($this->file);
    }

//  вывод содержимого файла на экран
    public function read()
    {
        $data =  file_get_contents($this->file);
        return $data;
    }

}