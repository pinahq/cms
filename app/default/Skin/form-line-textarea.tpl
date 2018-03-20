<div class="form-group {$class}">
	{if $title}<label for="{$id|default:$name}" {if !$inline}class="col-sm-2 control-label"{/if}>{$title}{*if $help} <span class="icon-info" onclick="alert('{$help}')"></span>{/if*}</label>{/if}
    {if !$inline}<div class="col-sm-10">{/if}
    <textarea id="{$id|default:$name}" name="{$name}" class="form-control {$width|default:"long-text"}{if $editor} {$editor}{/if}" {if $rows} rows="{$rows}"{/if} {if $cols} cols="{$cols}"{/if} {if $disabled}disabled{/if}>{$value}</textarea>
    {if !$inline}</div>{/if}
</div>