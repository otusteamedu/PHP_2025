<?php

namespace App\Views;

class NoteTemplate extends Template
{
    public function render(): string
    {
        $content = <<<DOC
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ банковской выписки</title>
    <style>
        body {
            font: 16px arial;
        }
        h1, div {
            width: 90%;
            max-width: 450px;
            margin: 40px auto 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Заявка получена</h1>
    <div>
        Вы заказали банковскую выписку за {$this->data['beginDate']} - {$this->data['endDate']}. 
        Выша заявка обрабатывается. Выписка будет отправлена на указанный Email
    </div>
</body>
</html>
DOC;        
        return $content;
    }
}