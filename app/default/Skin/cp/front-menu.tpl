{style src="/static/default/css/front-menu.css"}{/style}
{content name="bottom"}
<div class="front-menu-buttons">
    {if $params.resource_id}
        <a class="front-menu-button" href="{link get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id}">
            <i class="glyphicon glyphicon-pencil"></i>
            <span>Редактировать эту страницу</span>
        </a>
    {/if}

    {if $smarty.cookies.cp_order_id}
        <a class="front-menu-button btn-buy" style="display:none;" href="#" {action_attributes post="cp/:cp/orders/:order_id/offers" order_id=$smarty.cookies.cp_order_id}>
            <i class="glyphicon glyphicon-shopping-cart"></i>
            <span>Добавить в заказ #{$smarty.cookies.cp_order_id|string_format:"%08d"}</span>
        </a>

        {script}
        {literal}
            <script>
                if ($(".cart-form .btn-buy").length) {
                    $(".front-menu-button.btn-buy").show().on('click', function() {
                        $(".cart-form").attr("action", '/' + $(this).data("resource")).off("submit").submit();
                    });
                }
            </script>
        {/literal}
        {/script}
    {/if}

    <a class="front-menu-button" href="{link get="cp/:cp"}">
        <i class="glyphicon glyphicon-home"></i>
        <span>Панель управления</span>
    </a>
</div>
{/content}