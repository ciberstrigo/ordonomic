<?php

namespace Jegulnomic\Systems;

use Jegulnomic\Entity\RemittanceOperator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Ramsey\Uuid\Uuid;

class Authenticator
{
    private const int SESSION_LIFE_TIME = 60 * 60; // 1 hour

    public static function authenticate(string $telegramUserId, string $password): RemittanceOperator
    {
        if (empty($telegramUserId) || empty($password)) {
            throw new \RuntimeException('No login or password specified');
        }

        $operator = self::getRemittanceOperator($telegramUserId);

        if (!$operator) {
            throw new \RuntimeException('User not found');
        }

        if ($operator->sessionUntil > time()) {
            return $operator;
        }

        if (!password_verify($password, $operator->passwordHashed)) {
            throw new \RuntimeException('Incorrect password');
        }

        $operator->sessionUntil = time() + self::SESSION_LIFE_TIME;

        DatabaseStorage::i()->save($operator);

        return $operator;
    }

    public static function register(string $telegramUserId, string $password): RemittanceOperator
    {
        if (empty($password)) {
            throw new RuntimeException('Password can not be empty');
        }

        $operator = self::getRemittanceOperator($telegramUserId);

        if ($operator) {
            throw new \RuntimeException('Operator already exist!');
        }

        $operator = new RemittanceOperator(
            Uuid::uuid4(),
            $telegramUserId,
            time() + self::SESSION_LIFE_TIME,
            password_hash($password, PASSWORD_BCRYPT),
            0
        );

        $id = DatabaseStorage::i()->save($operator);

        if (!$id) {
            throw new RuntimeException('Error occurred while trying to register new operator');
        }

        return $operator;
    }

    public static function getRemittanceOperator(string $telegramUserId): ?RemittanceOperator
    {
        $result = DatabaseStorage::i()->get(
            RemittanceOperator::class,
            'WHERE telegram_user_id = :telegram_user_id',
            [':telegram_user_id' => $telegramUserId]
        );

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    public static function updateSession($telegramUserId): void
    {
        /** @var RemittanceOperator $operator */
        $operator = DatabaseStorage::i()->getOne(
            RemittanceOperator::class,
            'WHERE telegram_user_id = :telegram_user_id AND session_until > UNIX_TIMESTAMP()',
            [':telegram_user_id' => $telegramUserId]
        );

        if (!$operator) {
            throw new \RuntimeException('Can not update session, it\'s already expired.');
        }

        $operator->sessionUntil = time() + self::SESSION_LIFE_TIME;

        DatabaseStorage::i()->save($operator);
    }

    public static function getRegistrationLink($telegramUserId): string
    {
        return 'https://'
            . $_SERVER['HTTP_HOST']
            . '/remittance-operator/registration?telegram_user_id='
            . $telegramUserId;
    }

    public static function getLoginLink($telegramUserId): string
    {
        return 'https://'
            . $_SERVER['HTTP_HOST']
            . '/remittance-operator/login?telegram_user_id='
            . $telegramUserId;
    }
}
