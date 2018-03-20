<?php

namespace Pina\Modules\Cart;

use Pina\Request;
use Pina\Response;
use Pina\App;
use Pina\Event;
use Pina\Modules\Users\Auth;
use Pina\Modules\Users\UserGateway;

Request::match('carts/:cart_id/orders');

$data = Request::all();

$errors = [];
if (empty($data['lastname'])) {
    $errors[] = ['Введите фамилию', 'lastname'];
}
if (empty($data['firstname'])) {
    $errors[] = ['Введите имя', 'firstname'];
}
if (empty($data['country_key'])) {
    $errors[] = ['Укажите страну', 'coountry_key'];
}
if (empty($data['region_key'])) {
    $errors[] = ['Укажите регион', 'region_key'];
}
if (empty($data['city_id']) && empty($data['city'])) {
    $errors[] = ['Укажите город', 'city'];
}
if (empty($data['street'])) {
    $errors[] = ['Введите адрес', 'street'];
}
if (empty($data['zip'])) {
    $errors[] = ['Введите почтовый код', 'zip'];
}
if (empty($data['phone'])) {
    $errors[] = ['Укажите телефон', 'phone'];
}
if (empty($data['email'])) {
    $errors[] = ['Укажите e-mail', 'email'];
}
if (empty($data['shipping_method_id']) && ShippingMethodGateway::instance()->whereBy('enabled', 'Y')->exists()) {
    $errors[] = ['Укажите метод доставки', 'shipping_method_id'];
}

$paymentMethod = [];
if (Request::input('payment_method_id')) {
    $paymentMethod = PaymentMethodGateway::instance()->enabled()->whereId(Request::input('payment_method_id'))->first();
    if (empty($paymentMethod)) {
        $errors[] = ['Выберите корректный платежный метод'];
    }
}

if (!empty($errors)) {
    return Response::badRequest()->setErrors($errors);
}

if (!filter_var(Request::input('email'), FILTER_VALIDATE_EMAIL)) {
    return Response::badRequest('Введите корректный email', 'email');
}

$cartId = Request::input('cart_id');

if (!CartOfferGateway::instance()->whereBy('cart_id', $cartId)->exists()) {
    return Response::badRequest('Корзина пуста');
}

if (!empty($data['city_id'])) {
    $data['city'] = \Pina\Modules\Regions\CityGateway::instance()->whereId($data['city_id'])->value('city');
} else {
    $data['city_id'] = 0;
}

$data['user_id'] = Auth::userId();
$data['shipping_subtotal'] = 0;

if (!empty($data['shipping_method_id'])) {
    $data['shipping_method_title'] = ShippingMethodGateway::instance()->whereId($data['shipping_method_id'])->value('title');
    $shippingFee = Shipping::fee($data['shipping_method_id'], $data['country_key'], $data['region_key'], $data['city_id']);
    $data['shipping_subtotal'] = $shippingFee ? $shippingFee : 0;
}

$subtotal = CartOfferGateway::instance()
    ->whereBy('cart_id', $cartId)
    ->calculatedSubtotalValue();

$coupon = CouponGateway::instance()->select('*')
    ->whereBy('enabled', 'Y')
    ->innerJoin(
        CartCouponGateway::instance()->on('coupon')->whereBy('cart_id', $cartId)
    )
    ->first();

$discount = 0;
if ($coupon['absolute'] > 0) {
    $discount = $coupon['absolute'];
} elseif ($coupon['percent'] > 0 && $coupon['percent'] <= 100) {
    $discount = round($subtotal * $coupon['percent'] / 100, 2);
}
$data['coupon'] = $coupon['coupon'];
$data['coupon_discount'] = $discount;

$data['utm_source'] = isset($data['utm_source']) ? substr($data['utm_source'], 0, 255) : '';
$data['utm_medium'] = isset($data['utm_medium']) ? substr($data['utm_medium'], 0, 255) : '';
$data['utm_campaign'] = isset($data['utm_campaign']) ? substr($data['utm_campaign'], 0, 255) : '';
$data['utm_term'] = isset($data['utm_term']) ? substr($data['utm_term'], 0, 255) : '';
$data['utm_content'] = isset($data['utm_content']) ? substr($data['utm_content'], 0, 255) : '';

$orderId = OrderGateway::instance()->insertGetId($data);

if (empty($orderId)) {
    return Response::internalError();
}

OrderOfferGateway::instance()->addFromCart($orderId, CartOfferGateway::instance()->whereBy('cart_id', $cartId));
OrderOfferGateway::instance()->whereBy('order_id', $orderId)->update(['amount_status' => 'processed']);
CartOfferGateway::instance()->whereBy('cart_id', $cartId)->delete();

Event::trigger("order.placed", $orderId);

if (isset($data['subscribed']) && $data['subscribed'] == 'Y') {
    UserGateway::instance()->subscribe($data);
    Event::trigger('user.subscribed', $data['email']);
}

if (!empty($paymentMethod['resource'])) {
    $order = OrderGateway::instance()
        ->whereId($orderId)
        ->where('total > payed')
        ->calculate('total - payed AS payment_total')
        ->first();
    if (empty($order)) {
        return Response::internalError(__('Не удалось создать платеж'));
    }

    $paymentId = PaymentGateway::instance()
        ->insertGetId([
        'payment_method_id' => $paymentMethod['id'],
        'order_id' => $orderId,
        'total' => $order['payment_total']
    ]);


    return Response::created(App::link(':resource/:payment_id', ['resource' => $paymentMethod['resource'], 'payment_id' => $paymentId]));
}

return Response::created(App::link('carts/:cart_id/orders/:order_id', ['cart_id' => $cartId, 'order_id' => $orderId]));
