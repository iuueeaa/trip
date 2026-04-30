<main class="l-main">
	<?php
	if (!empty($this_page_value['section_mode'])) {
		include($root_path . "/assets/inc/page/" . $this_page_value['section_mode']  . ".php");
	}
	?>
	<a href="javascript:void(0);" class="js-totop__fix"><span></span></a>
</main>
