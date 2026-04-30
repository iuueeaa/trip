<?php
$sectionId = "test";
$sectionClass = "page-top__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="section <?php echo $sectionClass; ?>">
	<div class="section__wrap <?php echo $sectionClass; ?>__wrap">
		<div class="section__inner <?php echo $sectionClass; ?>__inner">
			<div class="<?php echo $sectionClass; ?>__xxx">
				<div class="<?php echo $sectionClass; ?>__xxx__wrap">
					<div class="<?php echo $sectionClass; ?>__xxx__imageBox">
						<?php setHtmlBgImage($sectionValue['image'], 'p-image'); ?>
					</div>
					<div class="<?php echo $sectionClass; ?>__xxx__textBox">
						<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
						<?php setHtmlText($sectionValue['lead'], 'p-lead'); ?>
						<?php setHtmlText($sectionValue['text'], 'p-text'); ?>
						<?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
					</div>
				</div>
			</div>
			<?php setHtmlBgImage($sectionValue['image'], 'p-image'); ?>
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php setHtmlText($sectionValue['lead'], 'p-lead'); ?>
			<?php setHtmlText($sectionValue['text'], 'p-text'); ?>
			<?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
			<?php
			$listClass = $sectionClass . '__list b-list__col3';
			$listValue = $sectionValue['list'];
			?>
			<div class="<?php echo $listClass; ?>">
				<div class="<?php echo $listClass; ?>__wrap">
					<ul class="<?php echo $listClass; ?>__ul">
						<?php foreach ($listValue as $box) : ?>
							<?php
							$boxClass = $sectionClass . '__box';
							$boxValue = $box;
							['url' => $boxUrl, 'target' => $boxTarget, 'title' => $boxTitle] = SetBoxLink($boxValue['link']);
							?>
							<li class="<?php echo $listClass; ?>__li">
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<div class="<?php echo $boxClass; ?>__imageBox">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</div>
										<div class="<?php echo $boxClass; ?>__textBox">
											<?php setHtmlTitle($boxValue["title"], "p-title__xxxx", "h3"); ?>
											<?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
											<?php setHtmlText($boxValue['text'], 'p-text'); ?>
											<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
										</div>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
			$listClass = $sectionClass . '__list b-list__col3';
			$listValue = $sectionValue['list'];
			?>
			<div class="<?php echo $listClass; ?>">
				<div class="<?php echo $listClass; ?>__wrap">
					<ul class="<?php echo $listClass; ?>__ul">
						<?php foreach ($listValue as $box) : ?>
							<?php
							$boxClass = $sectionClass . '__box';
							$boxValue = $box;
							['url' => $boxUrl, 'target' => $boxTarget, 'title' => $boxTitle] = SetBoxLink($boxValue['link']);
							?>
							<li class="<?php echo $listClass; ?>__li">
								<div class="<?php echo $boxClass; ?>">
									<div class="<?php echo $boxClass; ?>__wrap">
										<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" data-linktext="<?php echo $boxTitle; ?>" class="<?php echo $boxTitle; ?>__imageBoxLink">
											<?php setHtmlBgImage($boxValue['image'], 'p-image'); ?>
										</a>
										<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__textBoxLink">
											<?php setHtmlTitle($boxValue["title"], "p-title__xxxx", "h3"); ?>
											<?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
											<?php setHtmlText($boxValue['text'], 'p-text'); ?>

										</a>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div class="<?php echo $sectionClass; ?>__head"></div>
			<div class="<?php echo $sectionClass; ?>__body"></div>
			<div class="<?php echo $sectionClass; ?>__foot"></div>
		</div>
	</div>
</section>




<!-- <?php
			$sectionId = "about";
			$sectionClass = "page-top__" . $sectionId;
			$sectionValue = $this_page_value[$sectionId];
			?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> ">
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
							<?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
							<?php setHtmlText($boxValue['text'], 'p-text'); ?>
							<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>



<?php
$sectionId = "company";
$sectionClass = "page-top__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2 is-invert">
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
							<?php setHtmlText($boxValue['lead'], 'p-lead'); ?>
							<?php setHtmlText($boxValue['text'], 'p-text'); ?>
							<?php setHtmlLink($boxValue['link'], 'p-button'); ?>
						</div>
					</div>
				</div>
			</div>

			<?php
			// $blockClass = $sectionClass . '__list';
			$blockClass =  'p-list__col4';
			$blockValue = getContentListArray($sectionValue);
			?>
			<div class="<?php echo $blockClass; ?>">
				<div class="<?php echo $blockClass; ?>__wrap">
					<ul class="<?php echo $blockClass; ?>__ul">
						<?php foreach ($blockValue as $list) : ?>
							<li class="<?php echo $blockClass; ?>__li">
								<?php
								$boxClass = $sectionClass . '__box';
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
												<?php setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2'); ?>
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




<?php
$sectionId = "service";
$sectionClass = "page-top__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> ">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
			<?php
			// $blockClass = $sectionClass . '__list';
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
											<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
												<?php setHtmlTitle($boxValue['title'], 'p-title__box', 'h2'); ?>
												<?php setHtmlText($boxValue['lead'], 'p-lead');
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
			<div class="p-links">
				<?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
			</div>
		</div>
	</div>
</section>



<?php
$sectionId = "news";
$sectionClass = "page-top__" . $sectionId;
$sectionValue = $this_page_value[$sectionId];
?>
<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $sectionClass; ?> is-bg2">
	<div class="section__wrap">
		<div class="<?php echo $sectionClass; ?>__wrap">
			<?php setHtmlTitle($sectionValue["title"], "p-title__sec", "h2"); ?>
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
				</div>
			</div>

			<div class="p-links">
				<?php setHtmlLink($sectionValue['link'], 'p-button'); ?>
			</div>

		</div>
	</div>
</section>
 -->
