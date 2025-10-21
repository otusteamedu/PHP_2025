<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генерация банковской выписки</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 12px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Запрос банковской выписки</h1>
    
    <div id="message" class="message"></div>
    
    <form id="statementForm">
        <div class="form-group">
            <label for="email">Email для отправки выписки:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="account_number">Номер счета:</label>
            <input type="text" id="account_number" name="account_number" required>
        </div>
        
        <div class="form-group">
            <label for="start_date">Дата начала периода:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        
        <div class="form-group">
            <label for="end_date">Дата окончания периода:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        
        <button type="submit">Сгенерировать выписку</button>
    </form>

    <script>
        document.getElementById('statementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');
            
            fetch('/generate-statement', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message + ' ID запроса: ' + data.request_id;
                    messageDiv.style.display = 'block';
                    document.getElementById('statementForm').reset();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Ошибка: ' + error.message;
                messageDiv.style.display = 'block';
            });
        });

        const today = new Date();
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1);
        
        document.getElementById('start_date').valueAsDate = oneMonthAgo;
        document.getElementById('end_date').valueAsDate = today;
    </script>
</body>
</html>