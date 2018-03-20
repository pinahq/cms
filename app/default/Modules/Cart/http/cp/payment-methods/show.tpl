{content name="page_header"}{$m.title}{/content}

{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="{link get="cp/:cp"}"><i class="material-icons">home</i></a></li>
    <li><a href="{link get="cp/:cp/payment-methods"}">Методы платежа</a></li>
    <li>{$m.title}</li>
</ol>
{/content}

{content name=sidebar}
    {module get="cp/:cp/config" display="sidebar"}
{/content}

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="panel">
            <div class="tab-content panel-body">
                {form action="cp/:cp/payment-methods/:id" 
                    id=$m.id 
                    method="put" 
                    class="form form-horizontal pina-form form-payment-method"}
                    {view get="cp/:cp/payment-methods/block" display="form"}

                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                        </div>
                    </div>
                {/form}
            </div>
        </div>
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
<script>
    $(".form-payment-method").on("success", function () {
        var parts = document.location.pathname.split('/');
        var path = parts.slice(0, parts.length - 1).join('/');
        document.location = document.location.origin + path + '?changed=' + Math.random();
    });
</script>
{/literal}
{/script}