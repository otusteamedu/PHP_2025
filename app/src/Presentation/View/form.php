<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Получение банковской выписки</title>
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px;
            background: #f3f3f3;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,.1);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            text-align: center;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .success {
            color: #155724;
            background: #d4edda;
        }

        .error {
            color: #721c24;
            background: #f8d7da;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            cursor: pointer;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Формирование выписки</h1>
        <div id="success-message" class="message success" style="display:none"></div>
        <div id="error-message" class="message error" style="display:none"></div>
        <form id="statement-form">
            <div class="field">
                <label for="email">
                    Email для уведомления
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="example@mail.com"
                    required
                >
            </div>
            <button type="submit">
                Получить выписку за год
            </button>
        </form>
    </div>
    <script>
        const form = document.getElementById('statement-form');

        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            const formData = new FormData(form);

            try {
                const response = await fetch('/queue.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    successMessage.textContent = result.message;
                    successMessage.style.display = 'block';

                    form.reset();
                } else {
                    errorMessage.textContent = result.message;
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                errorMessage.textContent = 'Ошибка соединения с сервером';
                errorMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>