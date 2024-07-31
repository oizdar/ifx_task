<?php

namespace App\Account\Domain\Repository;

use App\Account\Domain\Exceptions\AccountNotFoundException;
use App\Account\Domain\Model\Account;
use App\Account\Domain\ValueObject\Payment;

interface AccountRepositoryInterface
{
    public function create(int $id, Account $budget): void;
    // todo: map Account to Doctrine entity and persist it, then account should be saved with mapped identifier in it,
    // Im not sure where should I use doctrine mapping in DDD I saw some  examples where mapping  was used in Domain layer but it is not Framework agnostic imho

    /**
     * @throws AccountNotFoundException
     */
    public function get(int $id): Account;

    public function credit(Account $account, Payment $payment): void;

    public function debit(Account $account, Payment $payment): void;
}
