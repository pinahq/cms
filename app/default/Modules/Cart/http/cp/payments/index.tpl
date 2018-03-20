{content name="page_header"}Платежи{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>Платежи</li>
</ol>
{/content}

{if $payments}
    {include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Платеж</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$payments item=payment}
                                <tr class="{if $payment.status eq 'payed'}success{elseif $payment.status eq 'processed'}info{elseif $payment.status eq 'canceled' or $payment.status eq 'failed'}danger{/if}">
                                    <td>
                                        №{$payment.id}
                                        <br />{$payment.created|format_date}
                                        <br />{$payment.created|format_time}
                                    </td>
                                    <td>
                                        <strong>{$payment.title}</strong>
                                        <br />Заказ <strong><a href="{link get="cp/:cp/orders/:order_id" order_id=$payment.order_id}">{$payment.order_number} (#{$payment.order_id})</a></strong>
                                    </td> 
                                    <td>
                                        <strong>{$payment.total|format_price}</strong>
                                        <br />{$payment.status}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {include file="Skin/paging.tpl" get="/cp/:cp/offers/"}
{else}
    <p>Платежей нет</p>
{/if}