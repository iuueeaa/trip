<?php
$valueKey = "map";

/*
* HTML　
*/
function setHtmlMap($body, $class = "p-map ") {
?>
	<?php if ($body["mode"] == "api") : ?>
		<div class="<?php echo $class; ?> is-api">
			<div class="js-map" data-lat="<?php echo $body["lat"]; ?>" data-lng="<?php echo $body["lng"]; ?>" data-pin="<?php echo $body["pin"]; ?>">
				<div class="js-map__wrap"></div>
			</div>
		</div>
	<?php elseif ($body["mode"] == "iframe") : ?>
		<?php if (!empty($body["link"])) : ?>
			<a href="<?php echo $body["link"]; ?>" target="_blank" class="<?php echo $class; ?> is-ovh">
				<?php echo $body["iframe"] ?>
			</a>
		<?php else : ?>
			<div class="<?php echo $class; ?>">
				<?php echo $body["iframe"] ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php }


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'mode' => 'iframe', //iframe,iframe_link,api
	'lat' => "35.32697306801492",
	'lng' => "139.43685625823144",
	'pin' => $image_path . "common/pin.webp",
	'iframe' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3255.1619227589626!2d139.43445795155552!3d35.32680035683735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60184dfa76e1ae43%3A0xa5c57aa86b013e27!2z44CSMjUzLTAwMzEg56We5aWI5bed55yM6IyF44O25bSO5biC5a-M5aOr6KaL55S677yR77yW4oiS77yT77yZ!5e0!3m2!1sja!2sjp!4v1660287810047!5m2!1sja!2sjp" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
	'link' => 'https://google.map/xxx',
);


/*
* ACF設定用
*/

function setAcfMap($name = "map", $label = "マップ", $layout = "block", $logic = array()) {
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			array(
				'type' => 'button_group',
				'label' => 'モード',
				'name' => 'mode',
				'return_format' => 'value',
				'choices' => array('iframe' => '埋め込み', 'api' => 'API'),
			),
			array(
				'type' => 'textarea',
				'label' => '緯度',
				'name' => 'lat',
				'rows' => 1,
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'api',
						),
					),
				)
			),
			array(
				'type' => 'textarea',
				'label' => '経度',
				'name' => 'lng',
				'rows' => 1,
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'api',
						),
					),
				)
			),
			array(
				'type' => 'image',
				'label' => 'ピン',
				'name' => 'pin',
				'return_format' => 'url',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'api',
						),
					),
				)
			),
			array(
				'type' => 'textarea',
				'label' => '埋め込みタグ',
				'name' => 'iframe',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '!=',
							'value' => 'api',
						),
					),
				)

			),
			array(
				'type' => 'text',
				'label' => 'マップのリンク',
				'name' => 'link',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'iframe',
						),
					),
				)

			),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
