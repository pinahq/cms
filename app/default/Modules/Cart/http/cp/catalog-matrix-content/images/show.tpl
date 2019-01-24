{extends layout=block}

{capture name=src}{img media=$image return=src}{/capture}
{strip}
<div class="image thumbnail" style="background-image:url({$smarty.capture.src});">
    <input type="hidden" name="media_id[]" value="{$image.id}" />
    <a class="image-center" target="blank"><span>{$image.width}x{$image.height}</span></a>
</div>
{/strip}
