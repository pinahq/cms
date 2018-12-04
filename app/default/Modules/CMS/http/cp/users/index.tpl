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

<div class="row">
    <div class="col-md-6 col-md-push-6">

        <div class="hidden-xs row" style="visibility: hidden;">
            {link_context enabled=$params.enabled subscribed=$params.subscribed search=$params.search}
            <div class="col-xs-8">
                {include file="Skin/paging.tpl"}
            </div>
            <div class="col-xs-4" style="text-align: right;">
                <div class="btn-group btn-group-sm" style="margin:20px 0;">
                    <a href="{link get=$paging.resource|cat:".csv"}" class="btn btn-raised"><i class="material-icons">file_download</i></a>
                </div>
            </div>
            {/link_context}
        </div>


        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        {link_context subscribed=$params.subscribed search=$params.search}
                        <ul class="nav nav-pills">
                            <li {if !$params.enabled} class="active"{/if}>
                                <a href="{link get='cp/:cp/users'}">{t}All{/t}</a>
                            </li>
                            <li {if $params.enabled eq 'Y'} class="active"{/if}>
                                <a href="{link get='cp/:cp/users' enabled=Y}">{t}Active{/t}</a>
                            </li>
                            <li {if $params.enabled eq 'N'} class="active"{/if}>
                                <a href="{link get='cp/:cp/users' enabled=N}">{t}Disabled{/t}</a>
                            </li>
                        </ul>
                        {/link_context}
                    </div>
                    <div class="col-sm-6">
                        {link_context enabled=$params.enabled search=$params.search}
                        <ul class="nav nav-pills">
                            <li {if !$params.subscribed} class="active"{/if}>
                                <a href="{link get='cp/:cp/users'}">{t}All{/t}</a>
                            </li>
                            <li {if $params.subscribed eq 'Y'} class="active"{/if}>
                                <a href="{link get='cp/:cp/users' subscribed=Y}">{t}Subscribed{/t}</a>
                            </li>
                            <li {if $params.subscribed eq 'N'} class="active"{/if}>
                                <a href="{link get='cp/:cp/users' subscribed=N}">{t}Unsubscribed{/t}</a>
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
                        {if $params.enabled}
                            <input type="hidden" name="enabled" value="{$params.enabled}" />
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



    </div>
    <div class="col-md-6 col-md-pull-6">

        <div class="row">
            {link_context enabled=$params.enabled subscribed=$params.subscribed search=$params.search}
            <div class="col-xs-8">
                {include file="Skin/paging.tpl"}
            </div>
            <div class="col-xs-4" style="text-align: right;">
                <div class="btn-group btn-group-sm" style="margin:20px 0;">
                    <a href="{link get=$paging.resource|cat:".csv"}" class="btn btn-raised"><i class="material-icons">file_download</i></a>
                </div>
            </div>
            {/link_context}
        </div>

        <div class="panel panel-default">
            <div class="panel-body">
                {if $users}
                    <div class="list-group" style="padding: 2em 0 1em;">
                        {foreach from=$users item=user}
                            <div class="list-group-item">
                                <div class="row-action-primary">
                                    {if $user.subscribed eq 'Y'}
                                        <i class="material-icons" style="background-color: #366e6f;">mail</i>
                                    {else}
                                        <i class="material-icons">unsubscribe</i>
                                    {/if}
                                </div>
                                <div class="row-content">
                                    <div class="least-content">
                                        <div class="togglebutton">
                                            <label>
                                                <input type="checkbox" class="action-toggle" data-key="enabled"
                                                       {action_attributes put="cp/:cp/users/:id/status" id=$user.id}
                                                       {if $user.enabled eq 'Y'} checked="checked"{/if} />
                                            </label>
                                        </div>
                                    </div>
                                    <h4 class="list-group-item-heading">{$user.firstname} {$user.middlename} {$user.lastname} &lt;<a href="mailto:{$user.email}">{$user.email}</a>&gt;</h4>

                                    <p>
                                        {t}Created{/t}: {$user.created|format_date}
                                        {if $user.phone}<br />{t}Phone{/t}: <a href="tel:{$user.phone}"><strong>{$user.phone}</strong></a>{/if}
                                    </p>

                                    <p class="list-group-item-text">
                                        <a href="{link get="cp/:cp/users/:id" id=$user.id}">{t}Edit{/t}</a>
                                    </p>

                                </div>
                            </div>
                            <div class="list-group-separator"></div>
                        {/foreach}
                    </div>
                {else}
                    <p>{t}Not found{/t}</p>
                {/if}
            </div>
        </div>

        {link_context enabled=$params.enabled subscribed=$params.subscribed search=$params.search}
        {include file="Skin/paging.tpl"}
        {/link_context}
    </div>
</div>

{script src="/static/default/js/pina.toggle.js"}{/script}

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
