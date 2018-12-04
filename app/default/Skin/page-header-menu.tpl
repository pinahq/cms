<ul class="nav navbar-nav">
    {module get="categories" display="main-menu" parent_id=0 length=2}
    {config key="company_phone" namespace="Pina\Modules\CMS" assign=phone}
    {if $phone}
        <li><a href="tel:{$phone}">{$phone}</a></li>
        {/if}
        {view get="submissions/block" display="callback"}
</ul>
<ul class="nav navbar-nav navbar-right">
    <li class="search">
        <a href="{link get="search"}">Поиск</a>
    </li>
    {module get="auth" display=navbar}
    {module get="carts" display="navbar" wrapper="li class=cart"}
</ul>