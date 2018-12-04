{extends layout=block}

{if $cities}
    <select name="{$params.name|default:"city_id"}"{if $params.id} id="{$params.id}"{/if} class="form-control form-control-city{if $params.class} {$params.class}{/if}" {if $params.disabled}disabled="disabled"{/if}>
        {foreach from=$cities item=item}
            {if $item.id == $params.value}
                <option selected="selected" value="{$item.id}">{$item.city}</option>
            {else}
                <option value="{$item.id}">{$item.city}</option>
            {/if}
        {/foreach}
        <option value="" {if !$params.value}selected="selected"{/if}>Ввести другой</option>
    </select>
    <input type="text" name="{$params.name_other|default:"city"}" value="{$params.value_other}" class="form-control" style="display: none;" />
{else}
    <input type="text" name="{$params.name_other|default:"city"}" value="{$params.value_other}" class="form-control"{if $params.disabled}disabled="disabled"{/if} />
{/if}

{script}
{literal}
    <script>
        $(".form-control-city").on("change", function () {
            if ($(this).val() == '') {
                $(this).next('input').show();
            } else {
                $(this).next('input').hide().val('');
            }
        }).trigger('change');
    </script>
{/literal}
{/script}