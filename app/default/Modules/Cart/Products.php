<?php

namespace Pina\Modules\Cart;

use Pina\Paging;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceFilterTagGateway;
use Pina\Modules\CMS\ResourceTextGateway;
use Pina\Modules\CMS\Config;

class Products
{

    protected $page = null;
    protected $paging = 96;
    protected $sort = null;

    public function __construct($sort, $page, $paging = null)
    {
        $this->page = $page;
        $this->sort = $sort;
        if ($paging) {
            $this->paging = $paging;
        }
    }

    public function search($parentId, $length, $tagId, $token, $sale)
    {
        $resourceTagId = 0;
        $resourceType = 'products';

        $resourceType = \Pina\Modules\CMS\ResourceTypeGateway::instance()->whereBy('type', $resourceType)->select('pattern')->select('id')->select('type')->first();

        if (empty($resourceType)) {
            return \Pina\Response::notFound();
        }

        $resourceTypeId = $resourceType['id'];

        $needGroupBy = false;

        $gwSearch = ResourceTreeGatewayExtension::instance()
            ->whereBy('resource_type_id', $resourceTypeId)
            ->whereBy('resource_enabled', 'Y')
            ->whereBy('resource_parent_id', $parentId);
        if (!empty($length)) {
            $gwSearch->whereBy('length', range(1, $length));
        }

        $resourceGw = ResourceGatewayExtension::instance()->on('id', 'resource_id');
        if ($token) {
            $gwSearch->innerJoin(
                ResourceGatewayExtension::instance()->on('id', 'resource_id')
            );
            $gwSearch->leftJoin(
                ResourceTextGateway::instance()->on('resource_id')
            );
            $gwSearch->leftJoin(
                ResourceTagGateway::instance()->on('resource_id')->alias('token_resource_tag')
                    ->leftJoin(
                        TagGateway::instance()->on('id', 'tag_id')->alias('token_tag')
                    )
            );
            
            $gwSearch->where("MATCH(resource.title) AGAINST ('".$token."') OR MATCH(resource_text.text) AGAINST ('".$token."') OR MATCH(token_tag.tag) AGAINST ('".$token."')");
            $gwSearch->whereLike(['resource.title', 'resource_text.text', 'token_tag.tag'], '%' . $token . '%');
            $needGroupBy = true;
        }

        if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
            $gwSearch->innerJoin(
                //ResourceAmountGateway::instance()->on('resource_id')->whereInStock()
                ResourceInStockGateway::instance()->on('resource_id')
            );
        }

        if ($sale == 'Y') {
            $gwSearch->innerJoin(
                ResourcePriceGateway::instance()->on('resource_id')->where('resource_price.sale_price < resource_price.price')->where('resource_price.sale_price > 0')
            );
            $needGroupBy = true;
        }

        $gwTags = clone($gwSearch);
        $tags = ResourceFilterTagGateway::instance()->getFilterTags($gwTags->on('resource_id'));
        list($tags, $selectedTags) = $this->prepareTags($tags, $tagId);

        $gwSearch->whereFilterTagIds($tagId, $needGroupBy);
                
        if ($needGroupBy) {
            $gwSearch->groupBy('resource_tree.resource_id');
        }

        $paging = new Paging($this->page, $this->paging);
        $gwSearch->paging($paging, $needGroupBy ? "DISTINCT resource_tree.resource_id" : false);
        
        $needGetGroupBy = false;
        
        $gw = \Pina\SQL::subquery($gwSearch->select('resource_id')->select('resource_order'))->alias('search');
        $gw->innerJoin(
            ResourceGatewayExtension::instance()->on('id', 'resource_id')->setListView($needGetGroupBy)
        );
        
        if ($needGetGroupBy) {
            $gw->groupBy('resource.id');
        }

