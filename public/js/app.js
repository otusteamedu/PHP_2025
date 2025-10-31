class BankStatementApp {
    constructor() {
        this.ws = null;
        this.currentRequestId = null;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;

        this.init();
    }

    init() {
        this.connectWebSocket();
        this.setupForm();
        this.setupEventListeners();
    }

    setupEventListeners() {
        window.addEventListener('focus', () => {
            if (!this.isConnected) {
                this.connectWebSocket();
            }
        });

        window.addEventListener('beforeunload', () => {
            if (this.ws) {
                this.ws.close();
            }
        });
    }

    connectWebSocket() {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            return;
        }

        if (this.ws) {
            this.ws.close();
        }

        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.hostname}:8080`;

        console.log('🔄 Connecting to WebSocket:', wsUrl);

        try {
            this.ws = new WebSocket(wsUrl);

            this.ws.onopen = () => {
                console.log('✅ WebSocket connected successfully');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.updateConnectionStatus('connected');
                this.updateStatus('Успешно подключено к серверу', 'success');

                if (this.currentRequestId) {
                    console.log('🔄 Re-subscribing to request:', this.currentRequestId);
                    setTimeout(() => {
                        this.subscribeToUpdates(this.currentRequestId);
                    }, 500);
                }
            };

            this.ws.onmessage = (event) => {
                console.log('📨 Raw WebSocket message:', event.data);
                try {
                    const data = JSON.parse(event.data);
                    console.log('📨 Parsed WebSocket data:', data);
                    this.handleWebSocketMessage(data);
                } catch (error) {
                    console.error('❌ Error parsing WebSocket message:', error);
                }
            };

            this.ws.onclose = (event) => {
                console.log('❌ WebSocket disconnected:', event.code, event.reason);
                this.isConnected = false;
                this.updateConnectionStatus('disconnected');

                if (event.code !== 1000) {
                    this.updateStatus('Соединение с сервером прервано', 'error');
                    this.attemptReconnect();
                }
            };

            this.ws.onerror = (error) => {
                console.error('💥 WebSocket error:', error);
                this.updateConnectionStatus('error');
                this.updateStatus('Ошибка подключения к серверу', 'error');
            };

        } catch (error) {
            console.error('❌ Error creating WebSocket:', error);
            this.updateConnectionStatus('error');
            this.updateStatus('Не удалось создать подключение', 'error');
        }
    }

    attemptReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log('❌ Max reconnection attempts reached');
            this.updateConnectionStatus('failed');
            this.updateStatus('Не удалось подключиться к серверу', 'error');
            return;
        }

        const delay = Math.min(1000 * Math.pow(2, this.reconnectAttempts), 10000);
        console.log(`🔄 Reconnecting in ${delay}ms... (attempt ${this.reconnectAttempts + 1}/${this.maxReconnectAttempts})`);

        this.updateStatus(`Попытка переподключения ${this.reconnectAttempts + 1}/${this.maxReconnectAttempts}...`, 'info');

        setTimeout(() => {
            this.reconnectAttempts++;
            this.connectWebSocket();
        }, delay);
    }

    handleWebSocketMessage(data) {
        console.log('🔄 Processing message type:', data.type, 'Full data:', data);

        switch (data.type) {
            case 'connection_established':
                this.updateStatus('Успешно подключено к серверу обработки', 'success');
                this.updateConnectionStatus('connected');
                break;

            case 'subscribed':
                this.updateStatus('Отслеживание прогресса обработки...', 'info');
                break;

            case 'progress':
                this.updateProgress(data);
                break;

            case 'completed':
                this.handleCompletion(data);
                break;

            case 'error':
                this.showError(data.message || 'Произошла ошибка');
                break;

            case 'pong':
                console.log('🏓 Pong received');
                break;

            default:
                console.log('❓ Unknown message type:', data.type);
        }
    }

    setupForm() {
        const form = document.getElementById('statementForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        [startDate, endDate].forEach(input => {
            input.addEventListener('change', () => {
                this.validateDates();
            });
        });

        const today = new Date();
        const oneMonthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

        if (!startDate.value) {
            startDate.value = oneMonthAgo.toISOString().split('T')[0];
        }
        if (!endDate.value) {
            endDate.value = today.toISOString().split('T')[0];
        }
    }

    validateDates() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const submitBtn = document.getElementById('submitBtn');

        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate > endDate) {
            this.showError('Дата начала не может быть больше даты окончания');
            submitBtn.disabled = true;
            return false;
        } else {
            this.clearError();
            submitBtn.disabled = false;
            return true;
        }
    }

    async submitForm() {
        if (!this.validateDates()) {
            return;
        }

        const form = document.getElementById('statementForm');
        const formData = new FormData(form);
        formData.append('ajax', 'true');

        this.showLoading();
        this.setSubmitButtonState(true);

        try {
            console.log('📤 Submitting form data...');
            const response = await fetch('/', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('📥 Form submission response:', result);

            if (result.success) {
                this.updateStatus('Запрос принят в обработку', 'success');
                this.currentRequestId = result.request_id;
                console.log('✅ Request ID received:', this.currentRequestId);

                setTimeout(() => {
                    this.subscribeToUpdates(result.request_id);
                }, 100);

            } else {
                this.showError(result.error || 'Произошла ошибка при отправке запроса');
            }
        } catch (error) {
            console.error('❌ Form submission error:', error);
            this.showError('Ошибка сети: ' + error.message);
        } finally {
            this.setSubmitButtonState(false);
        }
    }

    setSubmitButtonState(loading) {
        const submitBtn = document.getElementById('submitBtn');
        const spinner = submitBtn.querySelector('.spinner-border');

        if (loading) {
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
        } else {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    subscribeToUpdates(requestId) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            const message = {
                type: 'subscribe',
                request_id: requestId
            };
            console.log('📤 Sending subscribe message:', message);
            this.ws.send(JSON.stringify(message));
        } else {
            console.error('❌ WebSocket not connected, cannot subscribe');
            this.updateStatus('Нет подключения к серверу, повторная попытка...', 'warning');

            setTimeout(() => {
                this.connectWebSocket();
                setTimeout(() => {
                    if (this.isConnected && this.ws.readyState === WebSocket.OPEN) {
                        this.subscribeToUpdates(requestId);
                    } else {
                        this.updateStatus('Не удалось подключиться к серверу', 'error');
                    }
                }, 2000);
            }, 1000);
        }
    }

    updateProgress(data) {
        console.log('📊 Updating progress with data:', data);

        const progressText = document.getElementById('progressText');

        // Детектируем формат данных и извлекаем значения
        let progress, message, step;

        if (data.data && typeof data.data === 'object') {
            // Формат 1: {type: 'progress', data: {progress: 20, message: '...', step: '...'}}
            progress = data.data.progress || 0;
            message = data.data.message || 'Обработка...';
            step = data.data.step || '';
        } else if (data.progress !== undefined) {
            // Формат 2: {type: 'progress', progress: 20, message: '...', step: '...'}
            progress = data.progress;
            message = data.message || 'Обработка...';
            step = data.step || '';
        } else {
            // Формат по умолчанию
            progress = 0;
            message = 'Обработка...';
            step = '';
            console.warn('⚠️ Unknown progress data format:', data);
        }

        // Обеспечиваем, что progress является числом
        progress = Number(progress);
        if (isNaN(progress)) {
            progress = 0;
        }

        console.log(`📊 Progress: ${progress}%, Message: ${message}, Step: ${step}`);

        // Обновляем этапы
        this.updateProgressStages(step, progress);

        // Обновляем статус
        this.updateStatus(`${message} (${progress}%)`, 'info');
    }

    updateProgressStages(step, progress) {
        const stages = {
            'started': 'stage1',
            'validating': 'stage2',
            'processing': 'stage3',
            'generating': 'stage4',
            'finalizing': 'stage5',
            'completed': 'stage6'
        };

        // Сбрасываем все этапы
        Object.values(stages).forEach(stageId => {
            const stage = document.getElementById(stageId);
            if (stage) {
                stage.classList.remove('text-primary', 'fw-bold', 'text-success');
            }
        });

        // Подсвечиваем текущий этап
        if (step && stages[step]) {
            const stageElement = document.getElementById(stages[step]);
            if (stageElement) {
                stageElement.classList.add('text-primary', 'fw-bold');
                console.log(`🎯 Highlighting stage: ${step}`);
            }
        }

        // Если завершено, подсвечиваем все этапы зеленым
        if (step === 'completed' || progress >= 100) {
            Object.values(stages).forEach(stageId => {
                const stage = document.getElementById(stageId);
                if (stage) {
                    stage.classList.add('text-success', 'fw-bold');
                }
            });
            console.log('✅ All stages completed');
        }
    }

    handleCompletion(data) {
        console.log('✅ Handling completion data:', data);

        // Извлекаем statement из разных возможных структур
        let statement;
        if (data.data) {
            statement = data.data; // {type: 'completed', data: {statement...}}
        } else {
            statement = data; // Прямой statement
        }

        this.showResults(statement);
        this.currentRequestId = null;

        // Принудительно обновляем прогресс до 100%
        this.updateProgress({
            progress: 100,
            message: 'Обработка завершена!',
            step: 'completed'
        });
    }

    showResults(statement) {
        console.log('📄 Showing results with statement:', statement);

        if (!statement) {
            this.showError('Данные выписки не получены');
            return;
        }

        // Безопасное извлечение transactions
        const transactions = statement.transactions || [];
        console.log('📊 Transactions found:', transactions.length);

        let transactionsHtml = '';
        if (transactions.length > 0) {
            transactions.forEach((transaction, index) => {
                const amountClass = transaction.type === 'Пополнение' ? 'text-success' : 'text-danger';
                const amountSign = transaction.type === 'Пополнение' ? '+' : '-';

                // Безопасное извлечение данных транзакции
                const transactionData = {
                    date: transaction.date || 'Не указана',
                    description: transaction.description || `Транзакция ${index + 1}`,
                    type: transaction.type || 'Неизвестно',
                    amount: transaction.amount || 0,
                    currency: transaction.currency || 'RUB'
                };

                transactionsHtml += `
                    <tr>
                        <td>${this.formatDate(transactionData.date)}</td>
                        <td>${transactionData.description}</td>
                        <td><span class="badge bg-secondary">${transactionData.type}</span></td>
                        <td class="${amountClass} fw-bold">${amountSign}${this.formatCurrency(transactionData.amount, transactionData.currency)}</td>
                    </tr>
                `;
            });
        } else {
            transactionsHtml = '<tr><td colspan="4" class="text-center text-muted">Транзакции не найдены за выбранный период</td></tr>';
        }

        // Безопасное извлечение основных данных
        const statementData = {
            period: statement.period || 'Не указан',
            total_income: statement.total_income || 0,
            total_expense: statement.total_expense || 0,
            balance: statement.balance || 0,
            currency: statement.currency || 'RUB',
            generated_at: statement.generated_at || new Date().toISOString(),
            transactions_count: transactions.length
        };

        const resultsHtml = `
            <div class="alert alert-success">
                <div class="d-flex align-items-center">
                    <span class="fs-4 me-3">✅</span>
                    <div>
                        <h4 class="alert-heading mb-2">Выписка успешно сгенерирована!</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Период:</strong> ${statementData.period}</p>
                                <p class="mb-1"><strong>Общий доход:</strong> ${this.formatCurrency(statementData.total_income, statementData.currency)}</p>
                                <p class="mb-1"><strong>Общий расход:</strong> ${this.formatCurrency(statementData.total_expense, statementData.currency)}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Конечный баланс:</strong> ${this.formatCurrency(statementData.balance, statementData.currency)}</p>
                                <p class="mb-1"><strong>Количество транзакций:</strong> ${statementData.transactions_count}</p>
                                <p class="mb-0"><strong>Сгенерировано:</strong> ${this.formatDateTime(statementData.generated_at)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">📊 Детали транзакций</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Дата</th>
                                    <th>Описание</th>
                                    <th>Тип операции</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${transactionsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <div class="d-flex align-items-center">
                    <span class="fs-5 me-2">📧</span>
                    <div>
                        <strong>Уведомление:</strong> Результаты также отправлены на указанный email адрес.
                    </div>
                </div>
            </div>
        `;

        const resultsDiv = document.getElementById('results');
        if (resultsDiv) {
            resultsDiv.innerHTML = resultsHtml;
            resultsDiv.style.display = 'block';
        }

        this.updateStatus('Выписка успешно сгенерирована и отправлена на email!', 'success');
    }

    showLoading() {
        const resultsDiv = document.getElementById('results');
        if (resultsDiv) {
            resultsDiv.innerHTML = `
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>
                            <strong>Отправка запроса на генерацию выписки...</strong>
                            <div class="small text-muted mt-1">Подготовка данных и подключение к серверу обработки</div>
                        </div>
                    </div>
                </div>
            `;
            resultsDiv.style.display = 'block';
        }
    }

    updateStatus(message, type) {
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' :
                    type === 'warning' ? 'alert-warning' : 'alert-info';

            statusDiv.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <span class="me-2">${this.getStatusIcon(type)}</span>
                        <span>${message}</span>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;
        }

        console.log(`📢 Status [${type}]: ${message}`);
    }

    updateConnectionStatus(status) {
        const statusElement = document.getElementById('connectionStatus');
        if (statusElement) {
            const statusConfig = {
                'connected': { text: '✅ Подключено', class: 'bg-success' },
                'disconnected': { text: '❌ Отключено', class: 'bg-secondary' },
                'error': { text: '⚠️ Ошибка', class: 'bg-danger' },
                'failed': { text: '🚫 Не подключено', class: 'bg-warning' }
            };

            const config = statusConfig[status] || statusConfig.disconnected;
            statusElement.textContent = config.text;
            statusElement.className = `badge ${config.class}`;
        }
    }

    getStatusIcon(type) {
        const icons = {
            'success': '✅',
            'error': '❌',
            'warning': '⚠️',
            'info': 'ℹ️'
        };
        return icons[type] || '📢';
    }

    showError(message) {
        this.updateStatus(message, 'error');

        const resultsDiv = document.getElementById('results');
        if (resultsDiv) {
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <span class="fs-5 me-3">❌</span>
                        <div>
                            <h5 class="alert-heading mb-2">Ошибка</h5>
                            <p class="mb-0">${message}</p>
                        </div>
                    </div>
                </div>
            `;
            resultsDiv.style.display = 'block';
        }
    }

    clearError() {
        const statusDiv = document.getElementById('status');
        if (statusDiv) {
            const alert = statusDiv.querySelector('.alert-danger');
            if (alert) {
                alert.remove();
            }
        }
    }

    formatDate(dateString) {
        if (!dateString) return 'Не указана';
        try {
            return new Date(dateString).toLocaleDateString('ru-RU');
        } catch (e) {
            return dateString;
        }
    }

    formatDateTime(dateTimeString) {
        if (!dateTimeString) return 'Не указано';
        try {
            return new Date(dateTimeString).toLocaleString('ru-RU');
        } catch (e) {
            return dateTimeString;
        }
    }

    formatCurrency(amount, currency) {
        if (amount === undefined || amount === null) return '0 ' + (currency || 'RUB');
        return `${amount.toLocaleString('ru-RU')} ${currency || 'RUB'}`;
    }
}

// Инициализация приложения
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Initializing Bank Statement App...');
    window.bankStatementApp = new BankStatementApp();
});