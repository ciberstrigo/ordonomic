<?php

namespace Jegulnomic\Services\Integration;

use Jegulnomic\Services\IncomeFromParsedPayPalMailCreator;

class PayPalMailIntegration
{
    public const int MAILS_COUNT_TO_CHECK = 5;

    public static function getIncomes(): array
    {
        $connection = \imap_open(
            $_ENV['IMAP_SERVER'],
            $_ENV['IMAP_LOGIN'],
            $_ENV['IMAP_PASSWORD']
        );

        $mboxCheck = imap_check($connection);
        $totalMessages = $mboxCheck->Nmsgs;

        $result = array_reverse(
            imap_fetch_overview(
                $connection,
                (
                    $totalMessages - (
                        self::MAILS_COUNT_TO_CHECK > $totalMessages
                            ? $totalMessages - 1
                            : self::MAILS_COUNT_TO_CHECK + 1
                    )
                ) . ":" . $totalMessages
            )
        );

        $incomes = [];

        foreach ($result as $mail) {
            if (
                empty(
                    trim(
                        $mailBody = imap_fetchbody($connection, $mail->msgno, '1')
                    )
                )
            ) {
                $mailBody = imap_fetchbody($connection, $mail->msgno, '1.1');
            }

            $mailBody = preg_replace(
                '/\s+/',
                ' ',
                $mailBody
            );

            $header = imap_headerinfo($connection, $mail->msgno);
            $emailFrom = $header->from[0]->mailbox . "@" . $header->from[0]->host;

            if ($_ENV['RECEIVE_EMAIL_FROM'] !== $emailFrom) {
                continue;
            }

            if (!preg_match('/(.+) has sent you money/u', $header->subject, $incomeFromMatches)) {
                continue;
            }

            $transactionId = self::catch(
                $mailBody,
                'Transaction ID',
                '[0-9A-Za-z]+'
            );

            $transactionDate = self::catch(
                $mailBody,
                'Transaction date',
                '[0-9]{1,2} [A-Z][a-z]+ [0-9]{4}'
            );

            $transactionAmount = self::catch(
                $mailBody,
                'Money received',
                '[\$\p{Sc}]{1,2}[0-9]+,[0-9]{2}.*[A-Z]{3}'
            );

            if (!$transactionId || !$transactionDate || !$transactionAmount) {
                // weird email
                continue;
            }

            $incomes[] = IncomeFromParsedPayPalMailCreator::create(
                $transactionId,
                $transactionAmount,
                $transactionDate,
                $incomeFromMatches[1]
            );
        }

        imap_close($connection);

        return $incomes;
    }

    private static function catch(string $from, string $name, string $valueExpr): ?string
    {
        $regex = '/\*?'.$name.'\*?\s+('.$valueExpr.')/u';
        if (preg_match($regex, $from, $matches)) {
            if (count($matches) > 1) {
                return $matches[1];
            }
        }

        return null;
    }
}
