
{content name="page_header"}{t}Resource types{/t} 
<a href="{link get="cp/:cp/resource-types/create"}"
   class="btn btn-fab btn-fab-mini">
    <i class="material-icons">plus_one</i>
    <div class="ripple-container"></div>
</a>
{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Resource types{/t}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='resource-types' display="sidebar"}
    </div>
    <div class="col-md-10">
        {if $resource_types}
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{"Title"|t}</th>
                                <th>{"Type"|t}</th>
                                <th>{"Tree"|t}</th>
                                <th>{"Tag settings"|t}</th>
                                <th>{"List settings"|t}</th>
                                <th>{"Control panel list settings"|t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$resource_types item=type}
                                <tr>
                                    <td>
                                        <a href="{link get="cp/:cp/resource-types/:id" id=$type.id}">{$type.title}</a>
                                    </td>
                                    <td>
                                        {$type.type}
                                    </td>
                                    <td>
                                        {$type.tree}
                                    </td>
                                    <td>
                                        <a href="{link get="cp/:cp/resource-types/:id/tag-types" id=$type.id}">{t}Configure{/t}</a>
                                    </td>
                                    <td>
                                        <a href="{link get="cp/:cp/resource-types/:id" id=$type.id}">
                                            {if $type.pattern}
                                                {$type.pattern}
                                            {else}
                                                <i>{t}Not defined{/t}</i>
                                            {/if}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{link get="cp/:cp/resource-types/:id" id=$type.id}">
                                            {if $type.cp_pattern}
                                                {$type.cp_pattern}
                                            {else}
                                                <i>{t}Not defined{/t}</i>
                                            {/if}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {else}
            <p>Типы ресурсов не настроены.</p>
        {/if}
        <a href="{link get="cp/:cp/resource-types/create"}" class="btn btn-primary btn-raised">Добавить</a>

    </div>
</div>