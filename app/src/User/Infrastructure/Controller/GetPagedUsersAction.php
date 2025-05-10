<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Domain\Repository\Pager;
use App\Shared\Domain\Service\AssertService;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;
use App\User\Application\UseCase\Query\GetPagedUsers\GetPagedUsersQuery;
use App\User\Application\UseCase\Query\GetPagedUsers\GetPagedUsersQueryHandler;
use App\User\Domain\Repository\UserFilter;

class GetPagedUsersAction extends BaseAction
{
    private QueryHandlerInterface $handler;

    public function __construct()
    {
        $this->handler = new GetPagedUsersQueryHandler();
    }

    public function __invoke(Request $request)
    {
        $page = $request->post('page');
        if (!$page) {
            $page = Pager::DEFAULT_PAGE;
        }
        AssertService::integer($page, message: 'page must be a positive integer');
        $limit = $request->post('limit');
        if (!$limit) {
            $limit = Pager::DEFAULT_LIMIT;
        }
        AssertService::integer($limit, message: 'limit must be a positive integer');
        $filter = new UserFilter(new Pager($page, $limit));
        $query = new GetPagedUsersQuery($filter);
        $result = ($this->handler)($query);

        return $this->responseSuccess($result)->asJson();
    }
}
