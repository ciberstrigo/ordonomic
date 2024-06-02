<?php

namespace Jegulnomic\Systems;

use DI\Attribute\Inject;
use Jegulnomic\Entity\UserInterface;
use Jegulnomic\Systems\Controller\ControllerManager;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Ramsey\Uuid\Uuid;

abstract class BaseAuthenticator
{
    private const SESSION_LIFE_TIME = 60 * 60 * 24; // 1 hour

    public function __construct(
        #[Inject(DatabaseStorage::class)]
        protected StorageInterface $storage,
        #[Inject(PublicUrlProvider::class)]
        protected PublicUrlProvider $urlProvider
    ) {
    }

    public function authenticate(string $telegramUserId, string $password): UserInterface
    {
        if (empty($telegramUserId) || empty($password)) {
            throw new \RuntimeException('No login or password specified');
        }

        $operator = $this->getUser($telegramUserId);

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

        $this->storage->save($operator);

        return $operator;
    }

    public function register(string $telegramUserId, string $password): UserInterface
    {
        if (empty($password)) {
            throw new \RuntimeException('Password can not be empty');
        }

        $operator = $this->getUser($telegramUserId);

        if ($operator) {
            throw new \RuntimeException('Operator already exist!');
        }

        $operator = call_user_func(
            [$this->getUserClass(), 'register'],
            $telegramUserId,
            time() + self::SESSION_LIFE_TIME,
            password_hash($password, PASSWORD_BCRYPT)
        );

        $id = $this->storage->save($operator);

        if (!$id) {
            throw new \RuntimeException('Error occurred while trying to register new operator');
        }

        return $operator;
    }

    public function getUser(string $telegramUserId): ?UserInterface
    {
        $result = $this->storage->get(
            $this->getUserClass(),
            'WHERE telegram_user_id = :telegram_user_id',
            [':telegram_user_id' => $telegramUserId]
        );

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }

    public function updateSession($telegramUserId): void
    {
        /** @var UserInterface $operator */
        $operator = $this->storage->getOne(
            $this->getUserClass(),
            'WHERE telegram_user_id = :telegram_user_id AND session_until > UNIX_TIMESTAMP()',
            [':telegram_user_id' => $telegramUserId]
        );

        if (!$operator) {
            throw new \RuntimeException('Can not update session, it\'s already expired.');
        }

        $operator->sessionUntil = time() + self::SESSION_LIFE_TIME;

        $this->storage->save($operator);
    }

    public function logout(UserInterface $user): void
    {
        $user->endSession();
        $this->storage->save($user);
    }

    public function getRegistrationLink($payload): string
    {
        return $this->urlProvider->getControllerUrl($this->getRegistrationController())
            . '?' . http_build_query($payload);
    }

    public function getLoginLink($payload): string
    {
        return $this->urlProvider->getControllerUrl($this->getLoginController())
            . '?' . http_build_query($payload);
    }

    abstract protected function getUserClass(): string;

    abstract protected function getLoginController(): string;
    abstract protected function getRegistrationController(): string;
}
