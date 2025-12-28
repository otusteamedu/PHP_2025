<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Ak\Hw\Models\Events;

$events = new Events();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_param':
                $param = $_POST['param'] ?? '';
                $score = $_POST['score'] ?? '';
                if (!empty($param) && is_numeric($score)) {
                    $events->addParam($param, (int)$score);
                }
                break;
            case 'delete_param':
                $paramToDelete = $_POST['param_to_delete'] ?? '';
                if (!empty($paramToDelete)) {
                    $events->deleteParam($paramToDelete);
                }
                break;
            case 'delete_all_params':
                $events->deleteAllParams();
                break;
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$params  = $events->getParams();
?>

<h3>Меню для менеджера</h3>

<?php if ($params) : ?>
    <h4>Наши параметры событий</h4>
    <table>
        <tr>
            <th>Параметр</th>
            <th>Вес</th>
            <th></th>
        </tr>
    <?php foreach ($params as $param => $score) : ?>
        <tr>
            <td><?= $param ?></td>
            <td><?= $score ?></td>
            <td>
            <form action="" method="post" style="display:inline;">
                <input type="hidden" name="action" value="delete_param">
                <input type="hidden" name="param_to_delete" value="<?= $param ?>">
                <button type="submit">Удалить</button>
            </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else : ?>
    <p>Нет параметров событий.</p>
<?php endif; ?>
<hr>
<h4>Добавить новый параметр</h4>
<form action="" method="post">
    <input type="hidden" name="action" value="add_param">
    <table>
        <tr>
            <td><label for="new_param_name">Название параметра:</label></td>
            <td><input type="text" id="new_param_name" name="param" required></td>
        </tr>
        <tr>
            <td><label for="new_param_score">Вес параметра:</label></td>
            <td><input type="number" id="new_param_score" name="score" required></td>
        </tr>
    </table>
    <button type="submit">Добавить</button>
</form>
<hr>
<h4>Удалить все параметры</h4>
<form action="" method="post" onsubmit="return confirm('Вы уверены, что хотите удалить все параметры?');">
    <input type="hidden" name="action" value="delete_all_params">
    <button type="submit">Удалить все</button>
</form>