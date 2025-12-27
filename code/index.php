<?php
require __DIR__ . '/vendor/autoload.php';
use Ak\Hw\Models\Events;

$events = new Events();

if (isset($_POST['params'])) {
    $score = $events->getScore($_POST['params']);
    echo json_encode(['score' => $score]);
    exit;
}

$params = $events->getParams();
?>

<div id="params-container">
    <?php foreach ($params as $p => $val): ?>
        <button class="param-button" data-param="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></button>
    <?php endforeach; ?>
</div>

<div id="score-container">
    Score: <span id="score">0</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paramsContainer = document.getElementById('params-container');
        const scoreElement = document.getElementById('score');

        paramsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('param-button')) {
                e.target.classList.toggle('active');
                sendRequest();
            }
        });

        function sendRequest() {
            const activeButtons = document.querySelectorAll('.param-button.active');
            const selectedParams = Array.from(activeButtons).map(button => button.dataset.param);

            fetch('/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: selectedParams.map(param => `params[]=${encodeURIComponent(param)}`).join('&')
            })
            .then(response => response.json())
            .then(data => {
                scoreElement.textContent = data.score;
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>
<style>
    .param-button.active {
        background-color: #4CAF50;
        color: white;
    }
</style>