<!DOCTYPE html>
<html>
<head>
    <title>Запрос на банковскую выписку</title>
</head>
<body>
<h1>Запрос на банковскую выписку</h1>

<form id="requestForm">
    Дата начала: <input type="date" name="start_date" required><br><br>
    Дата конца: <input type="date" name="end_date" required><br><br>
    <button type="submit">Отправить запрос</button>
</form>

<div id="statusMessage"></div>

<script>
    const form = document.getElementById('requestForm');
    const statusDiv = document.getElementById('statusMessage');
    let pollInterval = null;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        statusDiv.textContent = 'Отправляю запрос...';

        const formData = new FormData(form);

        try {
            const response = await fetch('submit.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            const taskId = result.task_id;

            statusDiv.textContent = 'Запрос принят. Ожидайте обработки...';

            pollInterval = setInterval(async () => {
                const res = await fetch(`status.php?task_id=${taskId}`);
                const data = await res.json();

                statusDiv.textContent = `Статус: ${data.status}`;

                if (data.status === 'done') {
                    clearInterval(pollInterval);
                    statusDiv.textContent = 'Выписка готова!';
                }
            }, 3000);

        } catch (err) {
            statusDiv.textContent = 'Ошибка при отправке запроса';
            console.error(err);
        }
    });
</script>
</body>
</html>
