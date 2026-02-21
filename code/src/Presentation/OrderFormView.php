<?php
declare(strict_types=1);

namespace Ak\Hw\Presentation;

class OrderFormView
{
    public static function render(array $errors = [], array $data = [], ?string $successMessage = null): void
    {
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Order Payment</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Payment Form</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($successMessage): ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($successMessage); ?>
                                </div>
                            <?php endif; ?>

                            <form id="payment-form" method="POST" action="/index.php">
                                <div class="mb-3">
                                    <label for="order_number" class="form-label">Order Number</label>
                                    <input type="text" class="form-control <?php echo isset($errors['order_number']) ? 'is-invalid' : ''; ?>" id="order_number" name="order_number" value="<?php echo htmlspecialchars((string)($data['order_number'] ?? '1')); ?>">
                                    <div class="invalid-feedback" id="order_number-error"><?php echo $errors['order_number']['message'] ?? ''; ?></div>
                                </div>
                                <div class="mb-3">
                                    <label for="sum" class="form-label">Sum</label>
                                    <input type="text" class="form-control <?php echo isset($errors['sum']) ? 'is-invalid' : ''; ?>" id="sum" name="sum" value="<?php echo htmlspecialchars((string)($data['sum'] ?? '10.1')); ?>">
                                    <div class="invalid-feedback" id="sum-error"><?php echo $errors['sum']['message'] ?? ''; ?></div>
                                </div>
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control <?php echo isset($errors['card_number']) ? 'is-invalid' : ''; ?>" id="card_number" name="card_number" placeholder="xxxx xxxx xxxx xxxx" value="<?php echo htmlspecialchars((string)($data['card_number'] ?? '4111 1111 1111 1111')); ?>">
                                    <div class="invalid-feedback" id="card_number-error"><?php echo $errors['card_number']['message'] ?? ''; ?></div>
                                </div>
                                <div class="mb-3">
                                    <label for="card_holder" class="form-label">Card Holder</label>
                                    <input type="text" class="form-control <?php echo isset($errors['card_holder']) ? 'is-invalid' : ''; ?>" id="card_holder" name="card_holder" placeholder="John Doe" value="<?php echo htmlspecialchars((string)($data['card_holder'] ?? 'John Doe')); ?>">
                                    <div class="invalid-feedback" id="card_holder-error"><?php echo $errors['card_holder']['message'] ?? ''; ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <label for="card_expiration" class="form-label">Expiration Date</label>
                                        <input type="text" class="form-control <?php echo isset($errors['card_expiration']) ? 'is-invalid' : ''; ?>" id="card_expiration" name="card_expiration" placeholder="MM/YY" value="<?php echo htmlspecialchars((string)($data['card_expiration'] ?? '12/26')); ?>">
                                        <div class="invalid-feedback" id="card_expiration-error"><?php echo $errors['card_expiration']['message'] ?? ''; ?></div>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control <?php echo isset($errors['cvv']) ? 'is-invalid' : ''; ?>" id="cvv" name="cvv" placeholder="xxx" value="<?php echo htmlspecialchars((string)($data['cvv'] ?? '602')); ?>">
                                        <div class="invalid-feedback" id="cvv-error"><?php echo $errors['cvv']['message'] ?? ''; ?></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mt-4">Pay</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Этот скрипт больше не нужен для первоначального отображения ошибок,
            // но может быть полезен для будущей валидации на клиенте.
            // Пока оставляем его без изменений.
            document.addEventListener('DOMContentLoaded', function () {
                const errors = <?php echo json_encode($errors); ?>;
                if (Object.keys(errors).length > 0) {
                    for (const field in errors) {
                        const input = document.getElementById(field);
                        if (input && !input.classList.contains('is-invalid')) {
                            input.classList.add('is-invalid');
                        }
                    }
                }
            });
        </script>
        </body>
        </html>
        <?php
    }
}
