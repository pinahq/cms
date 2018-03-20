<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Вам перезвонить?</a>
    <div class="dropdown-menu">
        <div class="recall-wrapper">
            <div id="recall-1-step">
                <div class="form-group row">
                    <label class="col-sm-offset-1 col-sm-3 form-control-label" for="recall-name">Ваше имя:</label>
                    <div class="col-sm-7 recall-input">
                        <input type="text" class="form-control" id="recall-name">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-offset-1 col-sm-3 form-control-label" for="recall-phone">Телефон:</label>
                    <div class="col-sm-7 recall-input">
                        <input type="text" class="form-control" id="recall-phone">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-offset-4 col-sm-7">
                        <button id="recall-send" type="button" class="btn btn-default pull-right">Заказать</button>
                    </div>
                </div>
            </div>
            <div id="recall-2-step" style="display: none">
                <div class="form-group row">
                    <div class="col-sm-offset-1 col-sm-10 text-center">
                        <p>Спасибо!<br>Мы перезвоним вам в ближайшее время.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>


{script}
{literal}
    <script type="text/javascript">
        $(document).ready(function() {
            if (getCookie('call_me')) {
                $('#recall-1-step').hide();
                $('#recall-2-step').show();
            }

            $('#recall-open').on('click', function(e) {
               $(this).parent().addClass('open');
            });

            $(document).mouseup(function (e) {
                var container = $(".dropdown-recall");

                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.parent().removeClass('open');
                }

                $("#recall-name").parent().removeClass('has-error');
                $("#recall-phone").parent().removeClass('has-error');
                $(".pina-error").remove();
            });

            $('#recall-send').on('click', function(e) {
                var name = $("#recall-name").val();
                var phone = $("#recall-phone").val();

                var error = false;
                if (!name || name.length < 3) {
                    $("#recall-name").parent().addClass('has-error');
                    $('<span class="help-block pina-error">Введите корректное имя</span>').insertAfter($("#recall-name"));
                    error = true;
                }

                if (!phone || phone.length < 10) {
                    $("#recall-phone").parent().addClass('has-error');
                    $('<span class="help-block pina-error">Введите корректный телефон</span>').insertAfter($("#recall-phone"));
                    error = true;
                }

                if (error) {
                    return false;
                }

                $.ajax({
                    async: false,
                    type: 'post',
                    url: '/submissions',
                    headers: {'X-CSRF-Token': {/literal}'{csrf_token method="post"}'{literal}},
                    data: {
                        type:'recall',
                        firstname: name,
                        phone: phone,
                        subject: 'Перезвонить'
                    },
                    success: function(html){
                        $('#recall-1-step').hide();
                        $('#recall-2-step').show();
                        setCookie('call_me', (new Date).toUTCString(), {expires: 3600*24})
                    }
                });
            });

        });


        function setCookie(name, value, options) {
            options = options || {};

            var expires = options.expires;

            if (typeof expires == "number" && expires) {
                var d = new Date();
                d.setTime(d.getTime() + expires * 1000);
                expires = options.expires = d;
            }
            if (expires && expires.toUTCString) {
                options.expires = expires.toUTCString();
            }

            value = encodeURIComponent(value);

            var updatedCookie = name + "=" + value;

            for (var propName in options) {
                updatedCookie += "; " + propName;
                var propValue = options[propName];
                if (propValue !== true) {
                    updatedCookie += "=" + propValue;
                }
            }

            document.cookie = updatedCookie;
        }

        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }
    </script>

{/literal}
{/script}