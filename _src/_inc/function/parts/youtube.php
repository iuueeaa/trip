<?php
$valueKey = "youtube";

/*
* HTML　setHtmlYoutube(WP入力値, class, ソース設定値).
*/
$youtubeId = 0;
function setHtmlYoutube($body, $class = "p-youtube", $attr = 'data-controls="true" data-mute="true" data-loop="false" data-auto="false" data-ratio="56.25"', $breakpoint = "sp") {
	global $valueFormat, $youtubeId;
	$body = (!empty($body)) ? $body : $valueFormat["youtube"];
	$has_sp = !empty($body['youtube_sp']);
	$pc_attr = $attr;
	if (!empty($body["image"])) {
		$pc_attr .= ' data-cover="' . $body["image"]["sizes"]["medium"] . '"';
	}
	if ($has_sp) {
		$sp_attr = $attr;
		if (!empty($body['youtube_sp']["image"])) {
			$sp_attr .= ' data-cover="' . $body['youtube_sp']["image"]["sizes"]["medium"] . '"';
		}
		$classes  = getResponsiveClasses($breakpoint);
		$pc_class = $classes['pc'];
		$sp_class = $classes['sp'];
	}
?>
	<div class="<?php echo $class; ?>">
		<?php if ($has_sp) : ?>
			<div class="<?php echo $pc_class; ?> js-youtube" data-id="<?php echo $body['id']; ?>" <?php echo $pc_attr; ?>></div>
			<div class="<?php echo $sp_class; ?> js-youtube" data-id="<?php echo $body['youtube_sp']['id']; ?>" <?php echo $sp_attr; ?>></div>
		<?php else : ?>
			<div class="js-youtube" data-id="<?php echo $body['id']; ?>" <?php echo $pc_attr; ?>></div>
		<?php endif; ?>
	</div>
<?php
	$youtubeId++;
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = setValueYoutube(
	'aqa9h-nL-TA',
	$image_path . '_dummy/pic-dummy.webp'
);


/*
* ACF設定用
*/

function setAcfYoutube($name = "youtube", $label = "", $layout = "rows", $logic = array()) {
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			array(
				'type' => 'text',
				'label' => 'ID',
				'name' => 'id',
				'prepend' => 'https://www.youtube.com/watch?v='
			),
			array(
				'type' => 'image',
				'label' => 'カバー画像',
				'name' => 'image',
			),
			array(
				'type' => 'group',
				'label' => 'SP YouTube',
				'name' => 'youtube_sp',
				'layout' => 'rows',
				'sub_fields' => array(
					array(
						'type' => 'text',
						'label' => 'ID',
						'name' => 'id',
						'prepend' => 'https://www.youtube.com/watch?v=',
					),
					array(
						'type' => 'image',
						'label' => 'カバー画像',
						'name' => 'image',
					),
				),
			),
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
