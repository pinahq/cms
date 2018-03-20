{module get="cp/:cp/orders/:order_id" order_id=$params.order_id title="Добавить товар в заказ" display="header"}

<div class="panel panel-default">
    <div class="panel-heading">
        <h2>Добавить товар в заказ</h2>
    </div>
    <div class="panel-body">
        <p>Добавление товара в заказ происходит через витрину. 
            Перейдите на витрину, выберите в каталоге товар, который необходимо добавить.
            Зайдите в карточку товара, выберите характеристики товара, но вместо нажатия 
            на кнопку добавления в корзину, нажмите на кнопку управления "добавить в заказ" 
            в левой части экрана рядом с кнопкой "панель управления".</p>
        
    </div>
    <div class="panel-footer">
        <a class="btn btn-default" href="{link get="/"}">На витрину</a>
    </div>
</div>

{script src="/static/default/js/pina.cookie.js"}{/script}
{script}
{literal}
    <script>
        Pina.cookie.set('cp_order_id', {/literal}{$params.order_id}{literal});
    </script>
{/literal}
{/script}