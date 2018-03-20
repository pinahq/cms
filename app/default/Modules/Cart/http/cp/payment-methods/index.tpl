{content name="page_header"}{t}Payment methods{/t}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li>{t}Payment methods{/t}</li>
</ol>
{/content}

{if $methods}
    <div class="row">
        <div class="col-md-2">
            {module get="cp/:cp/config" namespace='payment-methods' display="sidebar"}
        </div>
        <div class="col-md-8 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Метод</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$methods item=m}
                                <tr>
                                    <td>
                                        <a href="{link get="cp/:cp/payment-methods/:id" id=$m.id}">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                    </td>
                                    <td>{$m.title}{if $m.resource} ({$m.resource}){/if}</td>
                                    <td>
                                        <div class="togglebutton">
                                            <label>
                                                <input type="checkbox" class="action-toggle" data-key="enabled"
                                                   {action_attributes put="cp/:cp/payment-methods/:id/status" id=$m.id}
                                                   {if $m.enabled eq 'Y'} checked=""{/if}>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{else}
    <p>Методов нет</p>
{/if}

{script src="/static/default/js/pina.toggle.js"}{/script}