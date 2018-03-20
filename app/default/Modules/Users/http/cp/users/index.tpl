{content name="page_header"}
    {t}Users{/t}

    <a href="{link get="cp/:cp/users/create"}"
       class="btn btn-fab btn-fab-mini">
        <i class="material-icons">plus_one</i>
        <div class="ripple-container"></div>
    </a>
{/content}
{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li class="active">{t}Users{/t}</li>
</ol>
{/content}

<div class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">
                {link_context subscribed=$params.subscribed search=$params.search}
                <ul class="nav nav-pills">
                    <li {if !$params.status} class="active"{/if}>
                        <a href="{link get='cp/:cp/users'}">{t}All{/t}</a>
                    </li>
                    <li {if $params.status eq 'new'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' status=new}">{t}New{/t}</a>
                    </li>
                    <li {if $params.status eq 'active'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' status=active}">{t}Active{/t}</a>
                    </li>
                    <li {if $params.status eq 'suspended'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' status=suspended}">{t}Suspended{/t}</a>
                    </li>
                    <li {if $params.status eq 'disabled'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' status=disabled}">{t}Disabled{/t}</a>
                    </li>
                </ul>
                {/link_context}
            </div>
            <div class="col-sm-6">
                {link_context status=$params.status search=$params.search}
                <ul class="nav nav-pills">
                    <li {if !$params.subscribed} class="active"{/if}>
                        <a href="{link get='cp/:cp/users'}">{t}All{/t}</a>
                    </li>
                    <li {if $params.subscribed eq 'y'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' subscribed=y}">{t}Subscribed{/t}</a>
                    </li>
                    <li {if $params.subscribed eq 'n'} class="active"{/if}>
                        <a href="{link get='cp/:cp/users' subscribed=n}">{t}Unsubscribed{/t}</a>
                    </li>
                </ul>
                {/link_context}
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-body">
        {form action="/cp/:cp/users" method="get" name="user_search_form" class="form form-horizontal form-user pina-form"}
        <div class="col-sm-12">
            <div class="md-form input-group">
                {if $params.status}
                    <input type="hidden" name="status" value="{$params.status}" />
                {/if}
                {if $params.subscribed}
                    <input type="hidden" name="subscribed" value="{$params.subscribed}" />
                {/if}
                <input type="search" name="search" class="form-control" placeholder="{t}Search{/t}" value="{$params.search|default:''}" />
                <span class="input-group-btn">
                    <button class="btn btn btn-fab btn-fab-mini"><i class="material-icons">search</i></button>
                </span>
            </div>
        </div>
        {/form}
    </div>
</div>

<div class="row">
    {link_context status=$params.status subscribed=$params.subscribed search=$params.search}
    <div class="col-md-8">
        {include file="Skin/paging.tpl"}
    </div>
    <div class="col-md-4" style="text-align: right;">
        <div class="btn-group btn-group-sm" style="margin:20px 0;">
            <a href="{link get=$paging.resource|cat:".csv"}" class="btn btn-raised" style="float:right;"><i class="material-icons">file_download</i></a>
        </div>
    </div>
    {/link_context}
</div>

<div class="panel">
    <div class="panel-body">
        <table class="table table-hover" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        E-mail
                    </th>
                    <th>
                        ФИО
                    </th>
                    <th>
                        Телефон
                    </th>
                    <th>
                        Зарегистирован
                    </th>
                    <th>
                        Группа
                    </th>
                    <th>
                        Подписан
                    </th>
                    <th>
                        Статус
                    </th>
                </tr>
            </thead>
            <tbody>
                {if $users}
                    {foreach from=$users item=user}
                        <tr>
                            <td>{$user.id}</td>
                            <td>
                                <a href="{link get="cp/:cp/users/:id" id=$user.id}">
                                    {$user.email}
                                </a>
                            </td>
                            <td>
                                <a href="{link get="cp/:cp/users/:id" id=$user.id}">
                                    {$user.firstname} {$user.lastname}
                                </a>
                            </td>
                            <td>{$user.phone}</td>
                            <td>{$user.created|format_date}</td>
                            <td>{$user.group}</td>
                            <td>{$user.subscribed}</td>
                            <td>{$user.status}</td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="5"><center>{t}Not found{/t}</center></td>
                </tr>
            {/if}
            </tbody>
        </table>
    </div>
</div>

{link_context status=$params.status subscribed=$params.subscribed search=$params.search}
{include file="Skin/paging.tpl"}
{/link_context}

{content name=footer}
<div class="footer-links container-fluid">
    <div class="row">
        <div class="col-xs-6 bg-purple">
            <a href="{link get="cp/:cp"}">
                <span>{t}Dashboard{/t}</span>
                <span class="icon"><i class="material-icons">arrow_back</i></span>
            </a>
        </div> 
        <div class="col-xs-6 bg-deep-purple">
            <a href="{link get="cp/:cp/auth-history"}">
                <span>{t}История авторизаций{/t}</span>
                <span class="icon"><i class="material-icons">arrow_forward</i></span>
            </a>
        </div> 
    </div>
</div>
{/content}
