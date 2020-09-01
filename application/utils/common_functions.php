<?php

/**
 * Genera una stringa random
 * da http://stackoverflow.com/questions/4356289/php-random-string-generator
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_windows_name(string $win_kern): string
{
    switch($win_kern)
    {
        case "5.0":
            $name = "Windows 2000";
            break;
        case "5.1":
            $name = "Windows XP";
            break;
        case "5.2":
            $name = "Windows XP - Edizione 64bit";
            break;
        case "6.0": $name = "Windows Vista";
            break;
        case "6.1": $name = "Windows 7";
            break;
        case "6.2": $name = "Windows 8";
            break;
        case "6.3": $name = "Windows 8.1";
            break;
        case "10.0": $name = "Windows 10";
            break;
        default: $name = "Versione sconosciuta";
            break;
    }
    return $name." (kernel ".$win_kern.")";
}

function password_encode(string $encoding): string {
    return  hash('sha3-512', $encoding); // 128 caratteri
}