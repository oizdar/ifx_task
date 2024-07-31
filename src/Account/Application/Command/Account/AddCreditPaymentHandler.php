<?php

namespace App\Account\Application\Command\Account;

use App\Account\Domain\Repository\AccountRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;

final readonly class AddCreditPaymentHandler implements CommandHandlerInterface
{
    public function __construct(
        public AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(AddCreditPaymentCommand $command): void
    {
        $account = $this->accountRepository->get($command->accountId);

        $this->accountRepository->credit(
            $account,
            $command->amount,
        );
    }
}
