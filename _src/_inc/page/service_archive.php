<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "index";
$sectionClass = "page-service__" . $sectionId;
$sectionValue = $this_page_value
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php
			// $blockClass = $sectionClass . '__list';
			$blockClass = 'p-list__flex';
			$blockValue = getContentListArray($sectionValue);
			?>
			<div class="<?php echo $blockClass; ?>">
				<div class="<?php echo $blockClass; ?>__wrap">
					<ul class="<?php echo $blockClass; ?>__ul">
						<?php foreach ($blockValue as $list) : ?>
							<li class="<?php echo $blockClass; ?>__li">
								<?php
								// $boxClass = $sectionClass . '__box';
								$boxClass = 'p-box__yoko';
								$boxValue = $list;
								['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
								?>
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</a>
										<div class="<?php echo $boxClass; ?>__textBox textBox">
											<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
												<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
												<?php //setHtmlText($boxValue['lead'], 'p-lead');
												?>
												<?php setHtmlText($boxValue['text'], 'p-text'); ?>
											</a>
											<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
										</div>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
