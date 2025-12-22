<!DOCTYPE html>
<html>
<head>
    <title>Homework 15</title>
    <style>
        html,
        body {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
        }
        main {
            max-width: 500px;
            margin: 0 auto;
            font-size: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
    </style>
</head>
<body>

<main>
    <?php if (!isset($response)) { ?>
        <form action="/" enctype="multipart/form-data" method="post">
            <label>Date from
                <input type="date" name="dateFrom" required>
            </label>
            <label>Date to
                <input type="date" name="dateTo" required>
            </label>
            <label>Email
                <input type="email" name="email" required>
            </label>
            <button type="submit">Отправить</button>
        </form>
    <?php } else { ?>
        <?php echo $response->getMessage(); ?>
    <?php } ?>
</main>
</body>
</html>