{content name="page_header"}Купоны{/content}

<div class="panel panel-default">
    <div class="panel-body">
        {if $coupons}            
            <table class="table table-striped table-hover ">
                <thead>
                    <tr>
                        <th>Coupon</th>
                        <th>Absloute</th>
                        <th>Percent</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$coupons item=coupon}
                        <tr>
                            <td>{$coupon.coupon_id} <a href="{link get="cp/:cp/coupons/:coupon" coupon=$coupon.coupon}">
                                    {$coupon.coupon|default:'<i>no name</i>'}
                                </a></td>
                            <td>{$coupon.absolute}</td>
                            <td>{$coupon.percent}</td>
                            <td>
                                <div class="togglebutton">
                                    <label>
                                        <input type="checkbox" class="action-toggle" data-key="enabled"
                                               {action_attributes put="cp/:cp/coupons/:coupon/status" coupon=$coupon.coupon}
                                               {if $coupon.enabled eq 'Y'} checked=""{/if}>
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
        <a href="{link get="cp/:cp/coupons/create"}" class="btn btn-primary btn-raised">Добавить</a>
    </div>
</div>

{script src="/static/default/js/pina.toggle.js"}{/script}