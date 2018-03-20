<?php

function smarty_modifier_tag($tags, $tag)
{
    preg_match('/'.$tag.':([^\n]*)/si', $tags, $matches);
    
    if (!empty($matches) && !empty($matches[1])) {
        return trim($matches[1]);
    }
    
	return '';
}