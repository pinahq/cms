<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Modules\CMS\ResourceGateway;
use Pina\Modules\CMS\ResourceTextGateway;
use Pina\Modules\CMS\ResourceTagGateway;
use Pina\Modules\CMS\ResourceTagsGateway;
use Pina\Modules\CMS\TagGateway;
use Pina\Modules\CMS\Config;
use Pina\Modules\CMS\MySQLFullTextSearch;
use Pina\SQL;
use Pina\Arr;
use Pina\Paging;

use Pina\Modules\CMS\ResourceTreeGateway;

$info = pathinfo(\Pina\App::resource());
$isDownloading = !empty($info['extension']) && in_array($info['extension'], array('xls', 'xlsx', 'csv'));

$gw = OfferGateway::instance()->select("*")->filters(Request::all());

if (Request::input('parent_id')) {
    $gw->innerJoin(
        ResourceTreeGateway::instance()->on('resource_id')->whereBy('resource_parent_id', Request::input('parent_id'))
    )
    ->orderBy('resource_tree.resource_order', 'ASC');
}

if (Request::input('tag_resource_id')) {
    $tagId = TagGateway::instance()->whereBy('resource_id', Request::input('tag_resource_id'))->id();
    $gw->innerJoin(
        ResourceGateway::instance()->on('id', 'resource_id')->whereTagIds($tagId)
    );
}

if (Request::input('search') || Request::input('tag_id') || Request::input('tag')) {
    $gw->leftJoin(ResourceGateway::instance()->on('id', 'resource_id'));
    $gw->leftJoin(ResourceTextGateway::instance()->on('resource_id'));

    $tagGW = TagGateway::instance();
    if (Request::input('tag_id')) {
        $tagId = intval(Request::input('tag_id'));
        $tagGW->whereBy('id', $tagId);
    } elseif (Request::input('tag') && Request::input('tag_type_id')) {
        $tag = MySQLFullTextSearch::prepare(Request::input('tag'));
        $tagGW->where("MATCH(tag.tag) AGAINST('$tag' IN BOOLEAN MODE)");
        $tagGW->whereBy('tag_type_id', Request::input('tag_type_id'));
    }

    $gw->leftJoin(ResourceTagGateway::instance()->on('resource_id')->leftJoin($tagGW->on('id', 'tag_id')));

    if (Request::input('search')) {
        $search = MySQLFullTextSearch::prepare(Request::input('search'));
        $condition = "MATCH(resource.title) AGAINST('$search' IN BOOLEAN MODE)";
        $condition .= " OR MATCH(resource_text.text) AGAINST('$search' IN BOOLEAN MODE)";
        $condition .= " OR MATCH(tag.tag) AGAINST('$search' IN BOOLEAN MODE)";
        $gw->where($condition);
    }
    
    $gw->groupBy('offer.id');
}

if (!$isDownloading) {
    $paging = new Paging(Request::input('page'), Request::input("paging") ? Request::input("paging") : 25);
    $gw->paging($paging, 'DISTINCT offer.id');
}

$resultGw = SQL::subquery($gw->orderBy('offer.resource_id ASC'))->alias('offers')->select('*')->leftJoin(
        ResourceGateway::instance()->on('id', 'resource_id')->select('title')
    )
    ->leftJoin(
        ResourceTagsGateway::instance()->on('resource_id')->selectAs('tags', 'resource_tags')
    );

    /*OfferGateway::withTags() start*/
    //TODO: найти хорошее решение, чтобы свернуть это в метод для случая, когда отталкиваемся от SQL::subquery
    $offerTagTypeGateway = OfferTagTypeGateway::instance();

    $resultGw->leftJoin($offerTagTypeGateway);
    $tagGw = TagGateway::instance()->on('id', 'tag_id')->on('tag_type_id', $offerTagTypeGateway->getAlias().'.tag_type_id')->concatTags('tags');
    /*OfferGateway::withTags() end*/
        
    $resultGw->leftJoin(OfferTagGateway::instance()->on('offer_id', 'id')->leftJoin($tagGw));
    $resultGw->orderBy('offers.resource_id ASC')->groupBy('offers.id');

if ($isDownloading) {
    $config = Config::getNamespace(__NAMESPACE__);

    $charset = empty($config['csv_charset']) ? 'utf8' : $config['csv_charset'];
    $delimiter = empty($config['csv_delimiter']) ? ';' : ($config['csv_delimiter']);

    Offer::download($resultGw, $charset, $delimiter);
    exit;
}

$os = $resultGw->get();

return [
    'paging' => $paging->fetch(),
    'offers' => $os,
];
