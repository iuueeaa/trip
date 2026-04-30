<?php
/*
*	Template Name: お問い合わせ
*/
if ($post->post_name == 'confirm') {
	session_start();
	$confirmFlg = 1;
}
include(get_template_directory() . "/_l-page_wp.php");
