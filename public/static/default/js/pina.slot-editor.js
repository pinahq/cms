var Pina = Pina || {};
Pina.slotEditor = {

    handlers: {},

    blockMenuEl: '#block-menu',

    currentBlock: null,
    currentBlockId: 0,

    on: function (type, event, callback) {
        if (this.handlers[type] && this.handlers[type][event]) {
            return;
        }

        if (!this.handlers[type]) {
            this.handlers[type] = {};
        }
        this.handlers[type][event] = callback;
    },

    trigger: function (type, event, params) {
        if (!this.handlers[type] || !this.handlers[type][event]) {
            return;
        }

        var callback = this.handlers[type][event];
        callback(params);
    },

    init: function ()
    {
        this.initEvents();
        this.enableBlockSorting();
    },

    initEvents: function () {

        var _this = this;

        var container = $('body');

        container.on('click', '.content-editor_add', function (e) {
            e.preventDefault();

            _this.setCurrentBlock($(this).parents('.slot-content-editor'));
            _this.showNewContentMenu();
        });

        container.on('click', '.content-editor_edit,.content-cover', function (e) {
            e.preventDefault();

            _this.setCurrentBlock($(this).parents('.slot-content-editor'));

            _this.runCurrentContentEditor();

        });
        
        container.on('click', '.content-editor_cancel', function (e) {
            e.preventDefault();

            _this.cancelCurrentContentChanges();
        });

        container.on('click', '.content-editor_params', function (e) {
            e.preventDefault();

            _this.toggleCurrentContentParams();
        });

        container.on('click', '.content-editor_save', function (e) {
            e.preventDefault();

            _this.saveCurrentContent();
        });
        
        container.on('submit', '.content-editable .content-form', function (e) {
            e.preventDefault();
            
            _this.saveCurrentContent();
            
            return false;
        });
        
        container.on('click', '.content-editable .content-form [type=submit]', function (e) {
            e.preventDefault();
            
            _this.saveCurrentContent();
            
            return false;
        });

        container.on('click', '.content-editor_remove', function (e) {
            e.preventDefault();

            _this.removeCurrentContent();
        });

        container.on('click', '.content-params_close', function (e) {
            e.preventDefault();

            _this.hideCurrentContentParams();
        });


    },

    setCurrentBlock: function (block) {
        var _this = this;

        _this.hideBlockMenu();


        $('.slot-content-editor').each(function () {
            _this.currentBlock = $(this);
            _this.cancelCurrentContentChanges();
        });

        _this.currentBlock = block;
        _this.currentBlockId = _this.currentBlock.attr('data-id');
    },

    showNewContentMenu: function () {
        var _this = this;

        var action = _this.currentBlock
                .find('.content-editor_add')
                .attr('data-content-action');

        _this.showBlockMenu(action);
    },

    addBlock: function (resource, method, params, token) {
        var _this = this;

        if (!resource) {
            return;
        }

        $.ajax({
            url: resource,
            type: method,
            data: params,
            headers: token ? {'X-CSRF-Token': token} : {},
            success: function (result) {
                _this.setBlockContent(result);

                _this.enableBlockEditMode();
                _this.setBlockEditContent();
            }
        });
    },

    setBlockContent: function (result) {
        var _this = this;

        if (!_this.currentBlockId) {
            //add new content
            var r = _this.currentBlock.before(result);
            _this.disableBlockAddMode();
            _this.removeBlockAdd();
            
            var newBlock = _this.currentBlock.prevAll('.slot-content-editor').first();
            _this.setCurrentBlock(newBlock)
        } else {
            //replace existed content
            _this.currentBlock.find('.content-show').html(result);

            _this.closeCurrentContentEditor();
        }
    },

    enableBlockAddMode: function () {
        var _this = this;

        $(_this.currentBlock).addClass('on-add');
    },

    disableBlockAddMode: function () {
        var _this = this;

        $(_this.currentBlock).removeClass('on-add');
    },

    removeBlockAdd: function () {
        var _this = this;

        _this.currentBlock.find('.content-create').remove();
    },

    //////////////////saving

    saveCurrentContent: function () {
        var _this = this;

        var form = _this.currentBlock.find('.content-editable').find('.content-form');

        var method = $(form).data('method');
        var resource = $(form).data('resource');
        var headers = $(form).data('csrf-token') ? {'X-CSRF-Token': $(form).data('csrf-token')} : {};
        var data = _this.currentBlock.find('.content-editable .content-form').find('input,textarea,select').serializeObject();
        
        if (!resource || !method || !data) {
            return;
        }

        $.ajax({
            url: resource,
            type: method,
            data: data,
            headers: headers,
            success: function (result, status, xhr) {
                var location = '';
                if (location = xhr.getResponseHeader('Content-Location')) {
                    $.ajax({
                        url: location,
                        success: function(result) {
                            _this.setBlockContent(result);
                        }
                    });
                }
            }
        });
    },

    /////////////////editing

    runCurrentContentEditor: function () {
        var _this = this;

        _this.enableBlockEditMode();
        _this.setBlockEditContent();
    },

    enableBlockEditMode: function () {
        var _this = this;

        $(_this.currentBlock).addClass('on-edit');
    },

    disableBlockEditMode: function () {
        var _this = this;

        $(_this.currentBlock).removeClass('on-edit');
    },

    setBlockEditContent: function () {
        var _this = this;

        $.ajax({
            url: '/cp/ru/content/' + _this.currentBlockId,
            success: function (result) {
                _this.currentBlock.find('.content-show').hide();
                _this.currentBlock.find('.content-editable').html(result);
                _this.currentBlock.find('.content-editable').show();

                var type = _this.currentBlock.attr('data-type');
                _this.trigger(type, 'edit', _this.currentBlock);
            }
        });
    },

    ////////////////////////removing

    removeCurrentContent: function () {
        var _this = this;
        
        var token = $(_this.currentBlock).parents('.content-slot').data('csrf-token');
        
        $.ajax({
            url: '/cp/ru/content/' + _this.currentBlockId,
            type: 'DELETE',
            headers: token ? {'X-CSRF-Token': token} : {},
            success: function () {
                _this.currentBlock.hide("slide", {direction: "right"}, 500, function () {
                    _this.currentBlock.remove();
                });
            }
        });
    },

    /////////////////////menu

    hideBlockMenu: function () {
        var _this = this;

        $(_this.blockMenuEl).hide("slide", {direction: "right"}, 500);
    },

    showBlockMenu: function (action) {
        var _this = this;

        if (!action) {
            return;
        }

        $.ajax({
            url: action,
            success: function (result) {
                _this.setBlockMenuContent(result);
                $(_this.blockMenuEl).show("slide", {direction: "right"}, 500);
            }
        });
    },

    setBlockMenuContent: function (result) {
        var _this = this;

        if ($(_this.blockMenuEl).length) {
            $(_this.blockMenuEl).replaceWith(result);
        }

        $('body').append(result);

        _this.initBlockMenuEvents();
    },

    initBlockMenuEvents: function () {
        var _this = this;

        $(_this.blockMenuEl).on('click', '.block-menu_close', function () {
            _this.hideBlockMenu();
        });

        $(_this.blockMenuEl).on('click', '.block-list_item', function () {
            var resource = $(this).data('resource');
            var method = $(this).data('method');
            var params = $(this).data('params');
            var token = $(this).data('csrf-token');
            _this.addBlock(resource, method, params, token);
        });
    },

    toggleCurrentContentParams: function () {
        var _this = this;

        var params = $(_this.currentBlock).find('.content-params');

        if (params.is(":visible")) {
            _this.hideCurrentContentParams();
        } else {
            _this.showCurrentContentParams();
        }
    },

    showCurrentContentParams: function () {
        var _this = this;

        var params = $(_this.currentBlock).find('.content-params');

        if (!params.find('.content-params_close').length) {
            var close = '<div class="content-params_close">' +
                    '<i class="glyphicon glyphicon-remove"></i>' +
                    '</div>';

            params.append(close);
        }

        params.show("slide", {direction: "right"}, 500);
    },

    hideCurrentContentParams: function () {
        var _this = this;

        var params = $(_this.currentBlock).find('.content-params');
        params.hide("slide", {direction: "right"}, 500);
    },

    cancelCurrentContentChanges: function () {
        var _this = this;

        if (!_this.currentBlock) {
            return;
        }

        if (_this.currentBlockId) {
            _this.closeCurrentContentEditor();

            var type = _this.currentBlock.attr('data-type');
            _this.trigger(type, 'cancel', _this.currentBlock);

        } else {
            _this.disableBlockAddMode();
            _this.removeBlockAdd();
        }
    },

    closeCurrentContentEditor: function () {
        var _this = this;

        _this.disableBlockEditMode();
        _this.currentBlock.find('.content-show').show();
        _this.currentBlock.find('.content-editable').empty();
        _this.currentBlock.find('.content-editable').hide();
    },

    //////////////////////////sorting

    enableBlockSorting: function () {
        var _this = this;

        var slot = $('.content-slot').each(function () {
            if ($(this).attr('data-single') == 1) {
                return;
            }

            $(this).sortable({
                placeholder: "sort-placeholder",
                handle: ".content-sortable",
                revert: true,
                axis: "y",
                containment: "document",
                appendTo: "parent",
                // helper: "clone",
                cursor: "move",
                opacity: 0.7,
                cancel: ".sort-disabled",
                delay: 150,
                distance: 5,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.height());
                    $('<div id="content-block-holder"></div>').insertAfter($(ui.item));
                },
                stop: _this.updateBlockPosition.bind(_this)
            });
        });

        $('.content-sortable')
    },

    updateBlockPosition: function (e, ui) {
        var _this = this;

        var currentBlock = ui.item;

        var position = currentBlock
                .prevAll('.slot-content-editor').first()
                .attr('data-id');
        position = position || 0;

        var action = '/cp/ru/content/' + currentBlock.attr('data-id');

        _this.moveBlockContent(action, position, currentBlock);
    },

    moveBlockContent: function (action, prevPositionId, currentBlock) {
        var _this = this;

        var data = {
            position_prev_id: prevPositionId
        };

        if (!action ||
                !data
                ) {
            return;
        }

        var holder = $('#content-block-holder');
        
        var token = $(currentBlock).parents('.content-slot').data('csrf-token');

        $.ajax({
            url: action,
            type: 'PUT',
            data: data,
            headers: token ? {'X-CSRF-Token': token} : {},
            success: function (result) {
                holder.remove();
            },
            error: function () {
                $(currentBlock).insertBefore(holder);
                holder.remove();
            }
        });
    },
};

$(document).ready(function () {
    Pina.slotEditor.init();
});






(function ($) {
    $.fn.serializeObject = function () {

        var self = this,
                json = {},
                push_counters = {},
                patterns = {
                    "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                    "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                    "push": /^$/,
                    "fixed": /^\d+$/,
                    "named": /^[a-zA-Z0-9_]+$/
                };


        this.build = function (base, key, value) {
            base[key] = value;
            return base;
        };

        this.push_counter = function (key) {
            if (push_counters[key] === undefined) {
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function () {

            // skip invalid keys
            if (!patterns.validate.test(this.name)) {
                return;
            }

            var k,
                    keys = this.name.match(patterns.key),
                    merge = this.value,
                    reverse_key = this.name;

            while ((k = keys.pop()) !== undefined) {

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if (k.match(patterns.push)) {
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if (k.match(patterns.fixed)) {
                    merge = self.build([], k, merge);
                }

                // named
                else if (k.match(patterns.named)) {
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);
