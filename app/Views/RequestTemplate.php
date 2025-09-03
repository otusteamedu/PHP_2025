<?php

namespace App\Views;

class RequestTemplate extends Template
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
        form {
            width: 90%;
            max-width: 450px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid gray;
            border-radius: 10px;
        }
        h1 {
            width: 90%;
            max-width: 450px;
            margin: 40px auto 20px;
            text-align: center;
        }
        label {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            margin-bottom: 20px;
        }
        input {
            margin-left: 20px;
        }
        button {
            height: 40px;
            padding: 0 20px;
            font-size: 16px;
        }
        div {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Заказ банковской выписки</h1>

    <form action="/" method="post">
        <label>
            Дата начала
            <input type="date" name="date_start" required>
        </label>

        <label>
            Дата окончания
            <input type="date" name="date_finish" required>
        </label>

        <label>
            Email
            <input type="email" name="email" required>
        </label>

        <div>
            <button>Получить банковскую выписку</button>
        </div>
    </form> 
</body>
</html>
DOC;        
        return $content;
    }
}