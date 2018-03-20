<div id="container{$params.id}"></div>

{script}
{literal}
    <script>
        (function () {
            var data = {/literal}{$data|default:''}{literal};
            var resourceTypeId = '{/literal}{$params.id}{literal}';
            var container = '#container' + resourceTypeId;

            $(container).jstree({
                'core': {
                    'data': data,
                    'themes': {
                        'name': 'proton',
                        'responsive': true
                    }
                }
            });


            $(container).on('ready.jstree', function () {
                $(container).on('changed.jstree', function (e, data) {
                    var id = data.selected.shift();

                    var instance = $(container).jstree(true);
                    var node = instance.get_node(id);

                    if ('original' in node && 'url' in node.original && /http/.test(node.original.url)) {
                        window.location.href = node.original.url;
                    }
                });
            });

        }());
    </script>
{/literal}
{/script}