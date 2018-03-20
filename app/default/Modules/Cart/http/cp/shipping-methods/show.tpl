{content name="page_header"}{t}Shipping method{/t}{/content}

<div class="panel">
    <div class="panel-body">
        {form method="put" action="cp/:cp/shipping-methods/:id" id=$shipping_method.id}

        {view get="cp/:cp/shipping-methods/block" display="form"}

        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
            </div>
        </div>

        </fieldset>
        {/form}
    </div>
</div>

{script src="/static/default/js/pina.textarea-autosize.js"}{/script}