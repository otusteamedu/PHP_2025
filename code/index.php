<?php
require_once __DIR__ . '/vendor/autoload.php';
use Ak\Hw\Mappers\FilmsMapper;
use Ak\Hw\Models\Film;

$filmMapper = new FilmsMapper();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$list = $filmMapper->getList($limit, $offset);
$films = $list['films'];
$totalPages = $list['totalPages'];
$currentPage = $list['currentPage'];
?>

<?php if ($films):?>
    <?php foreach ($films as $film) :?>
    <div style="margin: 5px; border: 1px solid black; padding: 5px;">
        <h3><?= htmlspecialchars($film->getTitle()); ?>  </h3>
        <?= htmlspecialchars($film->getDescription()); ?>
    </div>
    <?php endforeach;?>
<?php endif;?>

<div>
    <?php if ($currentPage > 1): ?>
        <a href="/page/<?= $currentPage - 1 ?>">Назад</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/page/<?= $i ?>" <?= ($i == $currentPage) ? 'style="font-weight: bold;"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="/page/<?= $currentPage + 1 ?>">Вперед</a>
    <?php endif; ?>
</div>


<?php
// ПРОВЕРКИ IDENTITY MAP
[$title, $description] = ['new one','description'];
$newFilm = new Film($title, $description);
$fid = $filmMapper->insert($newFilm);

$obj1 = $filmMapper->find($fid);
$obj2 = $filmMapper->find($fid);

echo "<pre>";
print_r([$obj1, $obj2]);
echo "</pre>";
$obj1->setTitle('is title');
$obj2->setDescription('new description');

echo "<pre>";
print_r([$obj1, $obj2]);
echo "</pre>";

$filmMapper->delete($fid);

?>