{extends layout=block}    

{if $shipping_methods}
    <div class="shipping-methods">
        {foreach from=$shipping_methods item=item name=shipping_methods}
            <div class="radio">
                <label>
                    <input type="radio" name="shipping_method_id" value="{$item.id}" 
                           {if $item.id == $params.value || (!$value && $smarty.foreach.shipping_methods.first)}checked{/if}
                    >
                    <span class="shipping-method-header">{$item.title}</span>
                    <span class="shipping-method-details">{$item.fee|format_price}</span>
                </label>
            </div>
        {/foreach}
    </div>
{else}
    <p>Нет доступных методов доставки для выбранного региона</p>
{/if}