<?php namespace utils;


class WordChecker
{
    private const FORBIDDEN_WORDS = [
        '/cul[oi0]/','/c[a4]zz[0oi]/','/str[o0]nz[aioe40]/',
        '/putt[a4]n/','/m[e3]rd[a4]/','/ricchi[o0]n[e3]/',
        ];


    public static function has_forbidden_word(string $string): bool {
        foreach (self::FORBIDDEN_WORDS as $pattern) {
            if (preg_match($pattern, strtolower($string))) return true;
        }
        return false;
    }

    public static function has_special_characters(string $string): bool {
        return preg_match('/\W/i', $string);
    }
}