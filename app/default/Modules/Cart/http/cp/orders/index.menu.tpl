<h3>Новые заказы сегодня <small>({$orders|@count})</small></h3>
{if $orders}
    <ul>
        {foreach from=$orders item=order name=orders}
            {if $smarty.foreach.orders.index le 2}
                <li>
                    <a href="{link get="cp/:cp/orders/:id" id=$order.id}">{$order.number} (#{$order.id})
                        {if $params.date eq 'today'}
                            {$order.created|format_time}
                        {else}
                            {$order.created|format_datetime}
                        {/if}

                        <br />{$order.firstname} {$order.middlename} {$order.lastname} ({$order.city})<br />
                        <strong>{$order.total|format_price}</strong>
                        <br />{$order.order_status_title|default:$order.order_status|default:$order.order_status_group}
                    </a>
                </li>
            {/if}
        {/foreach}
        <li><a href="{link get="cp/:cp/orders"}"><strong>{t}All orders{/t}</strong></a></li>
    </ul>
{else}
    <p>{t}Not Found{/t}</p>
{/if}