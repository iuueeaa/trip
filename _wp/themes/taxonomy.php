<?php
$term_name = single_term_title("", false);
$term_id = get_queried_object_id();
$term_slug = $term;
$term_info = get_queried_object();
$taxonomy_slug = $term_info->taxonomy;
$taxonomy_name = get_taxonomy($taxonomy_slug)->label;

$post_type =  explode("_", $taxonomy_slug)[0];
$this_page_value = ${'page_' . $post_type};
$archiveflg = true;
include('_value.php');




include("header.php");
$this_page_value['mv']['lead'] = $term_info->name . 'の記事';
$this_page_value['mv']['text'] = '';
// $taxonomy_acf = get_field('value', $post_type_slug);
$this_page_value['postList']['tax_query'] = array(
  array(
    'taxonomy' => $taxonomy_slug,
    'field' => 'slug',
    'terms' => $term_slug,
  ),
);

include($root_path . "/assets/inc/_l-page.php");
include('footer.php');
