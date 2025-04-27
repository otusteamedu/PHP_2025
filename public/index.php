<?php

/***
This function will read the full structure of a directory. It's recursive becuase it doesn't stop with the one directory, it just keeps going through all of the directories in the folder you specify.

http://www.codingforums.com/showthread.php?t=71882
 ***/

//TODO читаем и реализуем: https://refactoring.guru/ru/design-patterns/composite/php/example
//https://www.youtube.com/watch?v=ZCNQ7xsed58
//https://habr.com/ru/articles/149570/


$storgeDirectory = $_SERVER['DOCUMENT_ROOT'].'/storage/';

getDirectory($storgeDirectory);


function getDirectory( $path = '.', $level = 0 ) {

    $ignore = array( 'cgi-bin', '.', '..' );
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.

    $dh = @opendir( $path );
    // Open the directory to the handle $dh

    while( false !== ( $file = readdir( $dh ) ) ){
        // Loop through the directory

        if( !in_array( $file, $ignore ) ){
            // Check that this file is not to be ignored

            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) );
            // Just to add spacing to the list, to better
            // show the directory tree.

            if( is_dir( "$path/$file" ) ){
                // Its a directory, so we need to keep reading down...

                echo "<strong>$spaces $file</strong><br />";
                getDirectory( "$path/$file", ($level+1) );
                // Re-call this same function but on a new directory.
                // this is what makes function recursive.
            } else {
                echo "$spaces $file<br />";
                // Just print out the filename
            }

        }

    }

    closedir( $dh );
    // Close the directory handle

}


function vardump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}


?>