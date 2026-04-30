<?php
$valueKey = "box";
/*
* HTML　
*/
function setHtmlBox($body = array(), $class = "p-box") { ?>
	<?php
	$boxClass = $class;
	$boxValue = $body;
	['url' => $boxUrl, 'target' => $boxTarget] = SetBoxLink($boxValue['link']);
	?>
	<div class="<?php echo $boxClass; ?>">
		<div class="<?php echo $boxClass; ?>__wrap">
			<?php
			if (!empty($boxValue['image'])) : ?>
				<a href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>" class="<?php echo $boxClass; ?>__imageBox imageBox">
					<?php setHtmlBgImage($boxValue['image'], 'image'); ?>
				</a>
			<?php endif; ?>
			<div class="<?php echo $boxClass; ?>__textBox textBox">
				<div class="info">
					<?php setHtmlText($boxValue['date'], 'p-date'); ?>
					<?php
					if (!empty($boxValue['taxonomy']['category'])) {
						setHtmlTaxonomy($boxValue['taxonomy']['category'], 'p-category', false, true);
					}
					?>
				</div>
				<a class="titleBox" href="<?php echo $boxUrl; ?>" target="<?php echo $boxTarget; ?>">
					<?php
					if (!empty($boxValue['title'])) {
						setHtmlTitle($boxValue['title'], 'p-title__sec', 'h2');
					}
					?>
					<?php
					if (!empty($boxValue['lead'])) {
						setHtmlText($boxValue['lead'], 'p-lead');
					}
					?>
					<?php
					if (!empty($boxValue['text'])) {
						setHtmlText($boxValue['text'], 'p-text');
					}
					?>
				</a>
				<?php
				if (!empty($boxValue['taxonomy']['tag'])) {
					setHtmlTaxonomy($boxValue['taxonomy']['tag'], 'p-tag', false, true);
				}
				?>
				<?php
				if (!empty($boxValue['link'])) {
					setHtmlLink($boxValue['link'], 'p-button is-color__sub', 'icon-arrow');
				}
				?>
			</div>
		</div>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'image' => setValueImage($image_path . '_dummy/pic-dummy.webp', "画像タイトル"),
	'date' => date($date_format),
	'taxonomy' => array(
		'category' => array(
			(object) ['name' => 'カテゴリ1', 'slug' => 'category1', 'taxonomy' => 'category'],
		),
		'tag' => array(
			(object) ['name' => 'カテゴリ1', 'slug' => 'tag1', 'taxonomy' => 'tag'],
			(object) ['name' => 'カテゴリ2', 'slug' => 'tag2', 'taxonomy' => 'tag'],
			(object) ['name' => 'カテゴリ3', 'slug' => 'tag3', 'taxonomy' => 'tag'],
		),
	),
	'title' => setValueTitle('見出しが入ります', "Headline Title"),
	'lead' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
	'link' => array(
		'mode' => 'link',
		'link' => array(
			'title' => 'VIEW MORE',
			'url' => $link_path . "/",
			'target' => '',
		),
		'file' => false, //pdfなどときはこちら(必須じゃない)
	),
);


/*
* ACF設定用
*/

function setAcfBox($name = "box", $label = "BOX", $layout = "block", $logic = array()) {
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			setAcfImage(),
			setAcfTitle(),
			setAcfText('lead', 'リード文'),
			setAcfText(),
			setAcfLink(),
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
// $acfFormat[$valueKey] = setAcfBox();

// $flexValueFormat[$valueKey] = array(
// 	'layout' => $valueKey,
// 	$valueKey =>  $valueFormat[$valueKey]
// );

// $flexAcfFormat[$valueKey] = formatFlexAcf($valueKey, 'FAQ', setAcfBox(), array('class', 'widthForText', 'textAlign'));
