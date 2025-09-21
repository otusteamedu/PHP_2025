<?php

declare(strict_types=1);


namespace Dinargab\Homework12;

use Dinargab\Homework12\Mapper\MovieMapper;
use Dinargab\Homework12\Model\Movie;
use Dinargab\Homework12\Render\MovieRenderer;

class App
{

    public MovieMapper $movieMapper;


    public function renderTest(): void
    {
        $this->init();

        $movie = $this->movieMapper->getById(3);

        echo MovieRenderer::render($movie);


        $moviesList = $this->movieMapper->getList(15, 0);

        echo "<h2>Movie List Before deletion</h2>";
        echo MovieRenderer::renderList($moviesList);

        $this->movieMapper->delete($movie);

        $movie = new Movie(
            "Eraser",
            "A Witness Protection specialist becomes suspicious of his co-workers when dealing with a case involving high-tech weapons.",
            new \DateTime("1996-12-25"),
            115,
            6.2,
        );
        $this->movieMapper->save($movie);

        $moviesList[0]->setTitle("Updated title");

        $this->movieMapper->save($moviesList[0]);
        $moviesList = $this->movieMapper->getList(15, 0);

        echo "<h2>Movie List after deletion of movie id 3 and adding new movie, and udpdating first movie</h2>";
        echo MovieRenderer::renderList($moviesList);


        echo "<h2>Total Recall Movies</h2>";
        $totalRecallMovies = $this->movieMapper->getByMovieTitle("Total Recall");

        echo MovieRenderer::renderList($totalRecallMovies);
    }

    private function init(): void
    {
        $pdo = $this->initDatabase();
        $this->movieMapper = new MovieMapper($pdo);
        $this->initTestTable($pdo);
        $this->initTestData($pdo);
    }

    private function initDatabase()
    {
        $dbHost = getenv('DB_HOST');
        $dbPort = getenv('DB_PORT');
        $dbName = getenv('DB_DATABASE');
        $dbUser = getenv('DB_USERNAME');
        $dbPass = getenv('DB_PASSWORD');
        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $pdo = new \PDO($dsn, $dbUser, $dbPass);

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    private function initTestTable(\Pdo $pdo)
    {
        $sql = 'truncate table movie restart identity;';
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $sql = 'create table if not exists movie(
            movie_id serial primary key,
            title varchar(255) not null,
            overview text,
            release_date timestamp,
            duration integer,
            rating real
        )';
        $sth = $pdo->prepare($sql);
        $sth->execute();
    }

    private function initTestData($pdo)
    {

        $testMovies = [
            new Movie(
                'The Shawshank Redemption',
                'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                new \DateTime('1994-09-23'),
                142,
                9.3
            ),
            new Movie(
                'The Godfather',
                'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                new \DateTime('1972-03-24'),
                175,
                9.2
            ),
            new Movie(
                'The Dark Knight',
                'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                new \DateTime('2008-07-18'),
                152,
                9.0
            ),
            new Movie(
                'Pulp Fiction',
                'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                new \DateTime('1994-10-14'),
                154,
                8.9
            ),
            new Movie(
                'Forrest Gump',
                'The presidencies of Kennedy and Johnson, the events of Vietnam, Watergate, and other historical events unfold through the perspective of an Alabama man with an IQ of 75.',
                new \DateTime('1994-07-06'),
                142,
                8.8
            ),
            new Movie(
                'Inception',
                'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
                new \DateTime('2010-07-16'),
                148,
                8.8
            ),
            new Movie(
                'The Matrix',
                'A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.',
                new \DateTime('1999-03-31'),
                136,
                8.7
            ),
            new Movie(
                'Goodfellas',
                'The story of Henry Hill and his life in the mob, covering his relationship with his wife Karen Hill and his mob partners Jimmy Conway and Tommy DeVito.',
                new \DateTime('1990-09-19'),
                146,
                8.7
            ),
            new Movie(
                'Interstellar',
                'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
                new \DateTime('2014-11-07'),
                169,
                8.6
            ),
            new Movie(
                'Total Recall',
                'A factory worker, Douglas Quaid, begins to suspect that he is a spy after visiting Rekall - a company that provides its clients with implanted fake memories of a life they would like to have led - goes wrong and he finds himself on the run..',
                new \DateTime('2012-08-02'),
                118,
                6.2
            ),
            new Movie(
                'Total Recall',
                'When a man goes in to have virtual vacation memories of the planet Mars implanted in his mind, an unexpected and harrowing series of events forces him to go to the planet for real - or is he?',
                new \DateTime('1990-06-01'),
                113,
                7.5
            )
        ];

        foreach ($testMovies as $movie) {
            $this->movieMapper->save($movie);
        }
    }
}
