{extends layout=block}

{strip}
<div class="image thumbnail" style="background-image:url('{$url}');">
    <a class="remove action-remove" href="#"><i class="glyphicon glyphicon-remove"></i></a>
    
    <input type="hidden" name="media_ids[]" value="{$id}" />
    <a class="image-center" href="{$url}" target="blank"><span>{$width}x{$height}</span></a>
</div>
{/strip}