{view get="registration" display=block}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $("#form-registration").on("success", function () {
            document.location.reload();
        });
    </script>
{/literal}
{/script}