<?php

namespace Jegulnomic\Systems;

class Rest
{
    public static function get(
        string $url,
        array $params = [],
        array $headers = [],
        array $customOptions = []
    ): string {
        return self::request(
            $url,
            'GET',
            $headers,
            $params,
            $customOptions
        );
    }

    public static function post(
        string $url,
        array $params = [],
        array $headers = [],
        array $customOptions = []
    ): string {
        return self::request(
            $url,
            'POST',
            $headers,
            $params,
            $customOptions
        );
    }

    private static function request(
        string $url,
        string $requestType,
        array $headers,
        ?array $parametersUrlEncoded,
        array $customOptions = []
    ): string {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url.('GET' === $requestType ? '?'.http_build_query($parametersUrlEncoded) : ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ('POST' === $requestType) {
            if (array_key_exists('Content-Type', $headers) && $headers['Content-Type'] === 'application/json') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parametersUrlEncoded));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parametersUrlEncoded));
            }
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt_array($ch, $customOptions);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \LogicException(curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }
}
