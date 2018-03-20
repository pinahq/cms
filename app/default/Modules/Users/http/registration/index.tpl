{if !$registered}
    {content name="body_class"}registration{/content}

    <div class="row">
        <div class="col-sm-6">

            <h1 class="page-header">Зарегистироваться</h1>
            {view get="registration" display=block}

        </div>
    </div>

    {script src="/vendor/jquery.form.js"}{/script}
    {script src="/static/default/js/pina.skin.js"}{/script}
    {script src="/static/default/js/pina.request.js"}{/script}
    {script src="/static/default/js/pina.form.js"}{/script}


    {script}
    {literal}
        <script>
            $("#form-registration").on("success", function (event, packet, status, xhr) {
                if (!PinaRequest.handleRedirect(xhr)) {
                    document.location = '/';
                }
            });
        </script>
    {/literal}
    {/script}
{/if}