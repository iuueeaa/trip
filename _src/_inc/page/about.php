<?php setHtmlMv($this_page_value, 'p-mv__sub'); ?>

<?php
$sectionId = "message";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php
			$boxClass = 'p-box__yoko';
			$boxValue = $sectionValue['imageText'];
			?>
			<div class="<?php echo $boxClass; ?>">
				<div class="<?php echo $boxClass; ?>__wrap">
					<div class="<?php echo $boxClass; ?>__imageBox imageBox">
						<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
					</div>
					<div class="<?php echo $boxClass; ?>__textBox textBox">
						<div class="titleBox">
							<?php setHtmlTitle($boxValue['title'], 'p-title__sub', 'h3'); ?>
							<?php setHtmlText($boxValue['text'], 'p-text'); ?>
							<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php setHtmlText($sectionValue["text"]); ?>
		</div>
	</div>
</section>


<?php
$sectionId = "vision";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue["text"]); ?>
			<?php setHtmlVideo($sectionValue["video"]); ?>
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
												<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
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
$sectionId = "outline";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>

			<div class="p-table">
				<table>
					<tbody>
						<?php foreach ($sectionValue['dl'] as $tr) : ?>
							<tr>
								<th><span><?php echo $tr['th']; ?></span></th>
								<td><span><?php echo $tr['td']; ?></span></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<?php
$sectionId = "history";
$sectionClass = $this_page_value['class'] . "__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?>">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php setHtmlHistory($sectionValue["history"]); ?>
		</div>
	</div>
</section>
