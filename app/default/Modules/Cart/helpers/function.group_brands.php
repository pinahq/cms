<?php

function smarty_function_group_brands($params, &$smarty)
{
    if (!isset($params['brands']))
        return '';

    $brands = $params['brands'];

    $groupBrands = array();
    foreach ($brands as $brand) {
        $brandTitle = $brand['title'];
        $brandTitle = trim($brandTitle);
        $firstLetter = mb_strtoupper(mb_substr($brandTitle, 0, 1));

        if (is_numeric($firstLetter)) {
            $groupBrands['0-9'][] = $brand;
            continue;
        }

        $groupBrands[$firstLetter][] = $brand;
    }
    
    ksort($groupBrands);

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $groupBrands);
        $result = '';
    }

    return $result;

}
