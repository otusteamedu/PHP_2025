<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Отправка задач в очередь</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 30px auto; }
        label, button { display: block; margin-top: 15px; }
        input, textarea { width: 100%; padding: 8px; font-size: 1em; }
        #statusBlock { margin-top: 20px; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<h1>Отправить задачу</h1>

<form id="taskForm">
    <label for="taskPayload">Данные задачи (JSON):</label>
    <textarea id="taskPayload" rows="5" placeholder='{"message": "Привет"}'>{}</textarea>

    <button type="submit">Отправить задачу</button>
</form>

<div id="result" style="margin-top: 20px;"></div>

<div id="statusBlock" style="display:none;">
    <h2>Проверить статус задачи</h2>
    <input type="text" id="requestIdInput" placeholder="Введите ID задачи" />
    <button id="checkStatusBtn">Проверить статус</button>

    <div id="statusResult" style="margin-top: 10px;"></div>
</div>

<script>
    const form = document.getElementById('taskForm');
    const resultDiv = document.getElementById('result');
    const statusBlock = document.getElementById('statusBlock');
    const requestIdInput = document.getElementById('requestIdInput');
    const checkStatusBtn = document.getElementById('checkStatusBtn');
    const statusResult = document.getElementById('statusResult');

    const API_BASE = 'api/';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        resultDiv.textContent = '';
        statusResult.textContent = '';

        let payloadText = document.getElementById('taskPayload').value;

        let payload;
        try {
            payload = JSON.parse(payloadText);
        } catch (err) {
            resultDiv.textContent = 'Ошибка: некорректный JSON';
            return;
        }

        try {
            const response = await fetch(`${API_BASE}requests`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const err = await response.json();
                resultDiv.textContent = `Ошибка сервера: ${err.error || response.statusText}`;
                return;
            }

            const data = await response.json();
            resultDiv.textContent = `Задача отправлена! ID: ${data.id}`;
            requestIdInput.value = data.id;
            statusBlock.style.display = 'block';

        } catch (err) {
            resultDiv.textContent = 'Ошибка сети: ' + err.message;
        }
    });

    checkStatusBtn.addEventListener('click', async () => {
        const id = requestIdInput.value.trim();
        if (!id) {
            statusResult.textContent = 'Введите ID задачи';
            return;
        }

        statusResult.textContent = 'Загрузка...';

        try {
            const response = await fetch(`${API_BASE}requests/${id}`);
            if (!response.ok) {
                const err = await response.json();
                statusResult.textContent = `Ошибка: ${err.error || response.statusText}`;
                return;
            }

            const data = await response.json();
            statusResult.innerHTML = `
          <strong>Статус:</strong> ${data.status} <br/>
          <strong>Результат:</strong> ${data.result || '–'}
        `;

        } catch (err) {
            statusResult.textContent = 'Ошибка сети: ' + err.message;
        }
    });
</script>
</body>
</html>
