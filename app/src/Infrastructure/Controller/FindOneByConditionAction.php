<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Query\QueryHandlerInterface;
use App\Application\UseCase\Query\FindOneByCondition\FindOneByConditionQuery;
use App\Application\UseCase\Query\FindOneByCondition\FindOneByConditionQueryHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class FindOneByConditionAction extends BaseAction
{
    private QueryHandlerInterface $queryHandler;

    public function __construct()
    {
        $this->queryHandler = new FindOneByConditionQueryHandler();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $params = $request->post('params');
        if (empty($params)) {
            throw new \InvalidArgumentException('Params parameter is required');
        }
        if (!is_array($params)) {
            throw new \InvalidArgumentException('Params must be an array');
        }
        $query = new FindOneByConditionQuery($params);
        $result = ($this->queryHandler)($query);

        return $this->responseSuccess($result)->asJson();
    }

}