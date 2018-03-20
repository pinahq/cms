<?php

namespace Pina\Modules\CMS;

use Pina\Request;

class Tag
{

    public static function unserialize($s)
    {
        $tags = explode("\n", $s);
        foreach ($tags as $k => $v) {
            $tags[$k] = explode(': ', $v, 2);
            $tags[$k] = array_map('trim', $tags[$k]);
        }
        return $tags;
    }

    public static function onlyTypes($tags, $types)
    {
        $cond = implode('|', $types);
        if (preg_match_all('/^(' . $cond . '):.*$/im', $tags, $matches)) {
            return implode("\n", $matches[0]);
        }
        return '';
    }

    public static function pattern($title, $pattern, $tags)
    {
        if (empty($pattern)) {
            return $title;
        }

        $title = str_replace('***', $title, $pattern);

        $tags = Tag::unserialize($tags);
        foreach ($tags as $tag) {
            if (!isset($tag[0]) || !isset($tag[1])) {
                continue;
            }
            $title = str_replace('{' . $tag[0] . '}', $tag[1], $title);
        }

        $title = preg_replace('/{[^}]*}/si', '', $title);

        return trim($title);
    }

    public static function substract_pattern($tags, $pattern)
    {
        if (empty($pattern)) {
            return $tags;
        }
        
        if (preg_match_all('/{([^}]*)}/si', $pattern, $matches)) {
            $cond = implode('|', $matches[1]);
            $tags = preg_replace('/^(' . $cond . '):.*$/im', '', $tags);
        }

        return $tags;
    }

}
