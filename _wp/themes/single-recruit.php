<?php
if (have_posts()) :
	while (have_posts()) : the_post();
		$this_page_value = array();
		include('_value.php');
		$this_page_value['nav']['pankuzu'] = array('home', $post_type_slug);
		$this_page_value['taxonomy']['category'] = get_the_terms(get_the_ID(), $post_type . '_category');
	endwhile;
endif;
include('header.php');
include($root_path . "/assets/inc/page/recruit_single.php");
include('footer.php');
