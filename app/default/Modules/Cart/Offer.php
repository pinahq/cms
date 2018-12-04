<?php

namespace Pina\Modules\Cart;

use Pina\Arr;

use Pina\Modules\CMS\Tag;
use Pina\Modules\CMS\ListTagTypeGateway;
use Pina\Modules\CMS\ResourceTypeGateway;

use Pina\Modules\CMS\TagTypeGateway;

class Offer
{

    public static function download($gw, $encoding = 'utf8', $delimiter = ',', $enclosure = '"')
    {
        $tagHeaders = array();
        $offerTagHeaders = TagTypeGateway::instance()
            ->innerJoin(
                OfferTagTypeGateway::instance()->on('tag_type_id', 'id')
            )
            ->column('type');
        
        $resourceTagHeaders = TagTypeGateway::instance()
            ->innerJoin(
                ListTagTypeGateway::instance()->on('tag_type_id', 'id')
            )
            ->column('type');
        
        $tagHeaders = Arr::merge($resourceTagHeaders, $offerTagHeaders);
        
        $schema = [];
        $schema[] = ['id', __('Offer ID')];
        
        foreach ($tagHeaders as $tagHeader) {
            $schema[] = ['tags', $tagHeader];
        }
        
        $schema[] = ['title', __('Title')];
        $schema[] = ['amount', __('Amount')];
        $schema[] = ['cost_price', __('Cost Price')];
        $schema[] = ['price', __('Price')];
        $schema[] = ['sale_price', __('Sale Price')];
        
        header("Content-Type:application/csv;charset=UTF-8");
        header("Content-Disposition:attachment;filename=\"offers.csv\"");
        $os = $gw->get();
        
        $handle = fopen("php://output", "r+");

        $line = Arr::column($schema, 1);
        if ($encoding != 'utf8') {
            foreach ($line as $k => $v) {
                $line[$k] = iconv('utf8', $encoding, $v);
            }
        }
        fputcsv($handle, $line, $delimiter, $enclosure);
        foreach ($os as $k => $v) {
            $tags = array_merge(Tag::unserialize($v['resource_tags']), Tag::unserialize($v['tags']));
            $line = [];
            foreach ($schema as $columnSpec) {
                $column = $columnSpec[0];
                
                if ($column == 'tags') {
                    $value = '';
                    foreach ($tags as $tagKey => $tag) {
                        if ($tag['type'] === $columnSpec[1]) {
                            $value = $tag['value'];
                            unset($tags[$tagKey]);
                            break;
                        }
                    }
                    $line[] = $value;
                    continue;
                }
                
                if (!isset($v[$column])) {
                    $line[] = '';
                    continue;
                }

                $line[] = $v[$column];
            }

            if ($encoding != 'utf8') {
                foreach ($line as $k => $v) {
                    $line[$k] = iconv('utf8', $encoding, $v);
                }
            }
            
            fputcsv($handle, $line, $delimiter, $enclosure);
        }
        fclose($handle);
        exit;
    }

}
