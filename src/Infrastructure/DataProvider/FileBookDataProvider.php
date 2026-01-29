<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\DataProvider;

use Dinargab\Homework14\Application\DataProvider\BookDataProviderInterface;
use Dinargab\Homework14\Application\DTO\BookDTO;
use Generator;
use InvalidArgumentException;
use SplFileObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FileBookDataProvider implements BookDataProviderInterface
{
    public function __construct(
        #[Autowire(value: '%env(resolve:FILE_PATH)%')] private string $filePath
    ) {
        if ( ! file_exists($this->filePath)) {
            throw new InvalidArgumentException(
                "Invalid filepath: file does not exist"
            );
        }
    }

    public function load(): Generator
    {
        $file = new SplFileObject($this->filePath);

        $file->setFlags(
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        while ( ! $file->eof()) {
            $line = $file->fgets();

            if (empty($line)) {
                continue;
            }

            $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

            if (isset($data['create']) || isset($data['index']) || isset($data['delete']) || isset($data['update'])) {
                continue;
            }

            if ( ! isset($data['sku']) && ! isset($data['title'])) {
                continue;
            }

            yield new BookDTO(
                title: $data['title'] ?? '',
                sku: $data['sku'] ?? '',
                category: $data['category'] ?? '',
                price: (int)($data['price'] ?? 0),
                stock: $data['stock'] ?? []
            );
        }
    }
}
