<?php setHtmlMv($this_page_value, 'p-mv__noimage is-center'); ?>

<?php
$sectionId = "index";
$sectionClass = "page-news__" . $sectionId;
$sectionValue = $this_page_value
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> pt0 is-narrow">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php
			$blockClass = 'p-list__news';
			$blockValue = getContentListArray($sectionValue);
			?>
			<div class="<?php echo $blockClass; ?>">
				<div class="<?php echo $blockClass; ?>__wrap">
					<ul class="<?php echo $blockClass; ?>__ul">
						<?php foreach ($blockValue as $list) : ?>
							<li class="<?php echo $blockClass; ?>__li">
								<?php
								$boxClass = 'p-news';
								$boxValue = $list;
								['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
								?>
								<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>">
									<dl class="<?php echo $boxClass; ?>__dl">
										<dt class="<?php echo $boxClass; ?>__dt">
											<?php setHtmlText($boxValue['date'], 'p-date'); ?>
											<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, false); ?>
										</dt>
										<dd class="<?php echo $boxClass; ?>__dd">
											<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
										</dd>
									</dl>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php
					setHtmlPagerFromPostList($sectionValue['postList']);
					?>
				</div>
			</div>
		</div>
	</div>
</section>
