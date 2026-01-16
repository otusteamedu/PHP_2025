<?php

require_once __DIR__ . '/vendor/autoload.php';

use Arlex2305k\PatternsDb\Movie;
use Arlex2305k\PatternsDb\MovieDataMapper;
use Arlex2305k\PatternsDb\DatabaseConnection;

try {
	echo "Соединение с базой данных...\n";
	$pdo = DatabaseConnection::createConnection();
	echo "Соединение с базой данных успешно создано\n\n";

	$mapper = new MovieDataMapper($pdo);

	echo "Создание тестовых фильмов...\n";
	$movie1 = new Movie("Фильм1", "Бла бла бла-1", 100, "триллер", 4.6);
	$movie2 = new Movie("Фильм2", "Бла бла бла-2", 150, "фантастика", 3.7);
	$movie3 = new Movie("Фильм3", "Бла бла бла-3", 170, "боевик", 5.0);

	echo "Сохранение фильмов в базу данных...\n";
	$movie1 = $mapper->save($movie1);
	$movie2 = $mapper->save($movie2);
	$movie3 = $mapper->save($movie3);

	echo "Фильмы успешно сохранены с ID: {$movie1->getId()}, {$movie2->getId()}, {$movie3->getId()}\n\n";

	echo "Проверка работы Identity Map...\n";
	$fetchedMovie1 = $mapper->getById($movie1->getId());
	$fetchedMovie1Again = $mapper->getById($movie1->getId());

	if ($fetchedMovie1 === $fetchedMovie1Again) {
		echo "+ Identity Map работает корректно - возврат одного и того же объекта при повторном запросе\n";
	} else {
		echo "- Ошибка в работе Identity Map\n";
	}
	echo "\n";

	echo "Получение всех фильмов из базы данных...\n";
	$allMovies = $mapper->getAll();

	echo "Количество фильмов в коллекции: " . count($allMovies) . "\n";
	foreach ($allMovies as $idx => $movie) {
		echo ($idx + 1) . ". {$movie->getTitle()} ({$movie->getGenre()}, {$movie->getDurationMinutes()} мин.) - Рейтинг: {$movie->getRating()}\n";
	}
	echo "\n";

	echo "Получение фильмов с пагинацией (первые 2 записи)...\n";
	$pagedMovies = $mapper->getByOffsetAndLimit(0, 2);

	echo "Количество фильмов в результатах пагинации: " . count($pagedMovies) . "\n";
	foreach ($pagedMovies as $idx => $movie) {
		echo ($idx + 1) . ". {$movie->getTitle()}\n";
	}
	echo "\n";

	echo "Проверка работы Identity Map при массовом получении...\n";
	$firstMovieFromAll = $allMovies->get(0);
	$firstMovieFromPaged = $pagedMovies->get(0);

	if ($firstMovieFromAll === $firstMovieFromPaged) {
		echo "+ Identity Map работает корректно при массовом получении - одни и те же объекты\n";
	} else {
		echo "- Ошибка в работе Identity Map при массовом получении\n";
	}
	echo "\n";

	echo "Обновление фильма...\n";
	$movieToUpdate = clone $movie1;
	$movieToUpdate->setTitle("Обновленный Фильм1");
	$updatedMovie = $mapper->save($movieToUpdate);

	echo "Фильм обновлен: {$updatedMovie->getTitle()}\n";
	$refreshedMovie = $mapper->getById($movie1->getId());
	echo "Проверка из базы: {$refreshedMovie->getTitle()}\n\n";

	echo "Удаление тестовых данных...\n";
	$deleteResult = $mapper->delete($movie1->getId()) &&
		$mapper->delete($movie2->getId()) &&
		$mapper->delete($movie3->getId());

	if ($deleteResult) {
		echo "Тестовые данные успешно удалены\n";
	} else {
		echo "Ошибка при удалении тестовых данных\n";
	}

	echo "\nТестирование завершено успешно!\n";
} catch (Exception $e) {
	echo "Ошибка: " . $e->getMessage() . "\n";
	echo "Файл: " . $e->getFile() . "\n";
	echo "Строка: " . $e->getLine() . "\n";
}
