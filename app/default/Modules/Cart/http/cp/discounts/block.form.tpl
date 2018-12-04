
{module get="cp/:cp/resources/0/parent-selector" display="selector" parent_id=$discount.parent_id|default:$params.parent_id}

{module get="cp/:cp/discounts/:id/tags" display="selector" id=$discount.id name="user_tag_id" value=$discount.user_tag_id title="User tag"}
{module get="cp/:cp/discounts/:id/tags" display="selector" id=$discount.id name="resource_tag_id" value=$discount.resource_tag_id title="Resource tag"}

{include file="Skin/form-line-input.tpl" title="Discount"|t name="percent" value=$discount.percent}


<div class="form-group">
    <label class="control-label col-sm-2">Активен</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $discount.enabled eq 'Y'}checked="checked"{/if} />
                <!--span style="display:inline-block;min-width: 150px;text-align: left;">Активен</span-->
            </label>
        </div>
    </div>
</div>