{foreach from=$orders item=order}
    {module get="carts/:cart_id/orders/:order_id" cart_id=$order.cart_id order_id=$order.id}
{/foreach}