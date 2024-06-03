<?php

namespace Jegulnomic\Systems\Controller\Attributes;

use Attribute;
use Symfony\Component\HttpFoundation\Response;
use TimJMasters\JWS\JWSUtil;

#[Attribute]
class Signature
{
    public function __construct(
        string $secret,
        string $method = 'GET',
        ?string $field = null
    ) {
        $secret = $_ENV[$secret];
        $payload = 'GET' === $method ? $_GET : $_POST;
        unset($payload['q']);
        unset($payload['signature']);

        if ($field) {
            $payload = json_decode($_POST[$field], true);
        }

        if (!$this->verify(
            $payload,
            $_GET['signature'],
            $secret
        )) {
            if ('local' === $_ENV['APP_ENV']) {
                echo 'Signature is incorrect.' . PHP_EOL;
                echo 'Correct signature: ' . $this->signature($payload, $secret);
            }

            http_response_code(Response::HTTP_FORBIDDEN);
            die;
        }
    }

    private function verify(array $payload, string $signature, string $secret): bool
    {
        try {
            $jws = JWSUtil::createFromEncoded($signature);

            if ($jws->getPayload() !== $payload) {
                return false;
            }

            return JWSUtil::verify($jws, $secret);
        } catch (\Exception) {
            return false;
        }
    }

    private function signature(array $payload, string $secret)
    {
        return JWSUtil::createFromPayload(
            $payload,
            [
                "secret" => $secret,
                "payload" => [
                    "encoding" => JWSUtil::PAYLOAD_AS_JSON
                ]
            ]
        );
    }
}