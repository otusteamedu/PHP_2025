<?php

namespace Ak\Hw\Mappers;

use Ak\Hw\Models\Film;
use PDO;
use LogicException;


class FilmsMapper extends AbstractMapper
{
    public function find($id): ?Film
    {
        if (true === $this->identityMap->hasId($id)) {
            return $this->identityMap->getObject($id);
        }

        $stmt = $this->db->prepare('SELECT * FROM films WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $filmData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($filmData) {
            $film = new Film($filmData['title'], $filmData['description'], $filmData['id']);
            $this->identityMap->set($id, $film);
            return $film;
        }
        return null;
    }

    public function insert(Film $film): int
    {
        if ($film->getId() !== null) {
            throw new LogicException('Object has an ID, cannot insert.');
        }

        $stmt = $this->db->prepare("INSERT INTO films (title, description) VALUES (:title, :description) ");
        $stmt->bindValue(':title', $film->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $film->getDescription(), PDO::PARAM_STR);
        $stmt->execute();

        $id = (int)$this->db->lastInsertId();
        $film->setId($id);
        $this->identityMap->set($id, $film);
        return $id;
    }

    public function update(Film $film): bool
    {
        if ($film->getId() === null) {
            throw new LogicException('Object has no ID, cannot update.');
        }

        $stmt = $this->db->prepare("UPDATE films SET title = :title, description = :description WHERE id = :id");

        $stmt->bindValue(':id', $film->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':title', $film->getTitle(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $film->getDescription(), PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM films WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            if ($this->identityMap->hasId($id)) {
                $this->identityMap->removeId($id);
            }
            return true;
        }
        return false;
    }

    public function getList($count = 10, $offset = 0)
    {
        $stmt = $this->db->prepare('SELECT * FROM films LIMIT :count OFFSET :offset');
        $stmt->bindValue(':count', $count, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $filmsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $films = [];
        foreach ($filmsData as $filmData) {
            $film = new Film($filmData['title'], $filmData['description'], $filmData['id']);
            $this->identityMap->set($filmData['id'], $film);
            $films[] = $film;
        }

        $totalFilmsStmt = $this->db->query('SELECT COUNT(*) FROM films');
        $totalFilms = $totalFilmsStmt->fetchColumn();
        $totalPages = ceil($totalFilms / $count);

        $currentPage = floor($offset / $count) + 1;

        return ['films' => $films, 'totalPages' => $totalPages, 'currentPage' => $currentPage];
    }
}
