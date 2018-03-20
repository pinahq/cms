
{content name="page_header"}{t}Menu management{/t}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Menu management{/t}</li>
</ol>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='menus' display="sidebar"}
    </div>
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-body">
                {if $menus}
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{"Title"|t}</th>
                                <th>{"Key"|t}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$menus item=menu}
                                <tr>
                                    <td>
                                        <a href="{link get="cp/:cp/menus/:key" key=$menu.key}">{$menu.title}</a>
                                    </td>
                                    <td>
                                        <a href="{link get="cp/:cp/menus/:key" key=$menu.key}">{$menu.key}</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    <p>{t}Menus do not exist{/t}.</p>
                {/if}
            </div>
        </div>

    </div>
</div>