<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars((string) $title ?? '') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 40px; max-width: 820px; }
        .card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; }
        .row { margin: 10px 0; }
        label { display: block; margin-bottom: 4px; }
        input { padding: 10px; width: 100%; box-sizing: border-box; }
        button { padding: 10px 14px; cursor: pointer; }
        .error { color: #b91c1c; margin: 10px 0; }
        small { color:#6b7280; }
    </style>
</head>
<body>
<h1><?= htmlspecialchars((string) $title ?? '') ?></h1>
<div class="card">
    <?= $content ?? '' ?>
</div>
</body>
</html>
