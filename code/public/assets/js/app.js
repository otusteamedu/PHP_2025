document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('statementForm');
    const message = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');
    const getDateInput = (date) => date.toISOString().split('T')[0];

    const today = new Date();
    const thirtyDaysAgo = new Date(today);
    thirtyDaysAgo.setDate(today.getDate() - 30);
    document.getElementById('dateFrom').value = getDateInput(thirtyDaysAgo);
    document.getElementById('dateTo').value = getDateInput(today);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';
        message.className = 'message';
        message.textContent = '';

        try {
            const response = await fetch('/', {method: 'POST', body: new FormData(form)});
            const {success, data, error} = await response.json();

            if (success) {
                message.className = 'message success';
                message.textContent = data.message;
                form.reset();
            } else {
                message.className = 'message error';
                message.textContent = error || 'Ошибка при отправке';
            }
        } catch {
            message.className = 'message error';
            message.textContent = 'Ошибка соединения с сервером';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Заказать выписку';
        }
    });
});
