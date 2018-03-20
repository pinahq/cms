var Pina = Pina || {};

Pina.ajax = function(options)
{
	if (!options.type) options.type = 'get';
	if (1)
	{
		options.data = options.data || {};
		options.data[options.type] = options.url;
		options.url = '/pina.php';
	}
	if (options.type == 'put' || options.type == 'delete')
	{
		options.type = 'post';
	}
	$.ajax(options);
}

Pina.table = {
	
	resource: function(options, data)
	{
		var resource = options['resource'];
		
		if (!data) return resource;

		for (var i in data)
		{
			if (!data[i]) continue;
			resource = resource.replace(':' + i + '/', data[i] + '/');
		}
		
		return resource;
	},

	handleError: function(packet)
	{
		if (packet && packet.__messages) {
			var errors = [];

			for (i = 0; i < packet.__messages.length; i++)
			{
                var m = packet.__messages[i];
				if (m[0] == 'error')
				{
					errors.push(m[1]);
				}
			}

			if (errors.length > 0) {
				alert(errors.join('\n'));
			}
		}
		else
		{
			alert(PinaLng.lng("request_sending_failed"));
		}
	},

	reload: function(elem, options, page, data)
	{
		$(elem).fadeTo(0, 0.5);

		data['page'] = page;

		var sort = Pina.table.getSort(elem);
		if (sort)
		{
			data['sort'] = sort;
			data['sort_up'] = Pina.table.getSortUp(elem, sort);
		}
		


		Pina.ajax({
			type: 'get',
			url: Pina.table.resource(options, data) + (options['layout']?('.' + options['layout']):''),
			data: data,
			success: function(html) {
				//console.log(elem);
				//console.log(html);
				$(elem).html($('<div>' + html + '</div>').find(elem.selector).html());
				$(elem).fadeTo(0, 1);
				$(elem).trigger("table_reloaded");

				Pina.table.bind(elem, options);
			},
			error: function() {
				alert('error');
			},
			dataType: 'html'
		});
	},
	
	bind: function(elem, options)
	{
        Pina.table.bindReload(elem, options);

		Pina.table.bindClick(elem);
		Pina.table.bindEdit(elem, options);
		Pina.table.bindDelete(elem, options);
		Pina.table.bindCreate(elem, options);
        
        Pina.table.bindBath(elem, options);
		
		Pina.table.bindPaginator(options);
		Pina.table.bindSorting(elem, options);
	},

    bindReload: function(elem, options)
    {
        elem.on('table_reload', function() {
            if (!options['oncall']) return;
            var fnReload = options['oncall'];
            fnReload();
        });
    },
	
	bindClick: function(elem)
	{
		elem.find(".editable a").on("click", function()
		{
			var link = $(this).attr("href");
			if (!link) return true;
			document.location = link;
			return false;
		});
	},
	
	bindEdit: function(elem, options)
	{
		elem.find(".edit, .editable").on("click", function()
		{
			var id = $(this).attr("sid");			
			if (!id) id = $(this).parent().attr("sid");
			if (!id) id = $(this).parent().attr("id");
			if (!id) alert("Please specify SID");
			
			var focus = false;
			if ($(this).is(".editable"))
			{
				focus = $(this).index();
			}

			Pina.ajax({
				type: 'get',
				url: options['resource'] + '/' + id + (options['layout']?('.' + options['layout']):''),
                data: {'display': 'row-edit'},
				success: function(html) {
					var e = $("." + options["object"] + "-" + id).html(html);
					Pina.table.bindUpdate(e, options);
					Pina.table.bindCancel(e, options);
					if (focus)
					{
						$("." + options["object"] + "-" + id).children(":eq("+focus+")").find("input").focus();
						
					}
				},
				dataType: 'html'
			});
			return false;
		});
	},
	
	bindUpdate: function(elem, options)
	{
		elem.find(".accept").on("click", function()
		{
			var id = $(this).attr("sid");
			if (id != 'none' && id) {
				var params = $("." + options["object"] + "-" + id).getData();
				Pina.ajax({
					type: 'put',
					url: options['resource'] + '/' + id,
					data: params,
					success: function() {
						Pina.table.requestGet(options, id, function() {
							elem.trigger("row_changed", [id]);
						});
					},
					error: function(xhr) {
						var packet = eval('(' + xhr.responseText + ')');
						Pina.table.handleError(packet);
					},
					dataType: 'html'
				});
			}
			return false;
		});
		
		elem.find("input[type=text]").on("keypress", function(e)
		{
			if (e.keyCode == 13)
			{
				$(this).parents("tr,ul").find(".accept,.add").trigger("click");
				return false;
			}
			return true;
		});
	},

	bindCancel: function(elem, options)
	{
		elem.find(".decline").on("click", function()
		{
			var id = $(this).attr("sid");
			if (id != 'none' && id) {
				Pina.table.requestGet(options, id, false);
			}
			return false;
		});
	},
	
	bindDelete: function(elem, options)
	{
		elem.find(".delete").on("click", function()
		{
			if (!$(this).confirmDeleteMessage()) return false;

			var id = $(this).attr("sid");
			if (!id) alert("Please specify SID");

			Pina.table.requestDelete(options, id, options["oncall"]);
			
			elem.trigger("row_deleted");

			return false;
		});
	},
		
	requestGet: function(options, id, onSuccess)
	{
		Pina.ajax({
			type: 'get',
			url: options['resource'] + '/' + id  + (options['layout']?('.' + options['layout']):''),
            data: {'display': 'row'},
			success: function(html) {
				var e = $("." + options["object"] + "-" + id).html(html);
				Pina.table.bindEdit(e, options);
				Pina.table.bindDelete(e, options);
				Pina.table.bindClick(e);
				if (onSuccess) onSuccess();
			},
			dataType: 'html'
		});
	},
		
	requestDelete: function(options, id, fnReload)
	{
		if (!id) return;

		Pina.ajax({
			type: 'delete',
			url: options['resource'] + '/' + id,
			success: function(packet) {
				if (packet && packet.e) {
					var errors = []
					for (i = 0; i < packet.e.length; i++)
					{
						errors.push(packet.e[i].m);
					}

					if (errors.length > 0) {
						alert(errors.join('\n'));
					}
				} else {
					if (fnReload)
					{
						fnReload(Pina.table.getPage());
					}
				}
			},
			error: function(xhr) {
				var packet = eval('(' + xhr.responseText + ')');
				Pina.table.handleError(packet);
			},
			dataType: 'json'
		});
	},
	
	bindCreate: function(elem, options)
	{
		var fnAdd = function(id) 
		{
			if (!id) alert("Please specify SID");
	
			var params = $("." + options["object"] + "-" + id).getData();

			var fnReload = options["oncall"];


			$(elem).fadeTo(0, 0.5);
			Pina.ajax({
				type: 'post',
				url: Pina.table.resource(options, params),
				data: params,
				success: function(packet, code) {
					if (fnReload)
					{
						fnReload(Pina.table.getPage());
						elem.trigger("row_added");
					}
					$(elem).fadeTo(0, 1);
				},
				error: function(xhr) {
					var packet = eval('(' + xhr.responseText + ')');
					Pina.table.handleError(packet);
					$(elem).fadeTo(0, 1);
				},
				dataType: 'json'
			});
		};

		elem.find(".add").on("click", function()
		{
			fnAdd($(this).attr("sid"));
			return false;
		});
/*
		elem.find("#add input[type=text]").on("keypress", function(e)
		{
			if (e.keyCode == 13)
			{
				fnAdd('add');
				return false;
			}
			return true;
		});
*/
	},
    
    bindBath: function(elem, options)
    {
        var ch = elem.find('.check-all');
        if (!ch.length) return;
        
        var cl = ch.attr('data-target');
        if (!cl) return;
        
        var set = elem.find('.' + cl);
        
        var buttons = elem.siblings('.checkbox-operations').find('button');
        
        var onChange = function() {
            var found = false;
            var enabled = set.is(':checked');
            buttons.each(function() {
                $(this)[0].disabled = !enabled;
            });
        };
        
        ch.on('click', function() {
            var checked = ch.is(":checked");
            set.each(function() {
               $(this)[0].checked = checked; 
            });
            onChange();
        });
        
        set.on('change', onChange);
        
        buttons.on('click', function() {
            var action = $(this).attr('data-action');
            var method = $(this).attr('data-method');
            var params = {'id': []};
            set.each(function() {
               if ($(this)[0].checked) {
                   params['id'].push($(this).val());
               }
            });
            Pina.ajax({
                url: action,
                type: method,
                data: params,
				dataType: 'json',
				success: function(result) {
                    var fnReload = options["oncall"];
                    fnReload(Pina.table.getPage());
                    onChange();
				}
            });
            
        });
    },
		
	bindPaginator: function(options)
	{
		$(".paginator a").on("click", function()
		{
			var fnReload = options["oncall"];
			fnReload($(this).attr("data-value"));
			return false;
		});
	},
		
	bindSorting: function(elem, options)
	{
		elem.find("tr th a, ul.sorting li a").on("click", function()
		{
			var new_class = "sort-up";
			if ($(this).parent().find("span").hasClass("sort-up"))
			{
				new_class = "sort-down";
			}
			$(this).parent().parent().find("span").removeClass("sort-up sort-down");
			$(this).parent().find("span").addClass(new_class);

			var fnReload = options["oncall"];
			fnReload(Pina.table.getPage());
			return false;
		});
	},
		
		
	getPage: function()
	{
		var page = $(".paginator .current").html();
		if (!page) page = 0;
		return page;
	},

	getSort: function(elem)
	{
		return $(elem).find("tr th span.sort-up, tr th span.sort-down, ul.sorting li span.sort-up, ul.sorting li span.sort-down").attr("data-value");
	},

	getSortUp: function(elem, sort)
	{
		if ($(elem).find("tr th span.sort-up[data-value="+sort+"], tr th span.sort-down[data-value="+sort+"], ul.sorting li span.sort-up[data-value="+sort+"], ul.sorting li span.sort-down[data-value="+sort+"]").hasClass("sort-up"))
		{
			return "1";
		}
		return "0";
	}

}

