<?php
function setHtmlPagerArr($index, $taxonomy = "category")
{
	global $link_path, $wpflg;
	$postindex = $link_path . '/' . $index;

	if ($wpflg) {
		$prev = get_adjacent_post(false, '', true, $taxonomy);
		$prev_link = $prev ? get_permalink($prev->ID) : '';
		$next = get_adjacent_post(false, '', false, $taxonomy);
		$next_link = $next ? get_permalink($next->ID) : '';
	} else {
		$prev = $next = true;
		$prev_link = $next_link = $link_path . "/";
	}
?>
	<div class="p-pager__arr">
		<ul>
			<li>
				<?php if ($prev) : ?>
					<a class="p-pager__arr__prev arr" rel="prev" href="<?php echo $prev_link; ?>"><span>PREV</span></a>
				<?php endif; ?>
			</li>
			<li><a href="<?php echo $postindex; ?>" class="p-pager__arr__index"><span>INDEX</span></a></li>
			<li>
				<?php if ($next) : ?>
					<a class="p-pager__arr__next arr" rel="next" href="<?php echo $next_link; ?>"><span>NEXT</span></a>
				<?php endif; ?>
			</li>
		</ul>
	</div>
	<?php
}

function setHtmlPagerNum($args = array())
{
	global $wpflg;
	$class = "p-pager__num";
	$show_only = false;
	$range = 2;

	if ($wpflg) :
		$the_query = new WP_Query($args);
		$maxposts = $the_query->found_posts;
		$pageposts = $args["posts_per_page"];
		wp_reset_postdata();
		$pages = (int) $the_query->max_num_pages;
		$paged = isset($args['paged']) ? (int)$args['paged'] : max(1, get_query_var('paged'));
		$firstpost = ($paged - 1) * $pageposts + 1;
		$lastpost = $pages > 1 ? min($firstpost + $pageposts - 1, $maxposts) : $maxposts;
		$query_args = $_GET;
		$post_type = $args['post_type'] ?? 'post';
		$base_url = get_post_type_archive_link($post_type);
	?>
		<div class="<?php echo $class; ?>">
			<ul>
				<?php if ($show_only && $pages === 1) : ?>
					<li>
						<div><span>1</span></div>
					</li>
				<?php endif; ?>
				<?php if ($pages !== 1) : ?>
					<?php if ($paged > 1) : ?>
						<li><a href="<?php echo add_query_arg(array_merge($query_args, ['paged' => $paged - 1]), $base_url); ?>" class="prev"><span>PREV</span></a></li>
					<?php endif; ?>
					<?php for ($i = 1; $i <= $pages; $i++) : ?>
						<?php if ($i <= $paged + $range && $i >= $paged - $range) : ?>
							<?php if ($paged === $i) : ?>
								<li>
									<div><span><?php echo $i; ?></span></div>
								</li>
							<?php else : ?>
								<li><a href="<?php echo esc_url(add_query_arg(array_merge($query_args, ['paged' => $i]), $base_url)); ?>">
										<span><?php echo $i; ?></span></a>
								</li>
							<?php endif; ?>
						<?php endif; ?>
					<?php endfor; ?>
					<?php if ($paged < $pages) : ?>
						<li><a href="<?php echo add_query_arg(array_merge($query_args, ['paged' => $paged + 1]), $base_url); ?>" class="next"><span>NEXT</span></a></li>
					<?php endif; ?>
				<?php endif; ?>
			</ul>
		</div>
		<?php if ($maxposts != 0) : ?>
			<!-- <div class="pageresult">検索結果<?php echo $maxposts; ?>件中<?php echo $firstpost; ?>-<?php echo $lastpost ?>件</div> -->
		<?php endif; ?>
	<?php else : ?>
		<div class="<?php echo $class; ?>">
			<ul>
				<li>
					<div><span>1</span></div>
				</li>
				<li><a href="#"><span>2</span></a></li>
				<li><a href="#"><span>3</span></a></li>
				<li><a href="#"><span>4</span></a></li>
				<li><a href="#"><span>5</span></a></li>
			</ul>
		</div>
<?php
	endif;
}


function setHtmlArchivePagePager($postlist)
{
	global $wpflg;
	if ($postlist['pager']) {
		if ($wpflg) {
			setHtmlPagerNum($postlist['args']);
		} else {
			setHtmlPagerNum();
		}
	}
}

function setHtmlPagerFromPostList($postList) {
	global $wpflg;
	if (empty($postList['pager'])) return;
	if ($wpflg) {
		$args = formatPostListToArgs($postList);
		$paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
		$args['paged'] = max(1, (int)$paged);
		setHtmlPagerNum($args);
	} else {
		setHtmlPagerNum();
	}
}
