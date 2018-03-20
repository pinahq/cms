{content name="page_header"}Добавить метод доставки{/content}

<div class="row">
    <div class="col-md-2">
        {module get="cp/:cp/config" namespace='shipping-methods' display="sidebar"}
    </div>
    <div class="col-md-8 col-lg-6">
        {form method="post" action="cp/:cp/shipping-methods"}

        <fieldset class="well">

            {view get="cp/:cp/shipping-methods/block" display="form"}

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                </div>
            </div>

        </fieldset>
        {/form}

        {script src="/static/default/js/pina.textarea-autosize.js"}{/script}

    </div>
</div>