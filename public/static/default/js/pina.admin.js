function intval(mixed_var, base)
{ 	// Get the integer value of a variable
	// 
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	var tmp;

	if (typeof(mixed_var) == 'string')
	{
		tmp = parseInt(mixed_var);

		if (isNaN(tmp))
		{
			return 0;
		}
		else
		{
			return tmp.toString(base || 10);
		}
	}
	else if(typeof(mixed_var) == 'number')
	{
		return Math.floor(mixed_var);
	}

	return 0;
}

function updateTreeLevelIcons($table)
{
	if (!$($table).hasClass('category-tree')) { // не трогаем дерево категорий - оно обрабатывается по-другому
		$("tr td.tree", $table).each(function () {
			var $this = $(this);

			var parentLevel = $this.parent().data("level");
			var nextLevel = $this.parent().next().data("level");
			if (parentLevel == undefined)
			{
				return;
			}

			$("span.tree-level", $this).remove();
			for (var i = 0; i < parentLevel; i++)
			{
				$span = $("<span class='tree-level'></span>");
				if (i == 0 && parentLevel < nextLevel)
				{
					$span.addClass("expanded");
				}
				$this.prepend($span);
			}
		});
	}
}

function slideRowDown($row, duration)
{
	$row.find('td')
		.wrapInner('<div class="wrapper" style="display: none;" />')
		.parent()
		.show()
		.find('td > div')
		.slideDown(duration, function() {
			var $this = $(this);
			$this.replaceWith($this.contents());
		});
}

function slideRowUp($row, duration)
{
	$row.find('td')
		.wrapInner('<div class="wrapper" style="display: block;" />')
		.parent()
		.find('td > div')
		.slideUp(duration, function(){
			var $this = $(this);
			$this.parent().parent().hide();
			$this.replaceWith($this.contents());
		});
}

function expandCollapseNode($node)
{
	var $tr = $node.parent().parent();
	var parentLevel = $tr.data("level");

	$tr = $tr.next();
	while ($tr.is("tr"))
	{
		var rowLevel = $tr.data("level");
		if (rowLevel == undefined || rowLevel <= parentLevel)
		{
			break;
		}

		if ($node.hasClass("collapsed"))
		{
			slideRowDown($tr, 300);
			//$tr.show();
			$("span.collapsed", $tr)
				.removeClass("collapsed")
				.addClass("expanded");
		}
		if ($node.hasClass("expanded"))
		{
			slideRowUp($tr, 300);
			//$tr.hide();
		}
		$tr = $tr.next();
	}

	$node.toggleClass("collapsed");
	$node.toggleClass("expanded");
}

	// Переключение элементов селектора
	$(document).on("click", "ul.selector li a,ul.filter li a", function () {
		var $selector = $(this).parent().parent();

		if ($("a", $selector).hasClass('disabled')) {
			alert('Can not be changed.');
			return false;
		}

		$("a", $selector).removeClass("selected");
		$(this).addClass("selected");
		return false;
	});

	$(document).on("click", "ul.selector-input li a,ul.filter-input li a", function () {
		var $selector = $(this).parent().parent();
		$(".selector-input-" + $selector.attr("data-name")).val(
			$(this).attr("data-value")
		);
		setTimeout(function() {
			$(".selector-input-" + $selector.attr("data-name")).trigger("change");
		}, 10);
	});

$(document).ready(function () {

	//$("ul.selector li a").addClass("css3");

	// Удаляем старые и добавляем новые [+] / [-] в узлы дерева
	updateTreeLevelIcons($("table.tree"));

	// Сворачивание / разворачивание узлов дерева
	$(document).on("click", "span.collapsed, span.expanded", function () {
		if (!$(this).hasClass('category-tree-level')) { // не трогаем дерево категорий - оно обрабатывается по-другому
			expandCollapseNode($(this));
		}
	});

	// Переключатель левого меню
	if (Pina.cookie.get("menu_toggle") == 'hide') $("#wrapper").addClass('no-menu');
	$(".menu-toggle").on("click", function() {
		if ($("body > .wrapper").toggleClass("no-menu").hasClass("no-menu"))
			status = 'hide'; 
		else
			status = 'show';

		Pina.cookie.set('menu_toggle', status, 30);
	});

	// why?
	//$("#export-catagories-list tr[data-level=1] span.expanded").click();

	$('.add-new-row').hide();

	// Дополнительную форму сохранения прижимаем к низу страницы
	$operationsBottom = $('.operations').filter('.bottom').each(function() {
		$this = $(this);
		var $leftColumn = $("#main .left-wide-column");
		var $rightColumn = $("#main .right-narrow-column");
		var rightBottom = $rightColumn.height();
		var needBottom = Math.max($leftColumn.height(), $rightColumn.height());
		$this.css("padding-top", needBottom - rightBottom);
	});

});