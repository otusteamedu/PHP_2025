<?php

/** @var string $title */
/** @var string $content */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        body {
            margin: 0;
            background: #f4f7fb;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        main {
            width: min(720px, calc(100% - 32px));
            margin: 48px auto;
            background: #fff;
            border: 1px solid #d8e0ec;
            border-radius: 8px;
            padding: 28px;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 28px;
        }

        label {
            display: block;
            margin: 16px 0 6px;
            font-weight: 700;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            border: 1px solid #b9c4d6;
            border-radius: 6px;
            font-size: 16px;
            padding: 10px 12px;
        }

        button {
            margin-top: 22px;
            border: 0;
            border-radius: 6px;
            background: #2357a6;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            padding: 12px 18px;
        }

        .errors {
            background: #fff0f0;
            border: 1px solid #e5aaaa;
            border-radius: 6px;
            color: #8a1f1f;
            padding: 12px 16px;
        }

        .hint {
            color: #5c6778;
            font-size: 14px;
            margin-top: 4px;
        }

        .success {
            background: #edf8f0;
            border: 1px solid #afd9ba;
            border-radius: 6px;
            color: #215a2f;
            padding: 14px 16px;
        }
    </style>
</head>
<body>
<main>
    <?= $content ?>
</main>
</body>
</html>