$.fn.getData = function()
{
        var options = {};
	var elem = this;

        elem.find('select').each(function() {
            options[this.name] = elem.find('select[name='+this.name+']').val();
        });

        elem.find('select').each(function() {
            options[this.name] = elem.find('select[name='+this.name+']').val();
        });

        elem.find('input').each(function() {
            options[this.name] = elem.find('input[name='+this.name+']').val();
        });

        elem.find('textarea').each(function() {
            options[this.name] = elem.find('textarea[name='+this.name+']').val();
        });

        return options;
}


$.fn.manageTable = function(options)
{
	var elem = this;
	
	if (!options['resource']) return;

	if (!options['oncall'])
	{
		options["oncall"] = function(page)
		{
			var extra = {};
			if (options["oncall_extra"])
			{
				var fnExtra = options["oncall_extra"];
				extra = fnExtra();
			}
			if (options["filter"])
			{
				var data = $(options["filter"]).getData();
				for (var i in data)
				{
					extra[i] = data[i];
				}
			}
			Pina.table.reload(
				elem, options, page, extra
			);
		}
	}

	if (options["filter"])
	{
		$(options["filter"]).find("input").on("change", function() {
			var fnReload = options["oncall"];
			fnReload(0);
		});

		$(options["filter"]).on("submit", function() {
			return false;
		});
	}
	
	Pina.table.bind(elem, options);


	if (elem.hasClass('dnd'))
	{
		$(elem).on("table_reloaded", function(){
			var sortableItems = elem.find("div.tbody ul.tr:not(.no-dnd)");
			if (sortableItems.length)
			{
				var settings = {
					columns : 'div.table div.tbody'
					//,
					//handleSelector: 'li'
				}

				sortableItems.find(settings.handleSelector).css({
					cursor: 'move'
				}).mousedown(function (e) {
					sortableItems.css({width:''});
					$(this).parent().css({
						width: $(this).parent().width() + 'px'
					});
				}).mouseup(function () {
					if(!$(this).parent().hasClass('dragging')) {
						$(this).parent().css({width:''});
					} else {
						$(settings.columns).sortable('disable');
					}
				});

				$(settings.columns).sortable({
					items: sortableItems,
					connectWith: $(settings.columns),
					handle: settings.handleSelector,
					placeholder: 'widget-placeholder',
					forcePlaceholderSize: true,
					revert: 300,
					delay: 100,
					opacity: 0.8,
					containment: 'document',
					start: function (e, ui) {
						$(ui.helper).addClass('dragging');
					},
					stop: function (e, ui) {
						$(ui.item).css({width:''}).removeClass('dragging');
						$(settings.columns).sortable('enable');

						var data = [];
                        var params_object = options["object"];
                        var order = 1;
						$(elem).find("ul.tr:not(.no-dnd)").each(function(){
							var id = $(this).attr("id");
                            var line = {};
                            line[options["object"] + "_id"] = id;
                            line[options["object"] + "_order"] = order++;
							data.push(line);
						});

                        var params = {};
                        params[options['resource'].split('/').pop()] = data;

						$(elem).fadeTo(0, 0.5);
						Pina.ajax({
							type: 'put',
							url: Pina.table.resource(options, params),
							data: params,
							dataType: 'text',
							success: function(result) {
								$(elem).fadeTo(0, 1);
							},
							error: function(xhr) {
								var packet = eval('(' + xhr.responseText + ')');
								Pina.table.handleError(packet);
								$(elem).fadeTo(0, 1);
							}
						});
					}
				});
			}
		});

        $(document).ready(function(){
            $(elem).trigger('table_reloaded');
        });
	}

}


