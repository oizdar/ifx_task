<?php

namespace App\Account\Application\Query;

use App\Account\Domain\Model\Account;
use App\Account\Domain\Repository\AccountRepositoryInterface;

class FindAccountQueryHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(FindAccountQuery $query): Account // maybe will be better to return here any type of resource object
    {
        return $this->accountRepository->get($query->id);
    }
}
