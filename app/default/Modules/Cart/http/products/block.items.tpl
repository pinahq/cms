<div class="row products">
    {foreach from=$resources item=resource name=products}
        {assign var=index value=$smarty.foreach.products.iteration-1}
        <div class="col col-xs-6 col-sm-4 col-md-3 col-lg-2 product">  
            {if $resource.actual_price lt $resource.price}
                <div class="product-label-sale">SALE</div>
            {/if}
            <a href="/{$resource.url}" class="thumbnail">
                {img media=$resource|@mine:"image" width="220" height=220 trim=4 class="not-rotation img-responsive front"}
            </a>

            <a href="/{$resource.url}">{$resource.title|tag_pattern:$type.pattern:$resource.tags}</a>

            <div class="product-price">
                {if $resource.actual_price ne ''}
                    <span class="price_sale">{$resource.actual_price|format_price}</span>
                    {if $resource.actual_price lt $resource.price}
                        <del class="price_compare"> {$resource.price|format_price}</del>
                    {/if}
                {/if}
            </div>
        </div>
    {/foreach}
</div>

{script}
{literal}
    <script>
        function justifyHeight(e, minWidth) {
            if (minWidth && $(window).outerWidth() < minWidth) {
                $(e).outerHeight("auto");
                return
            }
            var max = -1;
            $(e).each(function () {
                $(this).outerHeight("auto");
                if ($(this).outerHeight() > max) {
                    max = $(this).outerHeight()
                }
            });
            $(e).outerHeight(max);

            return max;
        }

        $(window).load(function () {
            justifyHeight("*:not(.recommendations) > .products > .product", 751);
        });
        $(window).resize(function () {
            justifyHeight("*:not(.recommendations) > .products > .product", 751);
        })
    </script>
{/literal}
{/script}