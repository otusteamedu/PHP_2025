<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Query\QueryHandlerInterface;
use App\Application\UseCase\Query\FindEvent\FindEventQuery;
use App\Application\UseCase\Query\FindEvent\FindEventQueryHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class FindAction extends BaseAction
{
    private QueryHandlerInterface $queryHandler;

    public function __construct()
    {
        $this->queryHandler = new FindEventQueryHandler();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $id = $request->get('id');
        if (is_null($id)) {
            throw new \InvalidArgumentException('Id parameter is required');
        }
        $query = new FindEventQuery($id);
        $result = ($this->queryHandler)($query);

        return $this->responseSuccess($result)->asJson();
    }

}