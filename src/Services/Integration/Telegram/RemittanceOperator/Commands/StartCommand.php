<?php

namespace Jegulnomic\Services\Integration\Telegram\RemittanceOperator\Commands;

use Jegulnomic\Systems\Authenticator;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start using our application. Login or pass the registration though this command.';

    public function handle(Nutgram $bot): void
    {
        $id = $bot->userId();
        $operator = Authenticator::getRemittanceOperator($id);

        if (!$operator) {
            $bot->sendMessage(
                'Вас нет в списке операторов. Пройдите регистрацию. ',
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make(
                            'Регистрация',
                            web_app: WebAppInfo::make(Authenticator::getRegistrationLink($id))
                        )
                    )
            );
            return;
        }

        if (!$operator->isVerified) {
            $bot->sendMessage('Ваш аккаунт не верифицирован. Ожидайте верификацию администратора.');
            return;
        }

        if (!$operator->isAllowToProceed()) {
            $bot->sendMessage(
                'Пожалуйста, войдите в систему чтобы продолжать получать уведомления. ',
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make(
                            'Вход',
                            web_app: WebAppInfo::make(Authenticator::getLoginLink($id))
                        )
                    )
            );
            return;
        }
        Authenticator::updateSession($id);

        $bot->sendMessage('Вы находитесь в системе. Ваша сессия обновлена и действительна до: '
                . date("d F Y H:i:s", $operator->sessionUntil));
    }
}