$.fn.confirmDeleteMessage = function()
{
	var message = $(this).attr("data-message");
	if (!message) message = PinaLng.lng("are_you_sure_to_delete");

	if (!confirm(message)) {
		return false;
	}
	return true;
}





$.fn.lastWeek = function(selector_start, selector_end)
{
	$(this).on("click", function() {
		var d=new Date();
		var last_day=d.getDate();
		var last_month=d.getMonth()+1;
		var last_year=d.getFullYear();
		var first_day = 0;
		var first_month = 0;
		var first_year = 0;

		first_day = last_day - 6;
		if((first_day == 0) || (first_day < 0))
		{
			first_month = last_month - 1;
			if(first_month == 0)
			{
				first_year = last_year - 1;
				first_month = 12;
			}
			else
			{
				first_year = last_year;
			}

			var dayCount = new Date(last_year, first_month, 0).getDate();
			first_day = dayCount + first_day;
		}
		else
		{
			first_month = last_month;
			first_year = last_year;
		}

		if(first_day < 10)
		{
			first_day = '0' + first_day;
		}
		if(first_month < 10)
		{
			first_month = '0' + first_month;
		}
		if(last_day < 10)
		{
			last_day = '0' + last_day;
		}
		if(last_month < 10)
		{
			last_month = '0' + last_month;
		}
		$(selector_start).val(first_day + "." + first_month + "." + first_year);
		$(selector_end).val(last_day + "." + last_month + "." + last_year);
		$(selector_start).trigger("change");
		return false;
	});
}


