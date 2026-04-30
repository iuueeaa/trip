<?php
$valueKey = "imagebox";

/*
* HTML
*/
function setHtmlImageBox($body = array(), $class = "p-imagebox")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["imagebox"];
	$hasLink = !empty($body['link']['link']['url']) || !empty($body['link']['file']);
	if ($hasLink) {
		['url' => $url, 'target' => $target] = SetBoxLink($body['link']);
	}
?>
	<div class="<?php echo $class; ?>">
		<?php if ($hasLink) : ?>
			<a href="<?php echo $url; ?>" target="<?php echo $target; ?>" class="<?php echo $class; ?>__link">
		<?php endif; ?>
		<?php if (!empty($body['image'])) : ?>
			<?php setHtmlBgImage($body['image'], $class . '__image'); ?>
		<?php endif; ?>
		<?php if (!empty($body['title']['main']) || !empty($body['text'])) : ?>
			<div class="<?php echo $class; ?>__body">
				<?php if (!empty($body['title'])) : ?>
					<?php setHtmlTitle($body['title'], $class . '__title', 'p'); ?>
				<?php endif; ?>
				<?php if (!empty($body['text'])) : ?>
					<?php setHtmlText($body['text'], $class . '__text'); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($hasLink) : ?>
			</a>
		<?php endif; ?>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', '画像タイトル'),
	'title' => setValueTitle('見出しが入ります'),
	'text'  => 'この文章はダミーです。',
	'link'  => setValueLink('/', 'VIEW MORE'),
);


/*
* ACF設定用
*/
function setAcfImageBox($name = "imagebox", $label = "イメージボックス", $layout = "block", $logic = array())
{
	$array = array(
		'type'       => "group",
		'label'      => $label,
		'name'       => $name,
		'layout'     => $layout,
		'sub_fields' => array(
			setAcfImage('image', '画像'),
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText('text', 'テキスト', 2),
			setAcfLink('link', 'リンク', 'table'),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
