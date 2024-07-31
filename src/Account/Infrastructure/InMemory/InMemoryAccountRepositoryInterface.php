<?php

declare(strict_types=1);

namespace App\Account\Infrastructure\InMemory;

use App\Account\Domain\Model\Account;
use App\Account\Domain\Repository\AccountRepositoryInterface;
use App\Account\Domain\ValueObject\Payment;
use Doctrine\Common\Collections\ArrayCollection;

final class InMemoryAccountRepositoryInterface implements AccountRepositoryInterface
{
    /**
     * @var ArrayCollection<int, Account>
     */
    private ArrayCollection $entities;

    public function __construct()
    {
        $this->entities = new ArrayCollection();
    }

    public function create(int $id, Account $budget): void
    {
        $this->entities->add($budget);
    }

    public function get(int $id): Account
    {
        return $this->entities->get($id);
    }

    public function credit(Account $account, Payment $payment): void
    {
        $account->credit($payment);
    }

    public function debit(Account $account, Payment $payment): void
    {
        $account->debit($payment);
    }
}
