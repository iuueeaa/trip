<?php
/*
	Template Name: ttgbcheck
	*/

$time_start = microtime(true);

echo "test!<br>";

$timelimit = microtime(true) - $time_start;
echo $timelimit . ' seconds';


$local_path       = get_template_directory_uri();
$root_path        = get_template_directory();

use Michelf\MarkdownExtra;

if (have_posts()) : while (have_posts()) : the_post();
    $page_class       = "contact";
    $page_title       = strip_tags(get_the_title());
    $page_description = strip_tags(get_field('description'));
    $page_type        = "website"; // or blog
    $page_ogimage     = get_field('ogp')['image']['sizes']['large'];
    include($root_path . "/assets/inc/_l-head_wp.php");

    include($root_path . "/assets/inc/_l-header.php");
    $time_start = microtime(true);
    echo "test!<br>";
    $timelimit = microtime(true) - $time_start;
    echo $timelimit . ' seconds';


  endwhile;
endif; ?>
<?php include('footer.php'); ?>
