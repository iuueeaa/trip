<?php
$valueKey = "title";

/*
* HTML　
*/
function setHtmlTitle($body, $class = "p-title__sec", $attr = "h2", $id = "")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat['title'];
	$main = (is_array($body)) ? $body['main'] : $body;
	if (!empty($main) || !empty($body['sub'])) :
?>
		<div id="<?php echo $id; ?>" class="<?php echo $class; ?> title">
			<<?php echo $attr; ?> class="title__wrap">
				<?php if (is_array($body) && !empty($body['icon'])) : ?>
					<span class="title__icon"><?php setHtmlSvg($body['icon']); ?></span>
				<?php endif; ?>
				<?php if (!empty($main)) : ?>
					<span class="title__main"><?php echo $main; ?></span>
				<?php endif; ?>
				<?php if (!empty($body['sub'])) : ?>
					<span class="title__sub"><?php echo $body['sub']; ?></span>
				<?php endif; ?>
			</<?php echo $attr; ?>>
		</div>
<?php
	endif;
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'main' => 'メインタイトル',
	'sub' => 'subtitle'
);

/*
* ACF設定用
*/
function setAcfTitle($name = "title", $label = "見出し", $fields = array('main', 'sub', 'h'), $layout = "block", $logic = array())
{
	$subFields = array();
	$orderedFields = array('main', 'sub', 'h');

	foreach ($orderedFields as $fieldKey) {
		if (!in_array($fieldKey, $fields, true)) {
			continue;
		}

		if ($fieldKey === 'main') {
			$isOnlyMain = $fields === array('main');
			$subFields[] = array(
				'type' => 'textarea',
				'label' => $isOnlyMain ? '' : 'メイン',
				'name' => 'main',
				'rows' => 2,
				'placeholder' => "メイン・日本語タイトルを入力"
			);
			continue;
		}

		if ($fieldKey === 'sub') {
			$subFields[] = array(
				'type' => 'textarea',
				'label' => 'サブ',
				'name' => 'sub',
				'rows' => 2,
				'placeholder' => "サブ・英語タイトルを入力"
			);
			continue;
		}

		$subFields[] = array(
			'type' => 'select',
			'label' => "h",
			'name' => 'h',
			'return_format' => 'value',
			'width' => 20,
			'choices' => array(
				"h2" =>	"h2",
				"h3" =>	"h3",
				"h4" =>	"h4",
			),
		);
	}

	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  $subFields
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
