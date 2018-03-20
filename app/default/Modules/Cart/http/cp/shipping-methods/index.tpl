
{content name="page_header"}{t}Shipping methods{/t}
<a href="{link get="cp/:cp/shipping-methods/create"}"
   class="btn btn-fab btn-fab-mini">
    <i class="material-icons">plus_one</i>
    <div class="ripple-container"></div>
</a>
{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='shipping-methods' display="sidebar"}
    </div>
    <div class="col-md-10">
        {if $shipping_methods}
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="list-group" style="padding: 2em 0 1em;" {action_attributes post="cp/:cp/shipping-methods/0/reorder"}>
                        {foreach from=$shipping_methods item=shipping_method}
                            <div class="draggable" data-id="{$shipping_method.id}">
                                <div class="list-group-item">
                                    <div class="row-action-primary">
                                        <i class="material-icons">local_shipping</i>
                                    </div>
                                    <div class="row-content">
                                        <div class="least-content">
                                            <div class="togglebutton">
                                                <label>
                                                    <input type="checkbox" class="action-toggle" data-key="enabled"
                                                           {action_attributes put="cp/:cp/shipping-methods/:id/status" id=$shipping_method.id}
                                                           {if $shipping_method.enabled eq 'Y'} checked=""{/if} />
                                                </label>
                                            </div>
                                        </div>
                                        <h4 class="list-group-item-heading">{$shipping_method.title}</h4>
                                        
                                        <p>{$shipping_method.description}</p>

                                        <p class="list-group-item-text">
                                            <a href="{link get="cp/:cp/shipping-methods/:id" id=$shipping_method.id}">{t}Edit{/t}</a>
                                            |
                                            <a href="{link get="cp/:cp/shipping-methods/:id/fee" id=$shipping_method.id}">{t}Shipping charges{/t}</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="list-group-separator"></div>
                            </div>
                        {/foreach}
                    </div>

                </div>
            </div>
        {else}
            <p>Методы доставки не настроены.</p>
        {/if}
        <a href="{link get="cp/:cp/shipping-methods/create"}" class="btn btn-primary btn-raised">Добавить</a>
    </div>
</div>

{script}
{literal}
    <script>
        $('.list-group').sortable({
            stop: function (event, ui) {
                var resource = $(this).data('resource');
                var method = $(this).data('method');
                var headers = $(this).data('csrf-token') ? {'X-CSRF-Token': $(this).data('csrf-token')} : {};
                var data = [];
                var order = 0;
                console.log(this, event, ui);
                $('.draggable', this).each(function () {
                    data.push($(this).data('id'));
                });

                $.ajax('/' + resource, {method: method, data: {id: data}, headers: headers, success: function (response) {
                    }});
            }
        });
    </script>
{/literal}
{/script}

{script src="/static/default/js/pina.toggle.js"}{/script}

