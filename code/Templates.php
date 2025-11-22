<!DOCTYPE html>
<html>
<body>

    <form method="POST" action="">
        <label for="user_input">Введите адрес:</label>
        <input type="text" id="user_input" name="user_input" 
               value="<?php echo isset($_POST['user_input']) ? htmlspecialchars($_POST['user_input']) : ''; ?>">
        
        <button type="submit" name="submit">Проверить</button>
    </form>

    <?php if ($arOutput): ?>
    <div style="margin-top: 20px;">
        <h3>Результат проверки:</h3>
        <p><strong>Ввод:</strong> <?= $arOutput['sInput']; ?></p>
        <p><strong>Итог:</strong> <?= $arOutput['sResult']; ?></p>
    </div>
    <?php endif; ?>
</body>
</html>