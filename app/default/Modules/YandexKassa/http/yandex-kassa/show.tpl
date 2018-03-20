{extends layout=load}

<div class="row">
    <div class="col-xs-12">
        <p class="text-center">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </p>
    </div>
</div>

<form class="form form-horizontal yandex-form" action="https://money.yandex.ru/eshop.xml" method="post">
    <input type="hidden" name="shopId" value="{$shopId}" />
    <input type="hidden" name="scid" value="{$scid}" />
    
    <input type="hidden" name="sum" value="{$payment.total}" />
    <input type="hidden" name="customerNumber" value="{$payment.email}" />
    <input type="hidden" name="orderNumber" value="{$payment.id}" />

    <input type="hidden" name="paymentType" value="{$paymentType}" />
    <input type="hidden" name="shopSuccessUrl" value="{$shop_success_url}" />
</form>

{script}
{literal}
<script>
    $(".yandex-form").submit();
</script>
{/literal}
{/script}