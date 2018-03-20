{extends layout=block}

{capture name=src}{img image=$image return=src}{/capture}
{strip}
<div class="image thumbnail" style="background-image:url({$smarty.capture.src});">
    <a class="remove action-remove" href="#"><i class="glyphicon glyphicon-remove"></i></a>
    
    <input type="hidden" name="image_ids[]" value="{$image.id}" />
    <a class="image-center" href="{$smarty.capture.src}" target="blank"><span>{$image.width}x{$image.height}</span></a>
</div>
{/strip}