<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\GetPagedUsers;

use App\Application\DTO\User\UserDTOTransformer;
use App\Application\Query\QueryHandlerInterface;
use App\Domain\Repository\Pager;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\UserRepository;

class GetPagedUsersQueryHandler implements QueryHandlerInterface
{
    private UserRepositoryInterface $userRepository;
    private UserDTOTransformer $userDTOTransformer;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userDTOTransformer = new UserDTOTransformer();
    }

    public function __invoke(GetPagedUsersQuery $query): GetPagedUsersQueryResult
    {
        $paginator = $this->userRepository->getByFilter($query->filter);

        $users = $this->userDTOTransformer->fromEntityList($paginator->items);
        $pager = new Pager(
            $query->filter->pager->page,
            $query->filter->pager->per_page,
            $paginator->total
        );

        return new GetPagedUsersQueryResult($users, $pager);
    }
}