$.fn.lastMonth = function(selector_start, selector_end)
{
	$(this).on("click", function() {
		var d=new Date();
		var day=d.getDate();
		var last_month=d.getMonth()+1;
		var last_year=d.getFullYear();
		var first_month = 0;
		var first_year = 0;

		first_month = last_month - 1;
		if(first_month == 0)
		{
			first_year = last_year - 1;
			first_month = 12;
		}
		else
		{
			first_year = last_year;
		}

		if(day < 10)
		{
			day = '0' + day;
		}
		if(first_month < 10)
		{
			first_month = '0' + first_month;
		}
		if(last_month < 10)
		{
			last_month = '0' + last_month;
		}

		$(selector_start).val(day + "." + first_month + "." + first_year);
		$(selector_end).val(day + "." + last_month + "." + last_year);
		$(selector_start).trigger("change");
		return false;
	});
}

$.fn.lastAll = function(selector_start, selector_end)
{
	$(this).on("click", function()
	{
		$(selector_start).val('');
		$(selector_end).val('');
		$(selector_start).trigger("change");
		return false;
	});
}




$.fn.lastDay = function(selector_start, selector_end)
{
	$(this).on("click", function() {
		var d=new Date();
		var day=d.getDate();
		var month=d.getMonth()+1;
		var year=d.getFullYear();

		
		if(day < 10)
		{
			day = '0' + day;
		}
		if(month < 10)
		{
			month = '0' + month;
		}

		$(selector_start).val(day + "." + month + "." + year);
		$(selector_end).val(day + "." + month + "." + year);
		$(selector_start).trigger("change");
		return false;
	});
}
