<?php

namespace Controllers;

class BaseController
{
    /**
     * @return array
     */
    protected function getRequestData(): array
    {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $putData = file_get_contents('php://input');
            $exploded = explode('&', $putData);
            foreach($exploded as $pair) {
                $item = explode('=', $pair);
                if (count($item) == 2) {
                    $data[urldecode($item[0])] = urldecode($item[1]);
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return $_POST;
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function getDataFiles(): array
    {
        return [
            'name' => $_FILES['file']['name'],
            'tmp' => $_FILES['file']['tmp_name'],
            'size' => $_FILES['file']['size']
        ];
    }
}