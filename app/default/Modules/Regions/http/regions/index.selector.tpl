{extends layout=block}

<select name="{$params.name}"{if $params.id} id="{$params.id}"{/if} class="form-control{if $params.class} {$params.class}{/if}" {if $params.disabled}disabled="disabled"{/if}>

    {assign var=important_presented value=false}
    {foreach from=$regions item=item}
        {if $item.importance gt 0}
            {if $item.key == $params.value}
                <option selected="selected" value="{$item.key}">{$item.region}</option>
            {else}
                <option value="{$item.key}">{$item.region}</option>
            {/if}
        {/if}
        {assign var=important_presented value=true}
    {/foreach}

    {if $important_presented}
        <optgroup label="--------------------"></optgroup>
    {/if}

    {foreach from=$regions item=item}
        {if $item.importance eq 0}
            {if $item.key == $params.value}
                <option selected="selected" value="{$item.key}">{$item.region}</option>
            {else}
                <option value="{$item.key}">{$item.region}</option>
            {/if}
        {/if}
    {/foreach}
</select>