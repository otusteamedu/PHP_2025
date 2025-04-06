<hr/>

<h2>Заносим данные в коллекцию identityMap</h2>

<p>$collection->findById(1)</p>
<p>$collection->findById(2)</p>

<h2>Получаем данные из identityMap</h2>

<?

foreach($data["identityMap1"] AS $user) {
    echo "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
}

?>

<h2>Получаем данные из базы и дописываем те, которых нет, в identityMap</h2>

<?

foreach($data["identityMap2"] AS $user) {
    echo "<p>{$user->getId()} {$user->getName()} {$user->getEmail()}</p>";
} 

?>

<hr/>