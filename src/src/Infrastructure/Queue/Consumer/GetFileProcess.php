<?php


class GetFileProcess {

    public function __invoke() {

        sleep(20);
        echo "Файл сформирован ".date("Y:m:d H.i.s")."\n";

    }

}


