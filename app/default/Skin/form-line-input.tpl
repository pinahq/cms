<div class="form-group {$class}">
	{if $title}<label for="{$id|default:$name}" {if !$inline}class="col-sm-{$labelColumn|default:2} control-label"{else}class="control-label"{/if}>{$title}{*if $help} <span class="icon-info" onclick="alert('{$help}')"></span>{/if*}</label>{/if}
    {if !$inline}<div class="col-sm-{if $labelColumn}{if $labelColumn eq 12}12{else}{math equation='12-x' x=$labelColumn}{/if}{else}10{/if}">{/if}
        {if $addon_before}{$addon_before}{/if}
        <input id="{$id|default:$name}" 
               name="{$name|default:$field}" type="{$type|default:"text"}" class="form-control {$width}" 
                {if $maxlength}maxlength="{$maxlength}" {/if}value="{$value|htmlall}" 
                {if $disabled}disabled{/if} 
                {if $placeholder}placeholder="{$placeholder}"{/if}
                {if $autofocus}autofocus{/if}
        />
        {if $addon_after}{$addon_after}{/if}
    {if !$inline}</div>{/if}
</div>