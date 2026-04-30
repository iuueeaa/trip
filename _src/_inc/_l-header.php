<?php 
	$header_class = "l-header";
	$hamburger_class = $header_class."__hbg";
?>
<header class="<?php echo $header_class; ?>">
	<div class="<?php echo $header_class; ?>__wrap">
		<div class="<?php echo $header_class; ?>__inner">
			<div class="<?php echo $header_class; ?>__head">
				<a href="<?php echo $link_path; ?>/" class="<?php echo $header_class; ?>__logo js-invertParts">
					<?php setHtmlLogo('logo', 'p-logo', $site_title);  ?>
				</a>
			</div>
			<div class="<?php echo $header_class; ?>__body">
				<?php setHtmlNav($menu_list, 'header', $header_class . '__nav'); ?>
			</div>
		</div>
	</div>
</header>
<div class="<?php echo  $hamburger_class; ?>__button">
	<a href="javascript:void(0);" class="js-hbg__button">
		menu
		<ul>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</a>
</div>
<div class="<?php echo  $hamburger_class; ?>__modal js-hbg__modal">
	<div class="<?php echo  $hamburger_class; ?>__modal__wrap">
		<div class="<?php echo  $hamburger_class; ?>__modal__inner">
			<?php setHtmlNav($menu_list, 'modal', $hamburger_class . '__modal__nav'); ?>
			<?php setHtmlNav($menu_list, 'spsubnav', $hamburger_class . '__modal__subnav'); ?>
			<?php //setHtmlSearch();?>
			<a href="<?php echo $link_path; ?>/" class="<?php echo $hamburger_class; ?>__modal__logo">
				<?php setHtmlLogo('logo', 'p-logo', $site_title);  ?>
			</a>
			<?php setHtmlInfo(); ?>
			<?php setHtmlSns(); ?>
		</div>
	</div>
</div>
