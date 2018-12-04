{extends layout=block}

{capture name=src}{img image=$image return=src}{/capture}
{strip}
<div class="image thumbnail" style="background-image:url({$smarty.capture.src});">
    <a href="{$smarty.capture.src}" target="blank"><span>{$image.width}x{$image.height}</span></a>
</div>
{/strip}