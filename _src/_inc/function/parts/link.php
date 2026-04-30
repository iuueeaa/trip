<?php
$valueKey = "links";

/*
* HTML　
*/
function setHtmlLink($body = array(), $class = "p-button", $icon = "") {

	$rightIconSet = array("icon-arrow", "icon-arrow2", "icon-arrow3", "icon-arrow4", "icon-arrow5", "icon-link");
	$mode = (!empty($body["mode"])) ? $body["mode"] : "link";

	if ($mode == "file") {
		$link_title = $body['filetitile'];
		$link_url = $body['file'];
		$link_target = "_blank";
	} else {
		$link_url = "";
		if (!empty($body['link'])) {
			$link_title = (!empty($body['link']['title']["main"])) ? $body['link']['title']["main"] : $body['link']['title'];
			$link_url = $body['link']['url'];
			$link_target = (!empty($body["link"]["target"])) ? $body["link"]["target"] : "_self";
			if ($link_target == "_blank") {
				$icon = "icon-link";
			}
		}
	}

	$detected = detectLinkIcon($link_url);
	if ($detected !== '') {
		$icon = $detected;
	}

	if (in_array($icon, $rightIconSet)) {
		$class .= " is-reverse";
	}

	if (!empty($link_url)) :
?>
		<a href="<?php echo $link_url; ?>" target="<?php echo $link_target ?>" class="<?php echo $class; ?>">
			<?php if (!empty($icon)) {
				setHtmlSvg($icon);
			} ?>
			<?php if (!empty($link_title)) : ?>
				<span><?php echo $link_title ?></span>
			<?php endif; ?>
		</a>
	<?php endif;
}


function setHtmlLinkNav($nav = array(), $class = "p-button", $icon = "") {
	$link = array(
		"mode" => "link",
		"link" => $nav
	);
	setHtmlLink($link, $class, $icon);
}


function setHtmlLinks($body = array(), $class = "p-links") {
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat['link'];
	$outerclass = SetboxClass($class);
	$class = (is_array($class)) ? $class[0] : $class;
	?>
	<div class="<?php echo $outerclass; ?>">
		<div class="<?php echo $class; ?>__wrap">
			<?php
			foreach ($body as $link) {
				setHtmlLink($link, "p-button", "icon-arrow");
			}
			?>
		</div>
	</div>
<?php }


function setHtmlModalLink($body = array(), $class = "") {
	$link_title = addslashes($body['title']);
?>
	<button class="js-modal__btn <?php echo $class; ?>" data-modal="<?php echo $body['modal']; ?>">
		<span>
			<?php if (!empty($body['icon'])) {
				setHtmlSvg($body['icon']);
			} ?>
			<?php if (!empty($link_title)) : ?>
				<span><?php echo $link_title ?></span>
			<?php endif; ?>
		</span>
	</button>
<?php }



/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	array(
		'mode' => "link",
		'link' => array(
			'url' => $link_path,
			'title' => "VIEW DETAIL",
			'target' =>  "_self",
		),
		'file' => 'http://localhost:8001/wp-content/uploads/drag-logo-up.mp4',
		'filetitile' => "Download",
	),
);


/*
* ACF設定用
*/

function setAcfLink($name = "link", $label = "リンク", $layout = "table", $logic = array()) {
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			array(
				'type' => 'select',
				'label' => 'モード',
				'name' => 'mode',
				'width' => 20,
				'return_format' => 'value',
				'choices' => array('link' => 'リンク', 'file' => 'PDF/ファイル'),
			),
			array(
				'type' => 'link',
				'label' => 'リンク',
				'name' => 'link',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'link',
						),
					),
				)
			),
			array(
				'type' => 'file',
				'label' => 'ファイル',
				'name' => 'file',
				'return_format' => 'url',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'file',
						),
					),
				)
			),
			array(
				'type' => 'text',
				'label' => 'ボタンラベル',
				'name' => 'filetitle',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'file',
						),
					),
				)
			),
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}

function setAcfLinks($name = "links", $label = "リンク", $layout = "row", $logic = array()) {
	$array = array(
		'type' => "repeater",
		'label' => $label,
		'name' => $name,
		'layout' => 'row',
		'button_label' => $label . "を追加",
		'min' => 0,
		'sub_fields' =>  array(
			array(
				'type' => 'button_group',
				'label' => 'モード',
				'name' => 'mode',
				'width' => "20",
				'return_format' => 'value',
				'choices' => array('link' => 'リンク', 'file' => 'PDF/ファイル'),
			),
			array(
				'type' => 'link',
				'label' => 'リンク',
				'name' => 'link',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'link',
						),
					),
				)
			),
			array(
				'type' => 'file',
				'label' => 'ファイル',
				'name' => 'file',
				'return_format' => 'url',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'file',
						),
					),
				)
			),
			array(
				'type' => 'text',
				'label' => 'ボタンラベル',
				'name' => 'filetitle',
				"conditional_logic" => array(
					array(
						array(
							'field' => "",
							'operator' => '==',
							'value' => 'file',
						),
					),
				)
			),
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
