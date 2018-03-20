{content name="page_header"}Заказы{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>Заказы</li>
</ol>
{/content}



<div class="row">
    <div class="col-md-8 col-lg-6">
        {include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
        {if $orders}
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Заказ</th>
                                <th>Покупатель</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$orders item=order}
                                <tr {if $order.order_status_color}style="background-color:#{$order.order_status_color}"{/if}>
                                    <td>
                                        <a href="{link get="cp/:cp/orders/:id" id=$order.id}">{$order.number} (#{$order.id})</a>
                                        <br />{$order.created|format_date}
                                        <br />{$order.created|format_time}
                                    </td>
                                    <td>
                                        <span>{$order.firstname} {$order.middlename} {$order.lastname}</span>
                                        <br />
                                        <small>
                                            {$order.city} ({$order.region})<br />
                                            email: {$order.email|default:"-"}<br />
                                            тел: {$order.phone|default:"-"}
                                        </small>
                                    </td> 
                                    <td>
                                        <strong>{$order.total|format_price}</strong>
                                        <br />{$order.order_status_title|default:$order.order_status|default:$order.order_status_group}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {else}
            <p>Заказов нет</p>
        {/if}
    </div>
    <div class="col-md-4">
        <h3>Оплаты</h3>
        <a class="btn btn-default btn-raised" href="{link get="cp/:cp/payments"}">Перейти в раздел оплат</a>
        
        <h3>Купоны</h3>
        <a class="btn btn-default btn-raised" href="{link get="cp/:cp/coupons"}">Управлять купонами</a>
        
        <h3>Методы доставки</h3>
        <a class="btn btn-default btn-raised" href="{link get="cp/:cp/coupons"}">Редактировать методы доставки</a>
    </div>
</div>




{content name=footer}
<div class="footer-links container-fluid">
    <div class="row">
        <div class="col-xs-6 bg-purple">
            <a href="{link get="cp/:cp/shipping-methods"}">
                {*<span class="state">Components</span>*}
                <span>Shipping methods</span>
                <span class="icon"><i class="material-icons">arrow_back</i></span>
            </a>
        </div> 
        <div class="col-xs-6 bg-deep-purple">
            <a href="{link get="cp/:cp/coupons"}">
                {*<span class="state">Components</span>*}
                <span>Coupons</span>
                <span class="icon"><i class="material-icons">arrow_forward</i></span>
            </a>
        </div> 
    </div>
</div>
{/content}