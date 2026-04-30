<?php
$sectionId = "detail";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value;
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2 is-narrow">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__pankuzu"><?php setHtmlPankuzu($this_page_value); ?></div>
		<div class="<?php echo $sectionClass; ?>__wrap">
			<div class="<?php echo $sectionClass; ?>__head">
				<?php setHtmlTitle($this_page_value['title']); ?>
				<div class="<?php echo $sectionClass; ?>__info">
					<?php if ($p_key == 'news' || $p_key == 'topics') : ?>
						<p class="p-date"><span><?php echo $this_page_value['date']; ?></span></p>
					<?php endif; ?>
					<?php setHtmlTaxonomy($this_page_value['taxonomy']['category'], 'p-category', false, true); ?>
				</div>
				<?php
				$pt = $this_page_value['post_type'] ?? $p_key ?? '';
				$show_agenda = $this_page_value['agenda'] ?? ($GLOBALS['page_' . $pt]['agenda'] ?? false);
				if ($show_agenda) { setHtmlAgenda($sectionValue['body']); }
				?>
			</div>

			<?php setHtmlBody($sectionValue['body']); ?>

			<div class="<?php echo $sectionClass; ?>__foot">
				<?php
				if (!empty($this_page_value['taxonomy']['tag'])) {
					setHtmlTaxonomy($this_page_value['taxonomy']['tag'], 'p-tag', false, true);
				}
				?>
				<?php
				setHtmlSnsshare();
				setHtmlPagerArr($this_page_value['post_type'])
				?>
			</div>
		</div>
	</div>
</section>
