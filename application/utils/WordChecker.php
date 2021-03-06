<?php namespace utils;


class WordChecker
{
    private const FORBIDDEN_WORDS = [
        '/cul[oi0]/','/c[a4]zz[0oi]/','/str[o0]nz[aioe40]/',
        '/putt[a4]n/','/m[e3]rd[a4]/','/ricchi[o0]n[e3]/',
        '/fr[o0]c/','/n[e3]gr/','/b[o0u][ck]+[i1l]n[0oi1la]/'];


    public static function has_forbidden_word(string $string): bool {
        foreach (self::FORBIDDEN_WORDS as $pattern) {
            if (preg_match($pattern, str_replace('/[,.-_]/', '', strtolower($string)))) return true;
        }
        return false;
    }

    public static function has_special_characters(string $string): bool {
        return !preg_match('/^[A-zÀ-ú0-9 \-_.]+$/i', $string);
    }
}