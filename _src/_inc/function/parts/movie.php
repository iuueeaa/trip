<?php
$valueKey = "video";

/*
* HTML　
*/
function setHtmlVideo($body, $class = "p-video", $attr = "autoplay loop muted playsinline", $breakpoint = "sp") {
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["video"];
	$has_sp = !empty($body['video_sp']);
	$pc_attr = $attr;
	if (!empty($body["image"])) {
		$pc_attr .= ' poster="' . $body["image"]["sizes"]["medium"] . '"';
	}
	if ($has_sp) {
		$sp_attr = $attr;
		if (!empty($body['video_sp']["image"])) {
			$sp_attr .= ' poster="' . $body['video_sp']["image"]["sizes"]["medium"] . '"';
		}
		$classes  = getResponsiveClasses($breakpoint);
		$pc_class = $classes['pc'];
		$sp_class = $classes['sp'];
	}
?>
	<div class="<?php echo $class; ?>">
		<?php if ($has_sp) : ?>
			<div class="<?php echo $pc_class; ?> js-video" data-src="<?php echo $body['video']; ?>">
				<div class="js-video__wrap">
					<video class="js-video__player" <?php echo $pc_attr; ?>></video>
				</div>
			</div>
			<div class="<?php echo $sp_class; ?> js-video" data-src="<?php echo $body['video_sp']['video']; ?>">
				<div class="js-video__wrap">
					<video class="js-video__player" <?php echo $sp_attr; ?>></video>
				</div>
			</div>
		<?php else : ?>
			<div class="js-video" data-src="<?php echo $body['video']; ?>">
				<div class="js-video__wrap">
					<video class="js-video__player" <?php echo $pc_attr; ?>></video>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php }


/*
* valueでの形
*/
$valueFormat[$valueKey] = setValueVideo(
	$local_path . '/assets/files/dummy.mp4',
	$image_path . '_dummy/pic-dummy.webp'
);

/*
* ACF設定用
*/
function setAcfVideo($name = "video", $label = "動画", $layout = "rows", $logic = array()) {
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			array(
				'type' => 'file',
				'label' => '動画ファイル',
				'name' => 'video',
				"return_format" => "url",
			),
			array(
				'type' => 'image',
				'label' => 'カバー画像',
				'name' => 'image',
			),
			array(
				'type' => 'group',
				'label' => 'SP動画',
				'name' => 'video_sp',
				'layout' => 'rows',
				'sub_fields' => array(
					array(
						'type' => 'file',
						'label' => '動画ファイル',
						'name' => 'video',
						'return_format' => 'url',
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
