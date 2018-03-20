

{include file="Skin/form-line-input.tpl" title="Title"|t name="title" value=$shipping_method.title}
{include file="Skin/form-line-input.tpl" title="Description"|t name="description" value=$shipping_method.description}

<div class="form-group">
    <label class="control-label col-sm-2">{t}Display{/t}</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $shipping_method.enabled eq 'Y'}checked="checked"{/if} />
            </label>
        </div>
    </div>
</div>