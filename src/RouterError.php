<?php

namespace Luizfnunes\PeanutRouter;

use stdClass;

class RouterError
{
    private static bool $hasError = false;
    private static array $errors;

    public static function registerError(string $code, string $message)
    {
        /**
         * Registra um novo erro
         */
        self::$hasError = true;
        $error = new stdClass();
        $error->code = $code;
        $error->message = $message;
        self::$errors[] = $error;
    }

    public static function hasError()
    {
        /**
         * Verifica se existem erros
         */
        return self::$hasError;
    }

    public static function getErrors()
    {
        /**
         * Retorna os erros
         */
        return self::$errors;
    }
}