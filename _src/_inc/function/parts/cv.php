<?php
$valueKey = "cv";

/*
* HTML　
*/
function setHtmlCv($body, $class = "p-cv")
{
	$titleclass = ((preg_match("/[ぁ-ん]+|[ァ-ヴー]+|[一-龠]/u", $body["title"]))) ? "__jp" : "";
	$imageclass = (!empty($body["image"])) ? "is-image" : "";
?>
	<a href="<?php echo $body["link"]['url']; ?>" target="<?php echo $body["link"]['target']; ?>" class="<?php echo $class . " " . $imageclass; ?>">
		<span class="p-cv__wrap">
			<span class="p-cv__inner">
				<span class="p-cv__left">
					<?php if (!empty($body["image"])) : ?>
						<div class="p-cv__image"><span class="js-lazy_bgi" data-bgi="<?php echo $body['image']['sizes']['medium']; ?>"></span></div>
					<?php else : ?>
						<span class="p-cv__svg"><?php setHtmlSvg($body["icon"]); ?></span>
					<?php endif; ?>
				</span>
				<span class="p-cv__right">
					<span class="p-cv__title<?php echo $titleclass; ?>"><?php echo $body["title"]; ?></span>
					<span class="p-cv__subtitle"><?php echo $body["text"]; ?></span>
				</span>
			</span>
		</span>
	</a>
<?php }




/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	"mode" => "link",
	"link" => array(
		"url" => "",
		"target" => "",
	),
	"icon" => "",
	"image" => "",
	"text" => ""
);

/*
* ACF設定用
*/

function setAcfCv($name = "cv", $label = "CVボタン", $layout = "table", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		array(
			'type' => 'link',
			'label' => '',
			'name' => 'link',
		),
		array(
			'type' => 'textarea',
			'label' => '',
			'name' => 'icon',
			'row' => 1,
			'placeholder' => "パスを入力"
		),
		array(
			'type' => 'textarea',
			'label' => '',
			'name' => 'title',
			'row' => 1,
			'placeholder' => "見出しを入力"
		),
		array(
			'type' => 'textarea',
			'label' => '',
			'name' => 'text',
			'placeholder' => "サブ見出しを入力"
		),
	), $layout, $logic);
}
