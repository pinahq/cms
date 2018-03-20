{module get="cp/:cp/resources/:resource_id" resource_id=$params.resource_id display=header}

{place name=tabs}
<div class="panel">
    <div class="tab-content panel-body">
        {form action="cp/:cp/products/:resource_id/offers" resource_id=$params.resource_id method=put class="form pina-form form-offers"}
        <table class="table table-striped table-hover ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{t}Tags{/t}</th>
                    <th>{t}Amount{/t}</th>
                    <th>{t}Min. Amount{/t}</th>
                    <th>{t}Fold{/t}</th>
                    <th>{t}Cost Price{/t}</th>
                    <th>{t}Price{/t}</th>
                    <th>{t}Sale Price{/t}</th>
                    <th>{t}Enabled{/t}</th>
                </tr>
            </thead>
            <tbody>
                
                {foreach from=$offers item=o}
                    <tr {if $o.actual_price le 0}class="danger"{elseif $o.amount eq 0}class="warning"{/if}>
                        <td><a href="{link get="cp/:cp/products/:resource_id/offers/:id" 
                                resource_id=$o.resource_id
                                id=$o.id}">
                                {$o.id} 
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </td>
                        <td>{$o.tags|replace:"\n":'<br />'}</td>
                        <td>{$o.amount}</td>
                        <td>{$o.min_amount}</td>
                        <td>{$o.fold}</td>
                        <td>{$o.cost_price}</td>
                        <td>{$o.price}</td>
                        <td>{$o.sale_price}</td>
                        <td>
                            <div class="togglebutton">
                                <label>
                                    <input type="checkbox" class="action-toggle" data-key="enabled"
                                           {action_attributes put="cp/:cp/offers/:id/status" id=$o.id}
                                           {if $o.enabled eq 'Y'} checked=""{/if}>
                                </label>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                <tr>
                    <td colspan="3" style="text-align: right;font-weight:bold;vertical-align: middle;">{t}Change all:{/t}</td>
                    <td><input type="text" name="min_amount" value="{$o.min_amount}" class="form-control" /></td>
                    <td><input type="text" name="fold" value="{$o.fold}" class="form-control" /></td>
                    <td><input type="text" name="cost_price" value="{$o.cost_price}" class="form-control" /></td>
                    <td><input type="text" name="price" value="{$o.price}"  class="form-control" /></td>
                    <td><input type="text" name="sale_price" value="{$o.sale_price}" class="form-control" /></td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-raised">{t}Change{/t}</button>
                    </td>
                </tr>
            </tbody>
        </table>
        {/form}

        {include file="Skin/paging.tpl" get="/cp/:cp/products/"|cat:$params.resource_id|cat:"/offers/"}

        <div class="row">
            <div class="col-sm-6">
                <a href="{link get="cp/:cp/products/:resource_id/offers/create" resource_id=$params.resource_id}" class="btn btn-primary btn-raised">
                    {t}Create offer{/t}
                </a>
            </div>
        </div>

        {script src="/static/default/js/pina.toggle.js"}{/script}
        
        {script src="/vendor/jquery.form.js"}{/script}
        {script src="/static/default/js/pina.skin.js"}{/script}
        {script src="/static/default/js/pina.request.js"}{/script}
        {script src="/static/default/js/pina.form.js"}{/script}

        {script}
        {literal}
            <script>
                $(".form-offers").on("success", function () {
                    document.location = document.location.origin + document.location.pathname + '?changed=' + Math.random();
                });
            </script>
        {/literal}
        {/script}
    </div>
</div>