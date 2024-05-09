<?php

return static function (string $path, array $parameters): void {
    $result = preg_replace_callback(
        '/((\/|-)[a-z])/i',
        function ($word) {
            return ('/' === $word[1][0] ? '\\' : '') . strtoupper($word[1][1]);
        },
        $path
    );

    $regexpToSearchMethod = '/\\\[A-Za-z0-9]+$/';
    $classPath = "Jegulnomic\Controller" . preg_replace($regexpToSearchMethod, '', $result);

    if (!preg_match($regexpToSearchMethod, $result, $matches)) {
        http_response_code(404);
        echo('not found' . PHP_EOL);
        die;
    }

    $methodName = lcfirst(ltrim($matches[0], '\\'));

    if (!method_exists($classPath, $methodName)) {
        $alternativeClassName = $classPath . '\\' . ucfirst($methodName);
        if (method_exists($alternativeClassName, 'index')) {
            echo (new ($alternativeClassName))->index();
            return;
        }

        http_response_code(404);
        echo('not found' . PHP_EOL);
        return;
    }

    echo (new $classPath())->$methodName();
};
