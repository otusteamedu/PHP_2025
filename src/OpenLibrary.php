<?php

namespace Elisad5791\Phpapp;

class OpenLibrary
{
    const BASE_URL = 'https://openlibrary.org';
    
    public function getSubject(string $subject): array
    {
        $response = file_get_contents(self::BASE_URL . '/subjects/' . $subject . '.json?limit=100');
        $data = json_decode($response, true);

        $subject = [
            'id' => $data['key'],
            'title' => $data['name'],
            'book_count' => $data['work_count'],
            'url' => self::BASE_URL . '/subjects/' . $subject . '.json',
        ];

        $books = [];
        foreach ($data['works'] as $item) {
            $author = implode(', ', array_column($item['authors'], 'name'));

            $books[] = [
                'id' => $item['key'],
                'subject_id' => $data['key'],
                'title' => $item['title'],
                'author' => $author,
                'first_publish_year' => $item['first_publish_year'],
                'page_count' => 1,
                'rating' => 2.5,
                'description' => 'description',
            ];
        }

        return ['subject' => $subject, 'books' => $books];
    }

    public function getDescription(string $bookId): string
    {
        $response = file_get_contents(self::BASE_URL . $bookId . '.json');
        $data = json_decode($response, true);
        $description = $data['description']['value'] ?? '';

        return $description;        
    }

    public function getRating(string $bookId): float
    {
        $response = file_get_contents(self::BASE_URL . $bookId . '/ratings.json');
        $data = json_decode($response, true);
        $rating = round($data['summary']['average'] ?? 0, 2);

        return $rating;        
    }

    public function getPageCount(string $bookId): int
    {
        $response = file_get_contents(self::BASE_URL . $bookId . '/editions.json');
        $data = json_decode($response, true);
        $entries = $data['entries'];

        $count = 0;
        foreach ($entries as $item) {
            if (isset($item['pagination'])) {
                $count = (int) $item['pagination'];
                if ($count > 0) {
                    break;
                }
            }
            if (isset($item['number_of_pages'])) {
                $count = (int) $item['number_of_pages'];
                if ($count > 0) {
                    break;
                }
            }
        }
        
        return $count;
    }
}