<div class="col-lg-2 col-md-4 col-sm-6">
    <h3>{t}Cart{/t}</h3>
    <ul>
        <li><a href="{link get="cp/:cp/orders"}">Заказы</a></li>
        <li><a href="{link get="cp/:cp/payments"}">Оплаты</a></li>
        <li><a href="{link get="cp/:cp/coupons"}">Купоны</a></li>
        <li><a href="{link get="cp/:cp/discounts"}">Скидки</a></li>
        <li><a href="{link get="cp/:cp/shipping-methods"}">Методы доставки</a></li>
        <li><a href="{link get="cp/:cp/payment-methods"}">Методы оплаты</a></li>
    </ul>
</div>
<div class="col-lg-4 col-md-4 col-sm-12">
    {module get="cp/:cp/orders" display="menu" status=placed date=today}
</div>