<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Transaction;

use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Fee;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\SharedKernel\Domain\ValueObject\Currency;
use DateTimeImmutable;

class Transaction
{
    private string $id;

    /** @phpstan-ignore-next-line  */
    private ?Account $account;

    private Type $type;

    private Amount $amount;

    private Currency $currency;

    private Fee $fee;

    private DateTimeImmutable $createdAt;

    private function __construct(Id $id, Type $type, Amount $amount, Currency $currency)
    {
        $this->id = (string)$id;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->fee = new Fee(0);
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(Id $id, Type $type, Amount $amount, Currency $currency): self
    {
        return new self($id, $type, $amount, $currency);
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getTotalAmount(): Amount
    {
        return $this->amount->add($this->fee);
    }

    public function calculateFee(FeePercent $feePercent): void
    {
        $feeValue = round($this->amount->value() * $feePercent->toFraction());
        $this->fee = new Fee((int)$feeValue);
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
