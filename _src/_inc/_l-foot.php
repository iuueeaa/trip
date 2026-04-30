<?php $footer_class = "l-footer"; ?>
<footer class="<?php echo $footer_class; ?>">
	<div class="<?php echo  $footer_class; ?>__pankuzu">
		<?php //setHtmlPankuzu($this_page_value);?>
	</div>
	<div class="<?php echo $footer_class; ?>__wrap">
		<div class="<?php echo $footer_class; ?>__inner">
			<div class="<?php echo $footer_class; ?>__head">
				<a href="<?php echo $link_path; ?>/" class="<?php echo $footer_class; ?>__logo">
					<?php setHtmlLogo('logo', 'p-logo', $site_title);  ?>
				</a>
				<?php setHtmlInfo(); ?>
				<?php setHtmlSns(); ?>
			</div>
			<div class="<?php echo $footer_class; ?>__body">
				<div class="<?php echo  $footer_class; ?>__body__wrap">
					<?php setHtmlNav($menu_list, 'footer1', $footer_class . '__nav'); ?>
					<?php setHtmlNav($menu_list, 'footer2', $footer_class . '__nav'); ?>
					<?php setHtmlNav($menu_list, 'footer3', $footer_class . '__nav'); ?>
					<?php setHtmlNav($menu_list, 'footer4', $footer_class . '__nav'); ?>
				</div>
			</div>
			<div class="<?php echo $footer_class; ?>__foot">
				<div class="<?php echo $footer_class; ?>__foot__wrap">
					<?php setHtmlNav($menu_list, 'footerSub', $footer_class . '__subnav'); ?>
					<div class="<?php echo $footer_class; ?>__copyright"><span><?php echo $copyright; ?></span></div>
				</div>
			</div>
		</div>
	</div>
</footer>
