<?php

use \Pina\Modules\Media\ImageTag;

function smarty_function_img($params, &$view)
{
    $i = new ImageTag($params);
    return $i->render();
}
