{content name="breadcrumb"}
<ol class="breadcrumb">
    <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
    <li class="active">{t}Cart{/t}</li>
</ol>
{/content}

<h1 class="page-header">{t}Cart{/t}</h1>

<div class="cart-content" {action_attributes get="carts/:cart_id/products" cart_id=$params.cart_id}>
    {module get="carts/:cart_id/products" cart_id=$params.cart_id}
</div>

{script src="/vendor/jquery.form.js"}{/script}

{script}
{literal}
    <script>
        $(document).ready(function () {

            $('.cart-content').on('change', ".amount", function () {
                var input = $(this);
                var minAmount = input.data('min-amount');
                var maxAmount = input.data('max-amount');
                
                var value = parseInt(input.val());
                var fold = parseInt(input.data('fold'));
                
                if (value < minAmount) {
                    value = minAmount;
                    input.val(value);
                }
                
                if (value > maxAmount) {
                    value = maxAmount;
                    input.val(value);
                }
                
                if (value % fold) {
                    value = value - value % fold;
                    input.val(value);
                }
                
                var form = $('form[name=cart-content]');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function () {
                        PinaSkin.showWaitingOverlay();
                    },
                    success: function (result, status, jqXHR) {
                        PinaSkin.hideOverlay();
                        var url = '/' + $('.cart-content').attr('data-resource');
                        $(".cart-content").load(url, function () {
                            $('.cart-content .pina-action').off('click').on('click', function () {
                                Pina.actionHandler(this);
                                return false;
                            });
                        });
                        $("li.cart").load(url + '?display=navbar');
                    },
                    error: function (jqXHR, status) {
                        PinaSkin.hideOverlay();
                    },
                });
            });
            
            $('.cart-content').on('click', '.js-minus', function() {
                var input = $(this).next();
                var minAmount = input.data('min-amount');
                var fold = parseInt(input.data('fold'));
                var value = parseInt(input.val());
                var newValue = value - fold;
                if (newValue < minAmount) {
                    return;
                }
                input.val(newValue).trigger('change');
            });
            
            $('.cart-content').on('click', '.js-plus', function() {
                var input = $(this).prev();
                var minAmount = input.data('min-amount');
                var maxAmount = input.data('max-amount');
                var fold = parseInt(input.data('fold'));
                var value = parseInt(input.val());
                var newValue = value + fold;
                if (newValue < minAmount) {
                    newValue = minAmount;
                }
                if (newValue > maxAmount) {
                    return;
                }
                input.val(newValue).trigger('change');
            });
        });
    </script>
{/literal}
{/script}

{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.action.js"}{/script}
{script}
{literal}
    <script>
        $(".cart-content").on("success", ".action-delete", function () {
            $(this).parent().parent().find(".amount").trigger("change");
        });
    </script>
{/literal}
{/script}
