<?php declare(strict_types=1);

namespace App;

use App\Exception\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Storage\KeyValueStoreInterface;

class App
{
    private Request $request;
    private Response $response;
    private KeyValueStoreInterface $storage;

    public function __construct(KeyValueStoreInterface $storage)
    {
        $this->storage = $storage;
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run(): Response
    {
        if ($this->request->isUrl('/search')) {
            return $this->search();
        }

        if ($this->request->isUrl('/add')) {
            return $this->add();
        }

        return $this->response->send(200, []);
    }

    private function search(): Response
    {
        $requestParams = $this->request->getQueryParams();
        $events = $this->storage->zRevRange('events', 0, -1);
        $result = null;

        foreach ($events as $event) {
            $data = json_decode($event, true);

            if ($data === null || !isset($data['conditions'])) {
                continue;
            }

            if (empty(array_diff_assoc($requestParams, $data['conditions']))) {
                $result = $data;
                break;
            }
        }

        return $this->response->send(200, [
            'result' => $result
        ]);
    }

    private function add(): Response
    {
        try {
            $data = $this->request->getJsonData();

            if (!isset($data['priority']) || !is_numeric($data['priority'])) {
                throw new HttpException('Invalid priority');
            }

            $encodedData = json_encode($data);

            if ($encodedData === false) {
                throw new HttpException('Invalid JSON data');
            }

            $this->storage->zAdd('events', $data['priority'], $encodedData);

            return $this->response->send(200, [
                'event' => $data
            ]);
        } catch (HttpException $e) {
            return $this->response->send(400, ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->response->send(500, ['error' => $e->getMessage()]);
        }
    }
}
