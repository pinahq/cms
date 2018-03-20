<?php

namespace Pina\Modules\Cart;

use Pina\ModuleInterface;
use Pina\Event;

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
        \Pina\Composer::placeView('cms.menu', 'cp/:cp/offers/block', ['display' => 'nav']);
        \Pina\Composer::placeView('config.menu', 'cp/:cp/shipping-methods/block', array('display' => 'nav'));
        \Pina\Composer::placeView('config.menu', 'cp/:cp/payment-methods/block', array('display' => 'nav'));
        \Pina\Composer::placeView('resource.header', 'cp/:cp/order-offer-tag-types/block', array('display' => 'nav'));
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
            'cp/payments',
            'cp/payment-methods',
            'cp/order-offer-tag-types',
            'payment-methods',
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
        Event::subscribe($this, 'catalog.import');
        Event::subscribe($this, 'catalog.build-import-preview');
        
        Event::subscribe($this, 'order.placed', 'order.notify');
    }

}

function __($string)
{
    return \Pina\Language::translate($string, __NAMESPACE__);
}
