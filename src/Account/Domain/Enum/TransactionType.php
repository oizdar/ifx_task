<?php

declare(strict_types=1);

namespace App\Account\Domain\Enum;

enum TransactionType
{
    case CREDIT;
    case DEBIT;
}
