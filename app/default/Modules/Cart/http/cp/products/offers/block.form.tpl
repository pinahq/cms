<div class="form-group">
    <label class="control-label col-sm-2">{t}Enabled{/t}</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $offer.enabled eq 'Y'}checked="checked"{/if} />
            </label>
        </div>
    </div>
</div>

{include file="Skin/form-line-input.tpl" title="Amount"|t name="amount" value=$offer.amount}
{include file="Skin/form-line-input.tpl" title="Min. amount"|t name="min_amount" value=$offer.min_amount}
{include file="Skin/form-line-input.tpl" title="Fold"|t name="fold" value=$offer.fold}
{include file="Skin/form-line-input.tpl" title="Cost"|t name="cost_price" value=$offer.cost_price}
{include file="Skin/form-line-input.tpl" title="Price"|t name="price" value=$offer.price}
{include file="Skin/form-line-input.tpl" title="Sale price"|t name="sale_price" value=$offer.sale_price}

{module get="cp/:cp/offers/:id/tags" 
    display="selector" 
    id=$offer.id}