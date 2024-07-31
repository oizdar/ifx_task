<?php

declare(strict_types=1);

namespace App\Account\Domain\ValueObject;

use Money\Money;
use Webmozart\Assert\Assert;

class Payment
{
    public function __construct(
        protected Money $money,
    ) {
        Assert::true($money->greaterThanOrEqual(new Money(0, $money->getCurrency())), 'Amount must be greater or equals 0');
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
