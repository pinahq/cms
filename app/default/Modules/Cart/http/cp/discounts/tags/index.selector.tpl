<div class="form-group">
    <label for="{$params.name}" class="col-sm-2 control-label">
        {$params.title}
    </label>
    <div class="col-sm-10">
        {view get="cp/:cp/discounts/:id/tags" id=$params.id  name=$params.name value=$params.value display=select tag=$tag}
    </div>
</div>