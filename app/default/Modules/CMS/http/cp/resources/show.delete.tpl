{view get="cp/:cp/resources/:resource_id" resource_type_id=$resource.resource_type_id display=header}

<div class="panel">
    <div class="panel-body tab-content">
        {if $resource.child_count}
            <p>Невозможно удалить страницу, так как у неё есть вложенные страницы</p>
            <a href="{link get="cp/:cp/resources/:id" id=$resource.id}" class="btn btn-primary btn-raised">{t}Back{/t}</a>
        {else}
            <p>Вы действительно хотите удалить страницу "{$resource.title}"?</p>
            <button class="btn btn-danger btn-raised pina-action action-delete"
                    {if $resource.parent_id}
                        data-redirect="{link get="cp/:cp/resources/:parent_id" parent_id=$resource.parent_id}"
                    {else}
                        data-redirect="{link get="cp/:cp/resources" resource_type_id=$resource.resource_type_id}"
                    {/if}
                    {action_attributes delete="cp/:cp/resources/:id" id=$resource.id}>{t}Yes, delete this page{/t}</button>
            <a href="{link get="cp/:cp/resources/:id" id=$resource.id}" class="btn btn-primary btn-raised">{t}No{/t}</a>
        {/if}
    </div>
</div>

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".action-delete").on("success", function (event, packet, status, xhr) {
            var redirect = $(this).data('redirect')
            document.location = redirect;
        });
    </script>
{/literal}
{/script}