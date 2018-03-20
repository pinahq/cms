<div id="price_example" style="display: none;">
    {include file="Skin/form-line-static.tpl" title="Пример отображения" id="price_example_value" value="price"}
</div>

{script}
{literal}
<script>
    (function () {
        function showPriceExample() {
            var formatPrice = $("#format_price").find('option:selected').text() || '0';
            formatPrice = formatPrice.replace(/99$/, '00');
            if ($(".hide_zeros").is(":checked")) {
                formatPrice = formatPrice.replace(/.00$/, '');
            }

            var prefix = $("#prefix_currency_symbol").val() || '';
            var suffix = $("#suffix_currency_symbol").val() || '';
            $("#price_example_value").html(prefix + formatPrice + suffix);
            $("#price_example").show();
        }

        var isPriceConfig = $("#format_price").val();
        if (isPriceConfig) {
            showPriceExample();
        }

        $("#format_price").on("change", function() {
            showPriceExample();
        });

        $("#prefix_currency_symbol").on("change", function() {
            showPriceExample();
        });

        $("#suffix_currency_symbol").on("change", function() {
            showPriceExample();
        });

        $(".hide_zeros").on("change", function() {
            showPriceExample();
        });

    }());
</script>
{/literal}
{/script}