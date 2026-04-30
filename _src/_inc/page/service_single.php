<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "feature";
$sectionClass = "page-service__" . $sectionId;
$sectionValue = $this_page_value[$sectionId]
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue['title'], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue['text'], "p-lead__s"); ?>
			<?php
			// $blockClass = $sectionClass . '__list';
			$blockClass = 'p-list__flex';
			$blockValue = $sectionValue['list'];
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
								?>
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<div class="<?php echo $boxClass; ?>__imageBox imageBox">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</div>
										<div class="<?php echo $boxClass; ?>__textBox textBox">
											<div class="titleBox">
												<?php setHtmlTitle($boxValue['title'], 'p-title__sub', 'h3'); ?>
												<?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
												<?php setHtmlText($boxValue['text'], 'p-text'); ?>
											</div>
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




<?php
$sectionId = "point";
$sectionClass = "page-service__" . $sectionId;
$sectionValue = $this_page_value[$sectionId]
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue['title'], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue['text'], "p-lead__s"); ?>
			<?php
			// $blockClass = $sectionClass . '__list';
			$blockClass = 'p-list__col3';
			$blockValue = $sectionValue['list'];
			?>
			<div class="<?php echo $blockClass; ?>">
				<div class="<?php echo $blockClass; ?>__wrap">
					<ul class="<?php echo $blockClass; ?>__ul">
						<?php foreach ($blockValue as $list) : ?>
							<li class="<?php echo $blockClass; ?>__li">
								<?php
								// $boxClass = $sectionClass . '__box';
								$boxClass = 'p-box__tate';
								$boxValue = $list;
								?>
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<div class="<?php echo $boxClass; ?>__imageBox imageBox">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</div>
										<div class="<?php echo $boxClass; ?>__textBox textBox">
											<div class="titleBox">
												<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h3'); ?>
												<?php setHtmlText($boxValue['text'], 'p-text'); ?>
											</div>
										</div>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php //setHtmlPagerNum();
					?>
				</div>
			</div>
		</div>
	</div>
</section>




<?php
$sectionId = "table";
$sectionClass = "page-service__" . $sectionId;
$sectionValue = $this_page_value[$sectionId]
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue['title'], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue['text'], "p-lead__s"); ?>
			<div class="p-table">
				<table>
					<tbody>
						<?php foreach ($sectionValue['dl'] as $tr) : ?>
							<tr>
								<th><span><?php echo $tr['dt']; ?></span></th>
								<td><span><?php echo $tr['dd']; ?></span></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>



<?php
$sectionId = "relate";
$sectionClass = "page-service__" . $sectionId;
$sectionValue = $this_page_value[$sectionId]
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue['title'], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue['text'], "p-lead__s"); ?>
			<?php
			$blockClass = 'p-list__col3';
			$blockValue = getContentListArray($sectionValue);
			?>
			<div class="<?php echo $blockClass; ?>">
				<div class="<?php echo $blockClass; ?>__wrap">
					<ul class="<?php echo $blockClass; ?>__ul">
						<?php foreach ($blockValue as $list) : ?>
							<li class="<?php echo $blockClass; ?>__li">
								<?php
								// $boxClass = $sectionClass . '__box';
								$boxClass = 'p-box__tate';
								$boxValue = $list;
								['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
								?>
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</a>
										<div class="<?php echo $boxClass; ?>__textBox textBox">
											<div class="info">
												<?php setHtmlText($boxValue['date'], 'p-date'); ?>
												<?php setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true); ?>
											</div>
											<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
												<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
												<?php //setHtmlText($boxValue['lead'], 'p-lead');
												?>
												<?php setHtmlText($boxValue['text'], 'p-text'); ?>
											</a>
											<?php setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true); ?>
											<?php setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow'); ?>
										</div>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

			<div class="p-links">
				<div class="p-links__wrap">
					<?php setHtmlLink($sectionValue['link'], "p-button"); ?>
				</div>
			</div>
		</div>
	</div>
</section>
