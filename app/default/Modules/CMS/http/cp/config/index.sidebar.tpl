{if $modules}
    <div class="panel">
        <div class="panel-body">
            <ul class="nav nav-pills nav-stacked">
                {foreach from=$modules item=m}
                    <li {if $m.namespace eq $params.namespace}class="active"{/if}>
                        <a href="{link get="cp/:cp/config" namespace=$m.namespace}">
                            {$m.module_title}
                        </a>
                    </li>
                {/foreach}
                {composer position="config.menu" namespace=$params.namespace}
            </ul>
        </div>
    </div>
{/if}