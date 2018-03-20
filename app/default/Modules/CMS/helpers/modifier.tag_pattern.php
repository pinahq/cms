<?php

use Pina\Modules\CMS\Tag;

function smarty_modifier_tag_pattern($title, $pattern, $tags)
{
    return Tag::pattern($title, $pattern, $tags);
}
