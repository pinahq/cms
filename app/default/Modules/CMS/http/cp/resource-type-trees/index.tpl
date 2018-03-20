{if $types}
    
    <link rel="stylesheet" href="/vendor/jstree-bootstrap-theme/themes/proton/style.min.css">
    {script src="/vendor/jstree-bootstrap-theme/jstree.min.js"}{/script}
    
    <ul class="nav nav-tabs tabs-3 indigo" role="tablist">
        {foreach from=$types item=type}
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#panel{$type.id}" role="tab">
                <i class="fa fa-user"></i> {$type.title}
            </a>
        </li>
        {/foreach}
    </ul>
    
    <div class="panel">
        <div class="panel-body">
            <div class="tab-content">
                {foreach from=$types item=type}
                <div class="tab-pane fade in 
                    {if !$active_id && $type.type eq "categories"} active 
                    {elseif $type.id eq $active_id} active {/if}
                    " id="panel{$type.id}" role="tabpanel">
                    {module get="cp/:cp/resource-type-trees/:id" id=$type.id parent_id=$params.parent_id url=$params.url}
                </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}