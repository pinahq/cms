<?php

use \Pina\Modules\Images\ImageTag;

function smarty_function_img($params, &$view)
{
    $i = new ImageTag($params);
    return $i->render();
}
