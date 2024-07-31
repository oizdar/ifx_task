<?php

declare(strict_types=1);

namespace App\Tests\Account\Domain\ValueObject;

use App\Account\Domain\ValueObject\Payment;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function testCannotCreatePaymentWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be greater or equals 0');

        new Payment(new Money(-1, new Currency('PLN')));
    }

    public function testCreatePayment(): void
    {
        $payment = new Payment(new Money(100, new Currency('PLN')));

        $this->assertEquals(new Money(100, new Currency('PLN')), $payment->getMoney());
    }
}
