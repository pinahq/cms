<?php

use Pina\Modules\CMS\Tag;

function smarty_modifier_substract_tag_pattern($tags, $pattern)
{
    return Tag::substract_pattern($tags, $pattern);
}
