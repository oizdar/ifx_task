<?php

namespace App\Account\Application\Command\Account;

use App\Account\Domain\ValueObject\Payment;
use App\Shared\Application\Command\CommandInterface;

final readonly class AddCreditPaymentCommand implements CommandInterface
{
    public function __construct(
        public int $accountId,
        public Payment $amount,
    ) {
    }
}
