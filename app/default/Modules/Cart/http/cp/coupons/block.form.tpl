
{if $coupon.coupon}
    {include file="Skin/form-line-static.tpl" title="Coupon"|t name="coupon" value=$coupon.coupon}
{else}
    {include file="Skin/form-line-input.tpl" title="Coupon"|t name="coupon"}
{/if}
{include file="Skin/form-line-input.tpl" title="Coupon absolute"|t name="absolute" value=$coupon.absolute}
{include file="Skin/form-line-input.tpl" title="Coupon percent"|t name="percent" value=$coupon.percent}


<div class="form-group">
    <label class="control-label col-sm-2">Активен</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $coupon.enabled eq 'Y'}checked="checked"{/if} />
                <!--span style="display:inline-block;min-width: 150px;text-align: left;">Активен</span-->
            </label>
        </div>
    </div>
</div>