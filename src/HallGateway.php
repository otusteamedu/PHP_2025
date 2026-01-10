<?php

require_once "Hall.php";
require_once "IdentityMap.php";

class HallGateway
{
    private PDO $pdo;
    private IdentityMap $identityMap;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->identityMap = new IdentityMap();
    }

    /**
     * @return Hall[]
     */
    public function findAll(int $limit = 1000): array
    {
        $stmt = $this->pdo->query("SELECT * FROM Hall LIMIT $limit");
        $halls = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = (int)$row['id'];

            // Используем Identity Map, чтобы избежать дублирования
            if ($this->identityMap->has($id)) {
                $halls[] = $this->identityMap->get($id);
                continue;
            }

            $hall = new Hall(
                $id,
                (int)$row['cinema_id'],
                $row['name'],
                (int)$row['capacity']
            );

            $this->identityMap->set($hall);
            $halls[] = $hall;
        }

        return $halls;
    }

    public function findById(int $id): ?Hall
    {
        if ($this->identityMap->has($id)) {
            return $this->identityMap->get($id);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM Hall WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $hall = new Hall(
            (int)$row['id'],
            (int)$row['cinema_id'],
            $row['name'],
            (int)$row['capacity']
        );

        $this->identityMap->set($hall);
        return $hall;
    }
}