        switch ($this->sort) {
            case 'price': 
                $gwSearch->leftJoin(
                    ResourcePriceGateway::instance()->on('resource_id')
                );
                $gwSearch->orderBy('actual_price ASC');
                $gw->orderBy('actual_price ASC');
                break;
            case '-price':
                $gwSearch->leftJoin(
                    ResourcePriceGateway::instance()->on('resource_id')
                );
                $gwSearch->orderBy('actual_price DESC');
                $gw->orderBy('actual_price DESC');
                break;
            case 'title':
                if (!$token) {
                    $gwSearch->innerJoin(
                        ResourceGatewayExtension::instance()->on('id', 'resource_id')
                    );
                }
                $gwSearch->orderBy('resource.title', 'asc');
                $gw->orderBy('resource.title', 'asc');
                break;
            case '-title':
                if (!$token) {
                    $gwSearch->innerJoin(
                        ResourceGatewayExtension::instance()->on('id', 'resource_id')
                    );
                }
                $gwSearch->orderBy('resource.title', 'desc');
                $gw->orderBy('resource.title', 'desc');
                break;
            default:
                $gwSearch->orderBy('resource_tree.resource_order ASC');
                $gw->orderBy('search.resource_order ASC');
        }

        $rs = $gw->get();
        
        $rs = Discount::applyList($rs, 'discount_percent');

        return [
            'tag_types' => \Pina\Arr::group($tags, 'type'),
            'selected_tags' => $selectedTags,
            'resources' => $rs,
            'type' => $resourceType,
            'paging' => $paging->fetch(),
        ];
    }

    public function searchTagged($resourceTagId, $tagId, $sale)
    {
        if (empty($resourceTagId)) {
            $paging = new Paging($this->page, $this->paging);
            return [
                'resources' => [],
                'paging' => $paging->fetch(),
                'tag_types' => [],
                'selected_tags' => [],
            ];
        }
        $resourceType = 'products';
        
        $needGroupBy = false;

        $gw = ResourceGatewayExtension::instance()->whereResourceType($resourceType);
        $gw->whereEnabled();

        $gw->whereTagIds($resourceTagId);
        if (Config::get(__NAMESPACE__, 'display_out_of_stock') !== 'Y') {
            $gw->whereInStock();
        }

        $gwTags = clone($gw);

        $selectedTagIds = $tagId;
        $gw->whereFilterTagIds($selectedTagIds, $needGroupBy);

        $paging = new Paging($this->page, $this->paging);
        $gw->paging($paging, $needGroupBy ? "DISTINCT resource.id" : false);

        $needGetGroupBy = false;
        $gw->setListView($needGetGroupBy);

        switch ($this->sort) {
            case 'price': $gw->orderBy('actual_price ASC');
                break;
            case '-price': $gw->orderBy('actual_price DESC');
                break;
            case 'title': $gw->orderBy('resource.title', 'ASC');
                break;
            case '-title': $gw->orderBy('resource.title', 'DESC');
                break;
            default: $gw->orderBy('resource.order', 'ASC');
                break;
        }

        if ($needGroupBy || $needGetGroupBy) {
            $gw->groupBy('resource.id');
        }
        $rs = $gw->get();
        $rs = Discount::applyList($rs, 'discount_percent');
        
        $pg = $paging->fetch();

        $tags = ResourceFilterTagGateway::instance()->getFilterTags($gwTags->on('id', 'resource_id'));
        list($tags, $selectedTags) = $this->prepareTags($tags, $selectedTagIds, $resourceTagId);

        return [
            'resources' => $rs,
            'paging' => $pg,
            'tag_types' => \Pina\Arr::group($tags, 'type'),
            'selected_tags' => $selectedTags,
        ];
    }

    private function prepareTags($tags, $selectedTagIds, $resourceTagId = 0)
    {
        $selectedTags = [];
        if (is_array($selectedTagIds)) {
            foreach ($tags as $k => $tag) {
                if (in_array($tag['id'], $selectedTagIds)) {
                    $selectedTags[] = $tag;
                    $tags[$k]['selected'] = true;
                }
            }
        }


        foreach ($tags as $k => $tag) {
            if ($tag['id'] === $resourceTagId) {
                //не показываем фильтры по тегу, который уже связан с родительским ресурсом. 
                //т.к. фильтровать по нему бессмысленно
                //например, при просмотре товаров бренда бессмысленно показывать выбор бренда
                unset($tags[$k]);
                continue;
            }
            list($tags[$k]['type'], $tags[$k]['tag']) = explode(': ', $tag['tag'], 2);
        }

        return [$tags, $selectedTags];
    }

}
