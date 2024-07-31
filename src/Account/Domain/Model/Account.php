<?php

declare(strict_types=1);

namespace App\Account\Domain\Model;

use App\Account\Domain\Enum\TransactionType;
use App\Account\Domain\ValueObject\Payment;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;
use Money\Money;
use Webmozart\Assert\Assert;

class Account
{
    public const TRANSACTION_DEBIT_FEE = 0.05; // 5% fee

    /**
     * @var ArrayCollection<int, Transaction>
     */
    protected ArrayCollection $transactions; // I'm not sure if this Collection is acceptable, maybe should I create my own aggregate class?

    /**
     * @param ArrayCollection<int, Transaction>|null $transactions
     */
    public function __construct(
        protected Currency $currency,
        protected Money $balance,
        ?ArrayCollection $transactions = null
    ) {
        $this->transactions = $transactions ?? new ArrayCollection();

        Assert::true($balance->getCurrency()->equals($this->currency), 'Account balance must be in the same currency as account');
        Assert::true($balance->greaterThanOrEqual(new Money(0, $this->currency)), 'Balance must be greater or equals 0');

        foreach ($this->transactions as $transaction) {
            Assert::true($transaction->getAmount()->getCurrency()->equals($this->currency), 'Transaction currency must be the same as account currency');
        }
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    /**
     * @return ArrayCollection<int, Transaction>
     */
    public function getTransactions(): ArrayCollection
    {
        return $this->transactions;
    }

    public function credit(Payment $payment): void
    {
        if (!$this->currency->equals($payment->getMoney()->getCurrency())) {
            throw new \InvalidArgumentException('Transaction currency must be the same as account currency');
        }

        $this->transactions[] = new Transaction($payment->getMoney(), TransactionType::CREDIT, new \DateTimeImmutable());
        $this->balance = $this->balance->add($payment->getMoney());
    }

    public function debit(Payment $payment): void
    {
        if (!$this->currency->equals($payment->getMoney()->getCurrency())) {
            throw new \InvalidArgumentException('Transaction currency must be the same as account currency');
        }

        $debit = $payment->getMoney()->multiply(1 + self::TRANSACTION_DEBIT_FEE);

        if ($this->balance->lessThan($debit)) {
            throw new \InvalidArgumentException('Insufficient funds');
        }

        if ($this->countTodayTransactions() >= 3) {
            throw new \InvalidArgumentException('You can add only 3 debit transactions each day');
        }

        $this->transactions[] = new Transaction($debit, TransactionType::DEBIT, new \DateTimeImmutable());
        $this->balance = $this->balance->subtract($debit);
    }

    private function countTodayTransactions(): int
    {
        $today = new \DateTimeImmutable();

        return $this->transactions->filter(
            fn (Transaction $transaction) => $transaction->getDate()->format('Y-m-d') === $today->format('Y-m-d')
        )
            ->count();
    }
}
