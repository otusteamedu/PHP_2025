<?php

use Dinargab\Homework12\App;
use Dinargab\Homework12\Model\Movie;
use Dinargab\Homework12\Render\MovieRenderer;

require_once '../vendor/autoload.php';

$app = new App();

$app->init();

$movie = $app->movieMapper->getById(3);

echo MovieRenderer::render($movie);


$moviesList = $app->movieMapper->getList(15, 0);

echo "<h2>Movie List Before deletion</h2>";
echo MovieRenderer::renderList($moviesList);

$app->movieMapper->delete($movie);

$movie = new Movie(
    "Eraser",
    "A Witness Protection specialist becomes suspicious of his co-workers when dealing with a case involving high-tech weapons.",
    new \DateTime("1996-12-25"),
    115,
    6.2,
);
$app->movieMapper->save($movie);

$moviesList[0]->setTitle("Updated title");

$app->movieMapper->save($moviesList[0]);
$moviesList = $app->movieMapper->getList(15, 0);

echo "<h2>Movie List after deletion of movie id 3 and adding new movie, and udpdating first movie</h2>";
echo MovieRenderer::renderList($moviesList);


echo "<h2>Total Recall Movies</h2>";
$totalRecallMovies = $app->movieMapper->getByMovieTitle("Total Recall");

echo MovieRenderer::renderList($totalRecallMovies);

