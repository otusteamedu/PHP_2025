<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запрос банковской выписки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0">🏦 Запрос банковской выписки</h2>
                    <div class="connection-status mt-2">
                        <small id="connectionStatus" class="badge bg-secondary">Подключение...</small>
                    </div>
                </div>
                <div class="card-body">
                    <form id="statementForm" method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">📧 Email для уведомлений:</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <div class="invalid-feedback">Пожалуйста, введите корректный email адрес.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">📅 Дата начала:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required
                                       value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-01')) ?>">
                                <div class="invalid-feedback">Пожалуйста, выберите дату начала.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">📅 Дата окончания:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required
                                       value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d')) ?>">
                                <div class="invalid-feedback">Пожалуйста, выберите дату окончания.</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                📊 Сгенерировать выписку
                            </button>
                        </div>
                    </form>

                    <!-- Результаты -->
                    <div id="results" class="mt-4"></div>
                </div>
                <div class="card-footer text-muted">
                    <small>⏱️ Обработка запроса может занять несколько минут. Результаты будут отправлены на указанный email.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>