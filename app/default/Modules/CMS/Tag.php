<?php

namespace Pina\Modules\CMS;

use Pina\Request;

class Tag
{

    public static function unserialize($s)
    {
        $tags = explode("\n", $s);
        foreach ($tags as $k => $v) {
            $parts = array_map('trim', explode(': ', $v, 2));
            $count = \count($parts);
            if ($count > 1) {
                $tags[$k] = ['type' => $parts[0], 'value' => $parts[1]];
            } elseif ($count === 1)  {
                $tags[$k] = ['type' => '', 'value' => $parts[0]];
            }
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
            if (!isset($tag['type']) || !isset($tag['value'])) {
                continue;
            }
            $title = str_replace('{' . $tag['type'] . '}', $tag['value'], $title);
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
