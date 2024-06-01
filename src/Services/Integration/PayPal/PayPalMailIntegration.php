<?php

namespace Jegulnomic\Services\Integration\PayPal;

use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\MailboxInterface;
use Ddeboer\Imap\Server;
use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\PayPal\Parsers\IncParser;
use Jegulnomic\Services\Integration\PayPal\Parsers\P2PParser;
use Jegulnomic\Services\Integration\PayPal\Parsers\ParserInterface;
use Jegulnomic\Services\Integration\PayPal\SearchCondition\IncIncomeConditionProvider;
use Jegulnomic\Services\Integration\PayPal\SearchCondition\IncWithdrawConditionProvider;
use Jegulnomic\Services\Integration\PayPal\SearchCondition\P2PIncomeConditionProvider;
use Jegulnomic\Services\Integration\PayPal\SearchCondition\P2PWithdrawConditionProvider;
use Jegulnomic\Services\Integration\PayPal\TransactionCreator\IncomeIncTransactionCreator;
use Jegulnomic\Services\Integration\PayPal\TransactionCreator\IncomeP2PTransactionCreator;

readonly class PayPalMailIntegration
{
    public const MAILS_COUNT_TO_CHECK = 5;

    private MailboxInterface $mailbox;

    private ConnectionInterface $connection;

    private ParserInterface $p2pParser;

    private ParserInterface $incParser;

    public function __construct(
        #[Inject(TransactionsFromSearchConditionCreator::class)]
        private TransactionsFromSearchConditionCreator $transactionsFromSearchCondition
    ) {
        $this->p2pParser = (new P2PParser(new IncomeP2PTransactionCreator()));
        $this->incParser = (new IncParser(new IncomeIncTransactionCreator()));
    }

    public function connect(): self
    {
        $server = new Server(
            'posteo.de',
            993,
            '/ssl'
        );

        $this->connection = $server->authenticate(
            $_ENV['IMAP_LOGIN'],
            $_ENV['IMAP_PASSWORD']
        );

        $this->mailbox = $this->connection->getMailbox('INBOX');

        return $this;
    }

    public function getAllIncomes(): array
    {
        return array_merge($this->getP2PIncomes(), $this->getIncIncomes());
    }

    public function getP2PIncomes(): array
    {
        return
            $this
                ->transactionsFromSearchCondition
                ->getTransactionsByCondition(
                    $this->mailbox,
                    (new P2PIncomeConditionProvider())->create(),
                    $this->p2pParser
                );
    }

    public function getIncIncomes(): array
    {
        return
            $this
                ->transactionsFromSearchCondition
                ->getTransactionsByCondition(
                    $this->mailbox,
                    (new IncIncomeConditionProvider())->create(),
                    $this->incParser
                );
    }

    public function getAllWithdraw(): array
    {
        return array_merge($this->getP2PWithdraw(), $this->getIncWithdraw());
    }

    public function getP2PWithdraw(): array
    {
        return
            $this
                ->transactionsFromSearchCondition
                ->getTransactionsByCondition(
                    $this->mailbox,
                    (new P2pWithdrawConditionProvider())->create(),
                    $this->p2pParser
                );
    }

    public function getIncWithdraw(): array
    {
        return
            $this
                ->transactionsFromSearchCondition
                ->getTransactionsByCondition(
                    $this->mailbox,
                    (new IncWithdrawConditionProvider())->create(),
                    $this->incParser
                );
    }
}
