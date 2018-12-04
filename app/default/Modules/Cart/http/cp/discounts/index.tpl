{content name="page_header"}Скидки{/content}

<div class="panel panel-default">
    <div class="panel-body">
        {if $discounts}            
            <table class="table table-striped table-hover ">
                <thead>
                    <tr>
                        <th>Parent</th>
                        <th>User Tag</th>
                        <th>Resource Tag</th>
                        <th>Percent</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$discounts item=discount}
                        <tr>
                            <td><a href="{link get="cp/:cp/discounts/:id" id=$discount.id}">{$discount.resource_title}</a></td>
                            <td><a href="{link get="cp/:cp/discounts/:id" id=$discount.id}">{$discount.user_tag}</a></td>
                            <td><a href="{link get="cp/:cp/discounts/:id" id=$discount.id}">{$discount.resource_tag}</a></td>
                            <td><a href="{link get="cp/:cp/discounts/:id" id=$discount.id}">{$discount.percent}</a></td>
                            <td>
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle" data-key="enabled"
                                               {action_attributes put="cp/:cp/discounts/:id/status" id=$discount.id}
                                               {if $discount.enabled eq 'Y'} checked=""{/if}>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <p>Не найдено.</p>
        {/if}
    </div>
</div>
<a href="{link get="cp/:cp/discounts/create"}" class="btn btn-primary btn-raised">Добавить</a>

{script src="/static/default/js/pina.toggle.js"}{/script}