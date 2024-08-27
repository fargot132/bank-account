<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account;

use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use App\BankAccount\Domain\Account\Event\CreditTransactionAddedEvent;
use App\BankAccount\Domain\Account\Event\DebitTransactionAddedEvent;
use App\BankAccount\Domain\Account\Exception\CurrencyMismatchException;
use App\BankAccount\Domain\Account\Exception\InsufficientFundsException;
use App\BankAccount\Domain\Account\Exception\TooManyDebitTransactionsException;
use App\BankAccount\Domain\Account\Transaction\Transaction;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\Balance;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\SharedKernel\Domain\AggregateRoot;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id as TransactionId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Account extends AggregateRoot
{
    private const float DEFAULT_FEE_PERCENT = 0.5;
    private const int DEFAULT_BALANCE = 0;
    private const int MAX_DEBIT_TRANSACTION_PER_DAY = 3;

    private string $id;

    private Currency $currency;

    private Balance $balance;

    private FeePercent $feePercent;

    private \DateTimeImmutable $createdAt;

    /** @phpstan-ignore-next-line */
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, Transaction> */
    private Collection $transactions;

    private function __construct(Id $id, Currency $currency, FeePercent $feePercent, Balance $balance)
    {
        $this->id = (string)$id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
        $this->transactions = new ArrayCollection();
        $this->currency = $currency;
        $this->balance = $balance;
        $this->feePercent = $feePercent;
    }

    public static function create(
        Id $id,
        Currency $currency,
        ?FeePercent $feePercent = null
    ): self {
        if ($feePercent === null) {
            $feePercent = new FeePercent(self::DEFAULT_FEE_PERCENT);
        }
        $account = new self($id, $currency, $feePercent, new Balance(self::DEFAULT_BALANCE));
        $account->raise(new AccountCreatedEvent($id));

        return $account;
    }

    /**
     * @throws CurrencyMismatchException|InsufficientFundsException|TooManyDebitTransactionsException
     */
    public function addTransaction(Transaction $transaction): void
    {
        if ($this->hasTransactionWithId($transaction->getId())) {
            return;
        }

        if ($this->currency !== $transaction->getCurrency()) {
            throw new CurrencyMismatchException();
        }

        if ($transaction->getType() === Type::CREDIT) {
            $this->balance = $this->balance->add($transaction->getTotalAmount());
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
            $this->updatedAt = new DateTimeImmutable();

            $this->raise(new CreditTransactionAddedEvent($transaction->getId(), new Id($this->id)));

            return;
        }

        if ($this->countDebitTransactionsToday() >= self::MAX_DEBIT_TRANSACTION_PER_DAY) {
            throw new TooManyDebitTransactionsException();
        }

        $transaction->calculateFee($this->feePercent);
        if ($this->balance->isGreaterOrEqual($transaction->getTotalAmount()) === false) {
            throw new InsufficientFundsException();
        }

        $this->balance = $this->balance->substract($transaction->getTotalAmount());
        $this->transactions->add($transaction);
        $transaction->setAccount($this);
        $this->updatedAt = new DateTimeImmutable();
        $this->raise(new DebitTransactionAddedEvent($transaction->getId(), new Id($this->id)));
    }

    private function hasTransactionWithId(TransactionId $id): bool
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    private function countDebitTransactionsToday(): int
    {
        $today = new DateTimeImmutable('today');
        $count = 0;

        /** @var Transaction $transaction */
        foreach ($this->transactions as $transaction) {
            if ($transaction->getType() === Type::DEBIT && $transaction->getDate() >= $today) {
                $count++;
            }
        }

        return $count;
    }
}
