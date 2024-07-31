<?php

declare(strict_types=1);

namespace App\Account\Domain\Model;

use App\Account\Domain\Enum\TransactionType;
use Money\Money;

class Transaction
{
    public function __construct(
        protected Money $amount,
        protected TransactionType $type,
        protected \DateTimeImmutable $date,
    ) {
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
