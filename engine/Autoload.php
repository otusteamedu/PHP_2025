<?php

namespace app\engine;

class Autoload
{

    public function loadClass($className) {

        var_dump($className);
        $patterns = ["/app/","/\\\\/"];
        $replacement = ['..','/'];
        //var_dump(preg_quote($pattern));
        $path = preg_replace($patterns,$replacement, $className);
        var_dump($path.'.php');
        include $path.'.php';
    }
}