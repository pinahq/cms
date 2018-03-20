{content name="title"}
    Личный кабинет
{/content}

<div class="row">
    <div class="col-sm-6">
        <h1 class="page-header">Личные данные</h1>
        {view get="users/:user_id/block" display=show}
        {view get="users/:user_id/block" display=logout}
    </div>
    <div class="col-sm-6">
        {module get="users/:user_id/orders"}
    </div>
</div>