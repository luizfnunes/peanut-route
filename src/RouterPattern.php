<?php 

namespace Luizfnunes\PeanutRouter;

class RouterPattern
{
    public static array $list = [
        '{number}' => '/^[0-9]*$/',
        '{string}' => '/^[a-zA-Z]*$/',
        '{lower}'  => '/^[a-z]*$/',
        '{upper}'  => '/^[A-Z]*$/',
        '{alphanum}' => '/^[a-zA-Z0-9]$/',
        '{numberx}' => '/^[0-9_-]*$/',
        '{stringx}' => '/^[a-zA-Z_-]*$/',
        '{alphanumx}' => '/^[a-zA-Z0-9_-]$/',
    ];

    public static function extends(string $name, string $pattern)
    {
        $patternValid = '/^{[a-zA-Z]*}$/';
        // Se o formato do nome é válido
        if(@preg_match($patternValid, $name)){
            // Se o pattern é válido
            if(@preg_match($pattern, '') === false){
                return false;
            }
            self::$list[$name] = $pattern;
            return true;
        }
        return false;
    }
}