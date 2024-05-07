<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;

#[Table(name: 'withdrawal')]
class Withdrawal
{
    public function __construct(
        #[Column(name: 'id')]
        readonly public string $id,
        #[Column(name: 'rate')]
        readonly public string $rate,
        #[Column(name: 'lari')]
        readonly public string $lari,
        #[Column(name: 'tax_lari')]
        readonly public string $taxLari,
        #[Column(name: 'interest_lari')]
        readonly public string $interestLari,
        #[Column(name: 'summary_lari')]
        readonly public string $summaryLari,
        #[Column(name: 'lari_to_rubbles')]
        readonly public string $lariToRubbles,
        #[Column(name: 'rubbles')]
        readonly public string $rubbles,
        #[Column(name: 'status')]
        public string $status,
        #[Column(name: 'sent_to_operator')]
        public ?RemittanceOperator $sentTo,
        #[Column(name: 'message_id')]
        public ?string $messageId
    ) {}
}