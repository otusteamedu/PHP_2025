<?php

namespace app\engine;

class Autoload
{

    public function loadClass($className) {

//        var_dump($className);
        $patterns = ["/app/","/\\\\/"];
        $replacements = ['..','/'];
        $path = preg_replace($patterns,$replacements, $className);
//        var_dump($path.'.php');
        include $path.'.php';
    }
}