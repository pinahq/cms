
	<div id="footer" class="bigft clearfix noprint">
		<div class="container clearfix">
			<div class="footer_box">
				<div class="footer_inner_box clearfix">
					{module get="menus/:menu_key" display="widget" menu_key="help"}
				</div><!-- footer_inner_box -->
			</div><!-- footer_box -->

			<div class="footer_box middle">
				<div class="footer_inner_box clearfix">
					{module get="menus/about_us" display="widget" menu_key="about_us"}
				</div><!-- footer_inner_box -->
			</div><!-- footer_box -->

			<div class="footer_box">
				<div class="footer_inner_box clearfix">
					{*view get="social/links"*}
				</div><!-- footer_inner_box -->
			</div><!-- footer_box -->

			<p class="footer_notes">
				<span class="copyright">&copy; {php}echo date("Y");{/php}. <a href="http://dobrosite.ru/">ООО "Добросайт"</a>
				{*view get="feedback/link"*}</span>
				<span>{include file="Skin/copyright.tpl"}</span>
			</p>

			<p>&nbsp;</p>

			{include file="Skin/counters.tpl"}

		</div><!-- container -->
	</div><!-- end footer -->