<?php

namespace Pina\Modules\CMS;

use Pina\DB;

Class MySQLFullTextSearch
{
    public static function prepare($search)
    {
        $stopWords = [
            'a', 'about', 'an', 'are', 'as', 'at', 'be', 'by',
            'com', 'de', 'en', 'for', 'from', 'how', 'i', 'in',
            'is', 'it', 'la', 'of', 'on', 'or', 'that', 'the',
            'this', 'to', 'was', 'what', 'when', 'where', 'who',
            'will', 'with', 'und','the', 'www'
        ];

        $search = trim($search);
        $search = preg_replace('/[^0-9a-zа-я]/iu', ' ', $search);
        $words = explode(' ', $search);
        foreach ($words as $k => $word) {
            $word = trim($word);

            if (in_array(strtolower($word), $stopWords) || empty($word)) {
                unset($words[$k]);
                continue;
            }

            if (strlen($word) < 3) {
                $word = $word .'*';
            } else {
                $word = '+'. $word .'*';
            }
            
            $words[$k] = $word;
        }
        
        return implode($words, ' ');
    }
}
