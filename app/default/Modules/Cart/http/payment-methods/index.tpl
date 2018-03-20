{foreach from=$payment_methods item=payment_method name=payments}
<div class="radio">
    <label>
        <input type="radio" name="payment_method_id" value="{$payment_method.id}" {if $smarty.foreach.payments.iteration eq 1} checked="checked" {/if} />
        <span class="payment-method-header">{$payment_method.title}</span>
    </label>
</div>
{/foreach}