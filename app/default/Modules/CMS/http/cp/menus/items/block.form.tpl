<div class="form-group">
    <label for="title" class="control-label col-sm-2">{t}Title{/t}</label>
    <div class="col-sm-10">
        <input type="text" name="title" value="{$menu_item.title}" class="form-control" />
    </div>
</div>
{if $menu_item.resource_id}
    <div class="form-group">
        <label for="link" class="control-label col-sm-2">{t}Resource{/t}</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <a href="{link get="cp/:cp/resources/:id" id=$menu_item.resource_id}" target="_blank">{link get="cp/:cp/resources/:id" id=$menu_item.resource_id}</a>
            </p>
        </div>
    </div>
{else}
    <div class="form-group">
        <label for="link" class="control-label col-sm-2">{t}Link{/t}</label>
        <div class="col-sm-10">
            <input type="text" name="link" value="{$menu_item.link}" class="form-control" />
        </div>
    </div>
{/if}
<div class="form-group">
    <label class="control-label col-sm-2">{t}Activity{/t}</label>
    <div class="col-sm-10">
        <div class="togglebutton" style="margin: 1rem 0;">
            <label>
                <input type="checkbox" name="enabled" value="Y" {if $menu_item.enabled eq 'Y'}checked="checked"{/if} />
            </label>
        </div>
    </div>
</div>