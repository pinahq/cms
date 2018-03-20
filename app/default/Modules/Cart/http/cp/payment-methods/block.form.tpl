<div class="form-group">
    <label class="control-label col-sm-2">{t}Enabled{/t}</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $m.enabled eq 'Y'}checked="checked"{/if} />
            </label>
        </div>
    </div>
</div>

{include file="Skin/form-line-input.tpl" title="Title"|t name="title" value=$m.title}
{include file="Skin/form-line-static.tpl" title="Resourse"|t name="resource" value=$m.resource}