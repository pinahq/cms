<div class="recommendations">
    <h3>Рекомендуем:</h3>
    <div class="row products">
        {foreach from=$resources item=resource name=products}
            {assign var=index value=$smarty.foreach.products.iteration-1}
            <div class="col-xs-6 col-sm-4 col-md-12 product"> 
                <a href="/{$resource.url}" class="thumbnail">
                    {*img image_id=$resource.image_id style="width:393px;height=480px;" class="not-rotation img-responsive front"*}
                    {img image=$resource|@mine:"image" width="220" height=220 trim=4 class="not-rotation img-responsive front"}
                </a>

                <div><strong>{$resource.tags|tag:"Бренд"}</strong></div>

                <a href="/{$resource.url}">{$resource.title}</a>

                <div class="product-price">
                    <span class="price_sale">{$resource.price|format_price}</span>
                    {if $resource.sale_price ne "0.00"}
                        <del class="price_compare"> {$resource.sale_price|format_price}</del>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
</div>