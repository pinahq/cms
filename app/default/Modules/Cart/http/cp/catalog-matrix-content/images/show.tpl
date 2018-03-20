{extends layout=block}

{capture name=src}{img image=$image|@mine:"image" return=src}{/capture}
{strip}
<div class="image thumbnail" style="background-image:url({$smarty.capture.src});">
    <input type="hidden" name="image_id[]" value="{$image.image_id}" />
    <a class="image-center" target="blank"><span>{$image.image_width}x{$image.image_height}</span></a>
</div>
{/strip}
