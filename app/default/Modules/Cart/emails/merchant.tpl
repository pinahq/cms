{extends layout="email"}
{content name="mail_subject"}Новый заказ{/content}
Здравствуйте!

Новый заказ #{$order.id} на сайте {$host}.

Статус: {$order.order_status_title}

ФИО: {$order.lastname} {$order.firstname} {$order.middlename}
Адрес: {$order.zip} {$order.country}, {$order.region}, {$order.city}, {$order.street}
Телефон: {$order.phone}
Email: {$order.email}

Содержимое заказа:
{foreach from=$offers item=offer}
{$offer.title}. {$offer.tags|replace:"\n":", "}
{$offer.amount} x {$offer.actual_price|format_price|strip_tags} = {$offer.amount*$offer.actual_price|format_price|strip_tags}

{/foreach}
Итого:
{if $order.coupon_discount ge 0}
Скидка{if $order.coupon} ({$order.coupon}){/if}: {$order.coupon_discount|format_price|strip_tags}
{/if}
Стоимость доставки ({$order.shipping_method_title}): {$order.shipping_subtotal|format_price|strip_tags}
К оплате: {$order.total|format_price|strip_tags}

---
{$host}