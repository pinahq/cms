{content name="breadcrumb"}
{module get="cp/:cp/resources/:resource_id/parents" display=breadcrumbs resource_id=$resource.id resource_type_id=$resource.resource_type_id title=$resource.title}
{/content}

{content name="page_header"}
{$resource.title}
{if $resource.resource_type_tree eq 'Y'}
    <a href="{link get="cp/:cp/resources/create" resource_type_id=$resource.resource_type_id parent_id=$resource.id}"
       class="btn btn-fab btn-fab-mini">
        <i class="material-icons">plus_one</i>
        <div class="ripple-container"></div>
    </a>
{/if}
{/content}

{content name=tabs}
<ul class="nav nav-tabs" role="tablist">
    <li><a href="{link get="cp/:cp/resources/:resource_id" resource_id=$resource.id}">{t}General{/t}</a></li>
    <li><a href="{link get="cp/:cp/resources/:resource_id/menus" resource_id=$resource.id}">Menus</a></li>
    <li><a href="{link get="cp/:cp/resources/:resource_id/tag-types" resource_id=$resource.id}">{t}Tag settings{/t}</a></li>
    {if $resource.resource_type_tree eq 'Y'}
        <li><a href="{link get="cp/:cp/resources" parent_id=$resource.id}">{t}Structure{/t} <span class="badge">{$resource.child_count|default:0}</span></a></li>
    {/if}
    <li><a href="{link get="cp/:cp/resources/:resource_id/tagged" resource_id=$resource.id}">Tagged <span class="badge">{$count|default:0}</span></a></li>
    {module get="cp/:cp/:resource_type/:resource_id/tabs" resource_type=$resource.resource_type resource_id=$resource.id fallback="cp/:cp/resources/:resourec_id/tabs"}
    {composer position="resource.header" resource=$resource}
    <li><a href="{link get=$resource.url}">{t}Go to page{/t} <span class="glyphicon glyphicon-new-window"></span></a></li>
</ul>
{/content}
