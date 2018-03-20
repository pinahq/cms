{form action="cp/:cp/offer-products" method="post"}
{foreach from=$types item=t}
    <div class="togglebutton">
        <label>
            <input type="checkbox" name="tag_types[]" value="{$t.tag_type_id}" {if $t.tag_type_enabled eq 'Y'} checked=""{/if}>
            <span style="display:inline-block;min-width: 150px;text-align: left;">{$t.tag_type}</span>
        </label>
    </div>
{/foreach}
<button class="btn btn-primary btn-raised">Привязать к товарам</button>
{/form}