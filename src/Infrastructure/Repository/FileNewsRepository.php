<?php

namespace Infrastructure\Repository;

use \Domain\Repository\NewsRepositoryInterface;
use \Domain\Entity\News;

class FileNewsRepository implements NewsRepositoryInterface
{

    public function findAll(): iterable
    {

        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);

        $keys = $redis->keys('*');
        $allRecords = [];
        foreach ($keys as $key) {
            $allRecords[$key] = json_decode($redis->get($key),true);
        }
        return $allRecords;

    }

    public function findById(array $id_array): iterable
    {

        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);
        $allRecords = [];
        foreach ($id_array as $key) {
            $allRecords[$key] = json_decode($redis->get($key),true);
        }
        return $allRecords;

    }

    public function save(News $News): void
    {
        
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), 6379);

        $keys = $redis->keys('*');
        $count = count($keys);

        $id = $count++;
        $url = ($News->getUrl())->getValue();
        $title = $News->getTitle();
        $date = $News->getDate();

        $data = [
            "id"=>$id,
            "url"=>$url,
            "title"=>$title,
            "date"=>$date
        ];

        $redis->set($id, json_encode($data,JSON_UNESCAPED_UNICODE));

        $reflectionProperty = new \ReflectionProperty(News::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($News, $id);

    }

    public function delete(News $News): void
    {
        // TODO: Implement delete() method.
    }

    public function report(): void
    {
        // TODO: Implement report() method.
         
    }
}