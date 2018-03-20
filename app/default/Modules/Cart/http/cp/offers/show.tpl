{content name="page_header"}Редактировать товарное предложение{/content}

<div class="panel">
    <div class="panel-body">
        {form action="cp/:cp/offers/:id" id=$offer.id method="put" class="form form-horizontal"}
        <fieldset>

            {view get="cp/:cp/offers/block" display="form"}

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <button class="btn btn-primary btn-raised">{t}Save{/t}</button>
                </div>
            </div>

        </fieldset>
        {/form}
    </div>
</div>