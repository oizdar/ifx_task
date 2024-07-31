<?php

declare(strict_types=1);

namespace App\Tests\Account\Domain\Model;

use App\Account\Domain\Enum\TransactionType;
use App\Account\Domain\Model\Account;
use App\Account\Domain\Model\Transaction;
use App\Account\Domain\ValueObject\Payment;
use Doctrine\Common\Collections\ArrayCollection;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testCannotCreateAccountWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Balance must be greater or equals 0');

        new Account(new Currency('PLN'), new Money(-1, new Currency('PLN')));
    }

    public function testCannotCreateAccountWithBalanceInDifferentCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Account balance must be in the same currency as account');

        new Account(new Currency('PLN'), new Money(1, new Currency('USD')));
    }

    public function testCannotCreateAccountWithTransactionInDifferentCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Transaction currency must be the same as account currency');

        $transactions = new ArrayCollection();
        $transactions->add(new Transaction(new Money(1, new Currency('USD')), TransactionType::CREDIT, new \DateTimeImmutable()));
        new Account(new Currency('PLN'), new Money(1, new Currency('PLN')), $transactions);
    }

    public function testCreateAccount(): void
    {
        $account = new Account(new Currency('PLN'), new Money(0, new Currency('PLN')));

        $this->assertEquals(new Currency('PLN'), $account->getCurrency());
        $this->assertEquals(new Money(0, new Currency('PLN')), $account->getBalance());
        $this->assertEquals(new ArrayCollection(), $account->getTransactions());
    }

    public function testAddCreditPaymentsToAccount(): void
    {
        $account = new Account(new Currency('PLN'), new Money(0, new Currency('PLN')));

        $account->credit(new Payment(new Money(100, new Currency('PLN'))));
        $account->credit(new Payment(new Money(10, new Currency('PLN'))));
        $this->assertEquals(new Money(110, new Currency('PLN')), $account->getBalance());
    }

    public function testCannotAddCreditPaymentInOtherCurrencyToAccount(): void
    {
        $account = new Account(new Currency('PLN'), new Money(0, new Currency('PLN')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Transaction currency must be the same as account currency');

        $account->credit(new Payment(new Money(100, new Currency('USD'))));
    }

    public function testAddDebitPaymentsToAccountWithTransactionFee(): void
    {
        $account = new Account(new Currency('PLN'), new Money(1000, new Currency('PLN')));

        $account->debit(new Payment(new Money(100, new Currency('PLN'))));

        $expectedValue = new Money((int) (1000 - (100 * (1 + Account::TRANSACTION_DEBIT_FEE))), new Currency('PLN'));

        $this->assertEquals($expectedValue, $account->getBalance());
    }

    public function testCanAddOnlyTreeDebitTransactionsEachDay(): void
    {
        $account = new Account(new Currency('PLN'), new Money(1000, new Currency('PLN')));

        $account->debit(new Payment(new Money(100, new Currency('PLN'))));
        $account->debit(new Payment(new Money(100, new Currency('PLN'))));
        $account->debit(new Payment(new Money(100, new Currency('PLN'))));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You can add only 3 debit transactions each day');

        $account->debit(new Payment(new Money(100, new Currency('PLN'))));
    }

    public function testCannotAddDebitPaymentInOtherCurrencyToAccount(): void
    {
        $account = new Account(new Currency('PLN'), new Money(1000, new Currency('PLN')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Transaction currency must be the same as account currency');

        $account->debit(new Payment(new Money(100, new Currency('USD'))));
    }

    public function testCannotDebitMoreThanAccountBalance(): void
    {
        $account = new Account(new Currency('PLN'), new Money(1000, new Currency('PLN')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $account->debit(new Payment(new Money(1001, new Currency('PLN'))));
    }

    public function testCannotDebitMoreThanAccountBalanceDebitHasFee(): void
    {
        $account = new Account(new Currency('PLN'), new Money(1000, new Currency('PLN')));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient funds');

        $account->debit(new Payment(new Money(1000, new Currency('PLN'))));
    }
}
