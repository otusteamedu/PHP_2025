<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Query\QueryHandlerInterface;
use App\Application\UseCase\Query\GetPagedUsers\GetPagedUsersQuery;
use App\Application\UseCase\Query\GetPagedUsers\GetPagedUsersQueryHandler;
use App\Domain\Repository\Pager;
use App\Domain\Repository\UserFilter;
use App\Domain\Service\AssertService;
use App\Infrastructure\Http\Request;

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