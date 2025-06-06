<?php


class GetFileProcess {

    public function __invoke() {

        sleep(5);
        echo "Файл сформирован ".date("Y:m:d H.i.s")."\n";

    }

}


