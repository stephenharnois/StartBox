<form class="searchform" method="get" action="<?php echo home_url(); ?>">
	<div>
		<input name="s" type="text" class="searchtext" value="" title="<?php echo apply_filters( 'sb_search_text', 'Type your search and press Enter.' ); ?>" size="10" tabindex="1" />
		<input type="submit" class="searchbutton button" value="Search" tabindex="2" />
	</div>
</form>