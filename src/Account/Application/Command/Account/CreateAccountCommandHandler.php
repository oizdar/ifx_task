<?php

namespace App\Account\Application\Command\Account;

use App\Account\Domain\Model\Account;
use App\Account\Domain\Repository\AccountRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Money\Money;

final readonly class CreateAccountCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        public AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(CreateAccountCommand $command): void
    {
        $this->accountRepository->create(
            $command->id,
            new Account(
                $command->currency,
                new Money(0, $command->currency),
            )
        );
    }
}
