
<ol class="breadcrumb">
    <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
    {foreach from=$parents item=resource}
    <li><a href="/{$resource.url}">{$resource.title}</a></li>
    {/foreach}
    {if $params.title}
    <li class="active">{$params.title}</li>
    {/if}
</ol>
