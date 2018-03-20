<div class="form-group {$class}">
	{if $title}<label for="{$id|default:$name}" {if !$inline}class="col-sm-{$labelColumn|default:2} control-label"{/if}>{$title}{*if $help} <span class="icon-info" onclick="alert('{$help}')"></span>{/if*}</label>{/if}
    {if !$inline}<div class="col-sm-{if $labelColumn}{math equation='12-x' x=$labelColumn}{else}10{/if}">{/if}
        {if $addon_before}{$addon_before}{/if}
        <p class="form-control-static" id="{$id}">
            {$value}
        </p>
        {if $addon_after}{$addon_after}{/if}
    {if !$inline}</div>{/if}
</div>