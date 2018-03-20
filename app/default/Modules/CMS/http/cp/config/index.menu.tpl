{if $modules}
    {foreach from=$modules item=m}
        <li {if $m.namespace eq $params.namespace}class="active"{/if}>
            <a href="{link get="cp/:cp/config" namespace=$m.namespace}">
                {$m.module_title}
            </a>
        <li>
    {/foreach}
    {composer position="config.menu"}
{/if}