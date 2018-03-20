<div class="form-group">
    <label for="resource_type_id" class="col-sm-2 control-label">{t}Type{/t}</label>
    <div class="col-sm-10">
        <select name="resource_type_id" class="form-control">
            {foreach from=$resource_types item=type}
                <option value="{$type.id}" {if $type.id eq $params.id}selected="seleted"{/if}>{$type.title} ({$type.type})</option>
            {/foreach}
        </select>
    </div>
</div>