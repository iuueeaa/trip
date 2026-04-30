<?php
$current_dir = getcwd();
$post_type_slug = $current_dir ;
$GetId = (isset($_GET['id'])) ? $_GET['id'] : 0;
require_once("../assets/inc/_l-page.php");
