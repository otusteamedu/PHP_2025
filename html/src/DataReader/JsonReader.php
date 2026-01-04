<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\DataReader;

use Otus\Elasticsearch\Factory\DataFactory;
use Otus\Elasticsearch\Factory\IndexFactory;
use SplFileObject;

readonly class JsonReader implements ReaderInterface
{
    /**
     * @var SplFileObject
     */
    protected SplFileObject $data;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->data = new SplFileObject(
            $path,
        );

        $this->data->setFlags(SplFileObject::DROP_NEW_LINE | SplFileObject::SKIP_EMPTY);
    }

    /**
     * @param int $length
     *
     * @return array
     */
    public function getData(int $length = 100): array
    {
        $result = [];

        while (!$this->data->eof()) {
            $index = $this->getIndex();
            $body = $this->getBody();

            if (empty($index) || empty($body)) {
                break;
            }

            $ei = IndexFactory::factory($index);
            $eb = DataFactory::factory($body);

            $result['body'][] = $ei->toArray();
            $result['body'][] = $eb->toArray();

            if ($this->data->key() % ($length * 2) === 0) {
                break;
            }
        }

        return $result;
    }

    /**
     * @return array|null
     */
    protected function getIndex(): ?array
    {
        $index = $this->data->fgets();

        return json_decode($index, true);
    }

    /**
     * @return array|null
     */
    protected function getBody(): ?array
    {
        if ($this->data->eof()) {
            return [];
        }

        $body = $this->data->fgets();

        return json_decode($body, true);
    }
}
