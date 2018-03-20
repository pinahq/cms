<div class="content-form form form-horizontal" {action_attributes put="cp/:cp/resource-list-content/:content_id" content_id=$content.id}>
    <div class="slot-content-container">
        {view get="resource-list-content/:content_id" content_id=$content.id content=$content}
    </div>

    <div class="content-params">
        <h2 class="params-header">{t}Options{/t}</h2>

        <div class="form-group ">
            <label class="col-sm-12 control-label">{t}Resource type{/t}</label>  
            <div class="col-sm-12">
                {module get="cp/:cp/resource-types" display=select id=$content.params.type_id}
            </div>
        </div>

        <div class="form-group ">
            <label class="col-sm-12 control-label">{t}Parent{/t}</label>  
            <div class="col-sm-12">
                {module get="cp/:cp/resources/:id/parent-selector" display="select" id=0 parent_id=$content.params.parent_id}
            </div>
        </div>

        <div class="form-group ">
            <label class="col-sm-12 control-label">{t}Depth{/t}</label>
            <div class="col-sm-12">
                <select name="length" class="form-control">
                    <option value="0">{t}All{/t}</option>
                    {section name=length start=1 loop=10}
                        <option value="{$smarty.section.length.index}" {if $smarty.section.length.index eq $content.params.length}selected="selected"{/if}>{$smarty.section.length.index}</option>
                    {/section}
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</div>
{script}
{literal}
    <script>
        Pina.slotEditor.on('resource-list-content', 'edit', function (currentBlock) {
            Pina.slotEditor.showCurrentContentParams();
        });

    </script>
{/literal}
{/script}