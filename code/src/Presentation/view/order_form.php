<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Интернет-ресторан</title>
</head>
<body>
    <h1>Сделать заказ</h1>
    <form method="POST">
        <div>
            <p>Выберите продукты:</p>
            <input type="checkbox" id="burger" name="product[]" value="Burger">
            <label for="burger">Лучший бургер</label><br>
            <input type="checkbox" id="sandwich" name="product[]" value="Sandwich">
            <label for="sandwich">Сочный бутерброд</label><br>
            <input type="checkbox" id="hotdog" name="product[]" value="HotDog">
            <label for="hotdog">Горячая сосиска в тесте</label><br>
            <input type="checkbox" id="pizza" name="product[]" value="Pizza">
            <label for="pizza">Пицца <span style="color: brown; margin-left: 2px;"> NEW </span></label><br>
        </div>
        <br>
        <div>
            <label for="custom_ingredients">Добавьте свои ингредиенты (через запятую):</label>
            <input type="text" id="custom_ingredients" name="custom_ingredients">
        </div>
        <br>
        <div>
            <p>Выберите тип уведомления:</p>
            <input type="radio" id="push" name="notification_type" value="Push" checked>
            <label for="push">Push-уведомление</label><br>
            <input type="radio" id="sms" name="notification_type" value="Sms">
            <label for="sms">SMS-уведомление</label><br>
        </div>
        <br>
        <button type="submit">Заказать</button>
    </form>

    <?php if (!empty($messages)): ?>
        <h2>Order processing:</h2>
        <?php foreach ($messages as $message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
        <p>Final product: <?php echo htmlspecialchars($finalProduct); ?></p>
        <p>Total price: <?php echo htmlspecialchars($totalPrice); ?></p>
    <?php endif; ?>
</body>
</html>
