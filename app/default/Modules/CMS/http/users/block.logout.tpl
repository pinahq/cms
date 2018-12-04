<center><a href="#" class="btn btn-danger pina-action action-logout" {action_attributes delete="auth"} >Выйти</a></center>

{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}

{script}
{literal}
    <script>
        $(".action-logout").on("success", function () {
            document.location = '/';
        });
    </script>
{/literal}
{/script}
