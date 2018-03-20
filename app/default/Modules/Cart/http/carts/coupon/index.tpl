{if $coupon}
    <div class="form form-inline">
        <div class="form-group">
            <label for="email" class="control-label">Промокод: {$coupon}</label>
            <a href="#" class="btn btn-default pina-action action-cancel-coupon" {action_attributes delete="carts/:cart_id/coupon" cart_id=$params.cart_id}>Отменить</a>
        </div>
    </div>
            
    {script src="/vendor/jquery.form.js"}{/script}
    {script src="/static/default/js/pina.skin.js"}{/script}
    {script src="/static/default/js/pina.action.js"}{/script}
            
    {script}
    {literal}
        <script>
            $(".action-cancel-coupon").on("success", function () {
                window.location.reload();
            });
        </script>
    {/literal}
    {/script}
            
{else}
    {form action="carts/:cart_id/coupon" cart_id=$params.cart_id method="put" class="form pina-form form-coupon" name="cart-content"}
    <div class="form form-inline">
        <div class="form-group">
            <label for="email" class="control-label">Введите промокод:</label>
            <input type="text" name="coupon" class="form-control" placeholder="введите номер купона..">
            <a href="#" class="btn btn-default submit-coupon">Применить</a>
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
            $(".submit-coupon").on("click", function () {
                $(".form-coupon").submit();
                return false;
            });

            $(".form-coupon").on("success", function () {
                window.location.reload();
            });
        </script>
    {/literal}
    {/script}
{/if}