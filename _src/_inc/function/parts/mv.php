<?php
// $body['title']['sub']	= $parentBody['sub'];
function setHtmlMv($body, $class = "p-mv")
{
	$sectionId = "mv";
	$image = (!empty($body["mv"]["image"]))?$body["mv"]["image"]:$body["image"];
	$text = (!empty($body["mv"]["text"]))?$body["mv"]["text"]:$body["text"];;
?>
	<section id="<?php echo ucfirst($sectionId); ?>" class="<?php echo $class; ?>">
		<div class="p-mv__wrap">
			<div class="p-mv__pankuzu">
				<?php setHtmlPankuzu($body); ?>
			</div>
			<?php if (!empty(	$image)) : ?>
				<?php setHtmlBgImage(	$image, 'p-mv__image'); ?>
			<?php endif; ?>
			<div class="p-mv__inner">
				<?php setHtmlTitle($body['title'], 'p-mv__title', 'h1'); ?>
				<?php
				if (!empty(	$text )) {
					setHtmlText(	$text , 'p-mv__text');
				}
				?>
			</div>
		</div>
	</section>
<?php
}



/*
* ACF設定用
*/
function setAcfMv($name = "mv", $label = "メインビジュアル", $layout = "block", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		setAcfImage(),
		setAcfText(),
	), $layout, $logic);
}


function setAcfMvMin($name = "mv", $label = "メインビジュアル", $layout = "block", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		setAcfText(),
	), $layout, $logic);
}
