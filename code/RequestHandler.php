<?php

namespace App;

class RequestHandler
{
    public function processRequest(array $postData): array
    {
        // проверяем на ошибки на уровне параметров запроса: наличие параметра string
        if (!isset($postData['string'])) {
            throw new \InvalidArgumentException('Параметр string не найден');
        }

        // ... и то что string не пустой
        if (trim(strlen($postData['string'] === 0))) {
            throw new \InvalidArgumentException('Параметр string пустой');
        }
        
        //остальные проверки на уровене содержимого запроса: согласованность скобочек
        $validator = new BracketValidator(trim($postData['string'])); 
        return $validator->isValid();
    }
}
