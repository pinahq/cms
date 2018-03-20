<div class="form-group">
    <label for="parent_id" class="col-sm-2 control-label">
        {t}Parent{/t}
    </label>
    <div class="col-sm-10">
        {view get="cp/:cp/resources/:id/parent-selector" id=$params.id parent_id=$params.parent_id display=select async=$params.async resources=$resources}
    </div>
</div>
