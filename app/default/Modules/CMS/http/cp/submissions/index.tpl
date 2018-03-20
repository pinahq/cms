{content name="page_header"}
Обращения пользователей
{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>Обращения пользователей</li>
</ol>
{/content}

<div class="panel">
    <div class="panel-body">

        <table class="table table-striped table-hover ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Extra data</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$submissions item=s}
                    <tr>
                        <td>
                            {$s.id}
                            <a href="{link get="cp/:cp/submissions/:id" id=$s.id}">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </td>
                        <td>{$s.created|format_datetime}</td>
                        <td>{$s.type}</td>
                        <td>{$s.subject|default:"-"}</td>
                        <td>
                            {if $s.firstname || $s.middlename || $s.lastname}
                                {$s.firstname} 
                                {$s.middlename}
                                {$s.lastname}
                            {else}
                                -
                            {/if}
                            {if $s.user_id}
                                <a href="{link get='cp/:cp/users/:user_id' user_id=$s.user_id}">
                                    <span class="glyphicon glyphicon-link"></span>
                                </a>
                            {/if}
                        </td>
                        <td>{$s.email|default:"-"}</td>
                        <td>{$s.phone|default:"-"}</td>
                        <td>
                            <dl>
                                {capture name=truncate_string}... <a href="{link get="cp/:cp/submissions/:id" id=$s.id}"><strong>Read more</strong></a>{/capture}
                            {foreach from=$s.data key=field item=value}
                                <dt>{$field}</dt><dd>{$value|truncate:160:$smarty.capture.truncate_string|nl2br}<dd>
                            {/foreach}
                            </dl>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {include file="Skin/paging.tpl"}

    </div>
</div>