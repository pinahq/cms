<div class="form-group {$class}">
  {if $title}<label for="{$id|default:$name}" {if !$inline}class="col-sm-{$labelColumn|default:2} control-label"{/if}>{$title}</label>{/if}
  {if !$inline}<div class="col-sm-{if $labelColumn}{if $labelColumn eq 12}12{else}{math equation='12-x' x=$labelColumn}{/if}{else}10{/if}">{/if}
  <select name="{$name}"{if $id} id="{$id}"{/if} class="form-control{if $class} {$class}{/if}" {if $disabled}disabled="disabled"{/if}>
    {if $any == 'true'}
      <option {if $value == 0}selected{/if} value="0">{t}All{/t}</option>
    {/if}
    {if $placeholder}
      <option value="">{$placeholder}</option>
    {/if}
    {if $important}
      {foreach from=$important item=item key=key}
        {if $key == $value || ($key && $key == $fill.$name)}
          <option selected="selected" value="{$key}">{$item}</option>
        {else}
          <option value="{$key}">{$item}</option>
        {/if}
      {/foreach}
      <optgroup label="--------------------"></optgroup>
    {/if}
    {foreach from=$list item=item key=key}
      {if $key == $value || ($key && $key == $fill.$name)}
        <option selected="selected" value="{$key}">{$item}</option>
      {else}
        <option value="{$key}">{$item}</option>
      {/if}
    {/foreach}
  </select>
  {if !$inline}</div>{/if}
</div>