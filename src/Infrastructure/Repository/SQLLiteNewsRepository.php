<?php

namespace App\Infrastructure\Repository;

use App\Domain\DomainException\URLException;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;
use App\Infrastructure\Factory\NewsFactory;
use PDO;

class SQLLiteNewsRepository implements NewsRepositoryInterface
{
    public function __construct(
        private readonly \SQLite3 $db,
        private readonly NewsFactory $newsFactory
    ){
        $this->checkDb();
    }

    private function checkDb(): void
    {
        if(
            $this->db->query("SELECT * FROM sqlite_master WHERE type='table' AND name='news'")->numColumns()===0
        ) {
            $this->db->exec(<<<SQL
CREATE TABLE news (
--     id INTEGER PRIMARY KEY AUTOINCREMENT,
    url TEXT NOT NULL UNIQUE,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    title TEXT NOT NULL
)
SQL
            );
        }
    }

    public function findAll(): iterable
    {
        $returnArray = [];
        $newsList = $this->db->query("SELECT news.*, ROWID FROM news ORDER BY date DESC");

        if(!$newsList) {
            return [];
        }
        while ($oneNews = $newsList?->fetchArray(SQLITE3_ASSOC)) {

            $returnArray[] = $this->newsFactory->createFromDb($oneNews);
        }
        return $returnArray;

    }

    public function findById(int $id): ?News
    {
        $query = $this->db->prepare("SELECT news.*, ROWID FROM news WHERE ROWID=:id");
        $query->bindValue(":id", $id, SQLITE3_INTEGER);
        $oneNews = $query->execute();
        if($oneNews===false) {
            return null;
        }
        $oneNews = $oneNews->fetchArray(SQLITE3_ASSOC);
        if($oneNews===false) {
            return null;
        }
        return $this->newsFactory->createFromDb($oneNews);
    }

    public function save(News $news): void
    {
            if($this->findByUrl($news->getUrl()->getValue())){
                throw new URLException("This URL already exists.");
            }

            if ((int)$news->getId() > 0) {
                $query = $this->db->prepare("UPDATE news SET url=:url, date=:date, title=:title WHERE ROWID=:id");
                $query->bindValue(":id", $news->getId(), SQLITE3_INTEGER);
                $needID = false;
            } else {
                $query = $this->db->prepare("INSERT INTO news (url, date, title) VALUES (:url, :date, :title)");
                $needID = true;
                // TODO: Проверить нет ли такого URL уже в базе?
            }
            $query = $this->db->prepare("INSERT INTO news (url, date, title) VALUES (:url, :date, :title)");
            $query->bindValue(":url", $news->getUrl()->getValue());
            $query->bindValue(":date", $news->getDate()->format('Y-m-d H:i:s.u'));
            $query->bindValue(":title", $news->getTitle()->getValue());

            $query->execute();

            if ($needID) {
                $lastInsertId = $this->db->lastInsertRowID();
                $news->setIdFromDb($lastInsertId);
            }
    }

    public function delete(News $news): void
    {
        $query = $this->db->prepare("DELETE FROM news WHERE ROWID=:id");
        $query->bindValue(":id", $news->getId(), SQLITE3_INTEGER);
        $query->execute();
    }

    public function findByUrl(string $url): ?News
    {
        $query = $this->db->prepare("SELECT news.*, ROWID FROM news WHERE url=:url");
        $query->bindValue(":url", $url);
        $oneNews = $query->execute();
        if($oneNews===false) {
            return null;
        }
        $oneNews = $oneNews->fetchArray(SQLITE3_ASSOC);
        if($oneNews===false) {
            return null;
        }

        return $this->newsFactory->createFromDb($oneNews);
    }

    public function findByIds(array $ids): iterable
    {
        $query = "SELECT *, ROWID FROM news WHERE ROWID IN (".implode(',', array_fill(0, count($ids), '?')).")";

        $returnArray = [];
        $newsList = $this->db->prepare($query);
        $i=0;
        foreach ($ids as $id) {
            $newsList->bindValue(++$i, $id,SQLITE3_INTEGER);
        }
        $newsList = $newsList->execute();

        if(!$newsList) {
            return [];
        }
        while ($oneNews = $newsList?->fetchArray(SQLITE3_ASSOC)) {

            $returnArray[] = $this->newsFactory->createFromDb($oneNews);
        }
        return $returnArray;


    }
}