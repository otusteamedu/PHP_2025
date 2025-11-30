<?php
declare(strict_types=1);

require '../vendor/autoload.php';
require '../app/bootstrap.php';
?>
<html>


<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container">
    <br>
    <div class="row">
        <div class="col">
            <h1 class="text-center">Банковская выписка</h1>
        </div>
    </div>
    <br><br>

    <div class="row">
        <div class="col">
            <i><b>Статус: </b><span class="text-status">готов к отправке</span></i>
        </div>
    </div>
    <br>

    <form class="js-form-bank-statement">
        <div class="mb-3">
            <div class="row">
                <div class="col">
                    <label for="date-from" class="form-label">Дата, начиная с:</label>
                    <input type="date" name="from" class="form-control" id="date-from" required>
                </div>

                <div class="col">
                    <label for="date-to" class="form-label">Дата, заканчивая до:</label>
                    <input type="date" name="to" class="form-control" id="date-to" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row">
                <div class="col">
                    <label for="data-email" class="form-label">Электронная почта:</label>
                    <input type="email" name="email" class="form-control" id="data-email" required>
                </div>

                <div class="col">
                    <label for="data-phone" class="form-label">Телефон:</label>
                    <input type="text" name="phone" class="form-control" id="data-phone" required>
                </div>
            </div>
        </div>

        <div class="mb-3 text-end">
            <button type="submit" class="btn btn-primary" id="status">Отправить</button>
        </div>
    </form>
</div>

</body>

<footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            let requestId = 0;

            $('.js-form-bank-statement').on('submit', function(e){
                e.preventDefault();

                $.post(
                    'bankStatement/submit.php',
                    $('.js-form-bank-statement').serialize(),
                    function(response) {
                        requestId = response.requestId;
                        $('.text-status').text('заявка обрабатывается !');
                    }
                );
            });

            setInterval(function(){
                if (requestId) {
                    $.post(
                        'bankStatement/status.php',
                        {'request_id': requestId},
                        function(status) {
                            if (status === 'done') {
                                $('.text-status').text('заявка готова !!!');
                                requestId = 0;
                            }
                        }
                    );
                }
            }, 3000);
        });
    </script>

</footer>

</html>