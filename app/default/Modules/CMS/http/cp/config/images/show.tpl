{extends layout=block}
{capture name=src}{img media=$media return=src}{/capture}
{strip}
<div class="image thumbnail" style="background-image:url({$smarty.capture.src});">
    <input type="hidden" name="params[{$params.key}]" value="{$media.id}" />
    <a class="image-center" target="blank"><span>{$media.width}x{$media.height}</span></a>
</div>
{/strip}