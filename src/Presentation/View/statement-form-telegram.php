<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запрос банковской выписки</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--tg-theme-bg-color, #f5f5f5);
            color: var(--tg-theme-text-color, #333);
        }
        .container {
            background: var(--tg-theme-secondary-bg-color, white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: var(--tg-theme-text-color, #333);
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--tg-theme-hint-color, #555);
        }
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--tg-theme-hint-color, #ddd);
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            background: var(--tg-theme-bg-color, white);
            color: var(--tg-theme-text-color, #333);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: var(--tg-theme-button-color, #007bff);
            color: var(--tg-theme-button-text-color, white);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: var(--tg-theme-button-color, #0056b3);
        }
        .info {
            background-color: var(--tg-theme-secondary-bg-color, #e7f3ff);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid var(--tg-theme-button-color, #007bff);
        }
        .error {
            background-color: #ffe7e7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            display: none;
        }
        .success {
            background-color: #e7ffe7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏦 Запрос банковской выписки</h1>
        
        <div class="info">
            <strong>Информация:</strong><br>
            Выписка будет сгенерирована в фоновом режиме и отправлена в Telegram.<br>
            Обработка может занять несколько минут.
        </div>

        <div class="error" id="error-message"></div>
        <div class="success" id="success-message"></div>

        <form id="statement-form">
            <div class="form-group">
                <label for="startDate">📅 Дата начала периода:</label>
                <input type="date" id="startDate" name="startDate" required>
            </div>

            <div class="form-group">
                <label for="endDate">📅 Дата окончания периода:</label>
                <input type="date" id="endDate" name="endDate" required>
            </div>

            <button type="submit">📋 Создать запрос выписки</button>
        </form>
    </div>

    <script>
        let tg = window.Telegram.WebApp;
        let chatId = null;

        if (tg && tg.initDataUnsafe) {
            tg.ready();
            
            // применяем тему Telegram
            tg.setHeaderColor('#007bff');
            tg.setBackgroundColor('#f5f5f5');

            if (tg.initDataUnsafe.user) {
                chatId = tg.initDataUnsafe.user.id;
            } else {
                try {
                    const initData = new URLSearchParams(tg.initData);
                    const userData = initData.get('user');
                    if (userData) {
                        const user = JSON.parse(decodeURIComponent(userData));
                        chatId = user.id;
                    }
                } catch (error) {
                    // игнорируем ошибки парсинга
                }
            }
        }

        document.getElementById('statement-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!chatId) {
                showError('❌ Ошибка: Не удалось получить Chat ID из Telegram. Откройте форму через бота.');
                return;
            }

            const formData = new FormData(this);
            const data = {
                startDate: formData.get('startDate'),
                endDate: formData.get('endDate'),
                telegramChatId: chatId.toString()
            };

            try {
                const response = await fetch('/api/statement-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    showSuccess('✅ Запрос создан успешно! Выписка будет отправлена в Telegram.');
                    this.reset();

                    if (tg && tg.MainButton) {
                        tg.MainButton.setText('Закрыть');
                        tg.MainButton.show();
                        tg.MainButton.onClick(() => {
                            tg.close();
                        });
                    }
                } else {
                    showError('❌ Ошибка: ' + (result.message || 'Неизвестная ошибка'));
                }
            } catch (error) {
                showError('❌ Ошибка сети: ' + error.message);
            }
        });

        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('success-message').style.display = 'none';
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('error-message').style.display = 'none';
        }

        // устанавливаем минимальную дату как сегодня
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('startDate').setAttribute('max', today);
        document.getElementById('endDate').setAttribute('max', today);
    </script>
</body>
</html>