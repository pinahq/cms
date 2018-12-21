{if $offers}
<div class="price">
    {foreach from=$offers item=offer name=offers}
        <div class="offer-{$offer.id}" {if $smarty.foreach.offers.index > 0}style="display: none;"{/if}>
            <span class="price-sale">{$offer.actual_price|format_price}</span>
            {if $offer.actual_price lt $offer.price}
                <del class="price-compare">{$offer.price|format_price}</del>
            {/if}
        </div>
    {/foreach}
</div>
{script}
<script>
    var Pina = Pina || {ldelim}{rdelim};
        Pina.offers = [];
        Pina.offers = Pina.offers.concat({$offers|@json_encode});
</script>
{/script}
{form action="carts" method="post" class="cart-form pina-form form-cart"}
{foreach from=$tag_groups key=tag_group item=tags}
    <div class="form-group">
        <label>{$tag_group}</label>
        <ul class="nav nav-pills">
            {foreach from=$tags item=tag}
                <li role="presentation"><a href="#" class="offer-tag" name="{$tag_group}" data-id="{$tag.id}">{$tag.tag|tag:$tag_group}</a></li>
                {/foreach}
        </ul>
    </div>
{/foreach}
<div class="offer-selector-amount">

</div>
<div class="buttons">
    <input type="submit" class="btn btn-primary btn-buy" value="В корзину" />
</div>
{/form}

{script}
{literal}
    <script>
        var Pina = Pina || {};
        Pina.offerSelector = function (opts) {
            var onlyUnique = function (value, index, self) {
                return self.indexOf(value) === index;
            }
            var inArray = function (needle, a) {
                for (var key in a) {
                    if (a[key] == needle) {
                        return true;
                    }
                }
                return false;
            }
            var getAvailableIds = function (id) {
                var offers = Pina.offers.filter(function (item) {
                    return inArray(id, item.tag_ids);
                });

                var ids = offers.reduce(function (previousValue, currentValue) {
                    return previousValue.concat(currentValue.tag_ids);
                }, []);

                return ids.filter(onlyUnique);
            }

            var getActiveIds = function () {
                var ids = [];
                $(opts.item).each(function () {
                    if (opts.isActive(this)) {
                        ids.push($(this).data('id'));
                    }
                });
                return ids;
            }

            var searchOffer = function (ids) {
                for (var key in Pina.offers) {
                    var found = true;
                    for (var i in ids) {
                        if (!inArray(ids[i], Pina.offers[key].tag_ids)) {
                            found = false;
                            break;
                        }
                    }
                    if (found) {
                        return Pina.offers[key];
                    }
                }
                return null;
            }

            var processIds = function (name, availableIds) {
                $(opts.item).each(function () {
                    if ($(this).attr('name') == name) {
                        opts.enable(this);
                        return;
                    }
                    if (inArray($(this).data('id'), availableIds)) {
                        opts.enable(this);
                    } else {
                        opts.disable(this);
                    }
                });
            }

            $(opts.item).on('click', function () {
                opts.makeActive(this);

                var id = $(this).data('id');
                var name = $(this).attr('name');
                processIds(name, getAvailableIds(id));

                var ids = getActiveIds();
                var offer = searchOffer(ids);

                opts.selectOffer(offer);
                return false;
            });

            opts.selectOffer(Pina.offers.length ? Pina.offers[0] : null);
        }

        Pina.offerSelector({
            item: '.offer-tag',
            isActive: function (e) {
                return $(e).parent().hasClass('active');
            },
            makeActive: function (e) {
                $(e).parent().siblings().removeClass('active');
                $(e).parent().addClass('active');

            },
            enable: function (e) {
                $(e).css('opacity', 1).css('pointer-events', '').css('cursor', 'pointer');
            },
            disable: function (e) {
                $(e).css('opacity', 0.5).css('pointer-events', 'none').css('cursor', 'default').parent().removeClass('active');
            },
            selectOffer: function (offer) {
                if (!offer || !offer.id) {
                    $(".btn-buy").attr('disabled', 'disabled');
                    return;
                }
                $(".btn-buy").removeAttr('disabled');
                $(".btn-buy-message").remove();
                var current = $('.offer-selector-amount input').val();
                //var html = '<input type="number" name="amount[' + offer.id + ']" max="' + offer.available_amount + '" value="' + (current < offer.available_amount ? current : offer.available_amount) + '" />';
                var html = '<input type="hidden" name="amount[' + offer.id + ']" value="1" />';
                $('.offer-selector-amount').html(html);
                $('.price > .offer-' + offer.id).siblings().hide();
                $('.price > .offer-' + offer.id).show();
            }
        });

        if ($(".cart-form .offer-tag").length) {
            $(".btn-buy").attr('disabled', 'disabled').before('<div class="btn-buy-message" style="font-size: 18px;color:#a94442;margin-bottom: 20px;">Выберите параметры</div>');
        }


    </script>
{/literal}
{/script}

{script src="/static/default/js/pina.cookie.js"}{/script}
{script}
{literal}
    <script>
        $('.cart-form').on('submit', function () {
            var guid = function () {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }
            var cartId = Pina.cookie.get('cart_id');
            if (!cartId) {
                cartId = guid();
                Pina.cookie.set('cart_id', cartId);
            }
            $(this).attr('action', '/carts/' + cartId + '/products');
            return true;
        });
    </script>
{/literal}
{/script}

{script src="/vendor/jquery.form.js"}{/script}
{script src="/static/default/js/pina.skin.js"}{/script}
{script src="/static/default/js/pina.request.js"}{/script}
{script src="/static/default/js/pina.form.js"}{/script}

{script}
{literal}
    <script>
        $(".form-cart").on("success", function (event, packet, status, xhr) {
            PinaRequest.handleRedirect(xhr);
        });
    </script>
{/literal}
{/script}

{/if}