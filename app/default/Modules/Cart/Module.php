<?php

namespace Pina\Modules\Cart;

use Pina\ModuleInterface;
use Pina\Event;
use Pina\Modules\CMS\ImportReaderRegistry;

class Module implements ModuleInterface
{

    public function getPath()
    {
        return __DIR__;
    }
    
    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'Cart';
    }

    public function http()
    {
        \Pina\Composer::placeModule('search', 'products');
        \Pina\Composer::placeView('cms.menu', 'cp/:cp/offers/block', ['display' => 'nav']);
        \Pina\Composer::placeView('config.menu', 'cp/:cp/shipping-methods/block', array('display' => 'nav'));
        \Pina\Composer::placeView('config.menu', 'cp/:cp/payment-methods/block', array('display' => 'nav'));
        \Pina\Composer::placeView('resource.header', 'cp/:cp/order-offer-tag-types/block', array('display' => 'nav'));
        \Pina\Composer::placeView('menu.list', 'cp/:cp/orders/block', array('display' => 'menu'));
        \Pina\Composer::placeView('dashboard.row', 'cp/:cp/offers/block', array('display' => 'dashboard'));
        \Pina\Composer::placeView('dashboard.row', 'cp/:cp/orders/block', array('display' => 'dashboard'));
        
        return [
            'collections',
            'products',
            'categories',
            'sale',
            'sale-content',
            'cp/collections',
            'cp/products',
            'cp/categories',
            'cp/offers',
            'cp/offer-products',
            'cp/offer-imports',
            'cp/offer-tag-types',
            'catalog-matrix-content',
            'cp/catalog-matrix-content',
            'cp/sale-content',
            'cp/view-price',
            
            'carts',
            'users/orders',
            'cp/orders',
            'shipping-methods',
            'cp/shipping-methods',
            'cp/coupons',
            'cp/discounts',
            'cp/payments',
            'cp/payment-methods',
            'cp/order-offer-tag-types',
            'payment-methods',
            
            'countries',
            'regions',
            'cities',
            'cp/countries',
            'cp/regions',
            'cp/cities',
        ];
    }
    
    public function cli()
    {
        return [
            'catalog',
        ];
    }
    
    public function boot()
    {
        ImportReaderRegistry::register('yml', __('YML File'), \Pina\Modules\Cart\YMLImportReader::class);
        
        Event::subscribe($this, 'catalog.import');
        Event::subscribe($this, 'catalog.build-import-preview');
        
        Event::subscribe($this, 'order.placed', 'order.notify');
        Event::subscribeSync($this, 'user.login');
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
