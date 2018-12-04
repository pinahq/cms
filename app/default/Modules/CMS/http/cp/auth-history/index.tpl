{content name='page_header'}{t}Журнал авторизаций{/t}{/content}

{content name='breadcrumb'}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/users"}">{t}Users{/t}</a></li>
    <li class="active">{t}Журнал авторизаций{/t}</li>
</ol>
{/content}

<div class="panel">
    
    
    <div class="panel-body">
        <div class="table-responsive table-catalog" id="history-login-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Дата</th>
                        <th>E-mail</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    {if $history}
                        {foreach from=$history item=item}
                            <tr>
                                <td>{$item.id}</td>
                                <td>{$item.created}</td>
                                <td>{$item.email}</td>
                                <td>{$item.action}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr class="active">
                            <td colspan="4">История не найдена</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
                
        {include file='Skin/paging.tpl' get='cp/:cp/auth-history'}
    </div>
</div>