{form class="form form-subscribe form-horizontal pina-form" action="subscription" method="post" id="form-subscribe"}
<div class="newsletter-popup-content">
    <span class="dash-line-newsletter"></span>
    <p class="tagline"></p>
    <div class="input-group">
        <input class="form-control" type="email" name="email" placeholder="email" />
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><span class="fa fa-caret-right"></span></button>
        </span>
    </div> 
</div>
{/form}


{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
    $("#form-subscribe").on("success", function () {
        document.location = '/subscription/thanks';
    });
    </script>
{/literal}
{/script}