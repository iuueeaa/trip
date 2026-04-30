<?php
$valueKey = "body";
/*
* HTML　
*/

function setHtmlBody($body = array(), $class = "p-body") { ?>
	<div class="<?php echo $class; ?>">
		<div class="<?php echo $class; ?>__wrap">
			<?php
			$agendaid = 0;
			foreach ($body as $box) :
				$layout = (!empty($box["acf_fc_layout"])) ? $box["acf_fc_layout"] : $box["layout"];
				$func = "setHtml" .  ucfirst($layout);
				$boxclass = (!empty($box["boxsetting"]["class"])) ? array($box["boxsetting"]["class"]) : array();
				if ($layout == "title") {
					$boxclass[] = $box["title"]["h"];
				}

			?>
				<div class="<?php echo $class . "__box " . $layout . " " . implode(" ", $boxclass); ?>">
					<?php
					if ($layout == "title") :
						$id = "";
						$h = $box["title"]["h"];
						if ($h == "h2") {
							$id = "contents_" . $agendaid;
							$agendaid++;
						}
						setHtmlTitle($box["title"], $h, $h, $id);
					elseif ($layout == "link") :
						echo '<div class="p-links">';
						$func($box[$layout]);
						echo '</div>';
					else :
						// text, image, youtube, movie, splide, gallery, links, pdf,profile,interview,imagetext,bnr, faq,ppcontact,timetable,linktable,dl,award,point,ceoprofile,relate
						$func($box[$layout]);
					endif;
					?>
				</div>
			<?php
			endforeach;
			?>
		</div>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array();


/*
* ACF設定用
*/
// for term and Privacy policy
function setAcfBody($name = "body", $label = "規約系", $layout = "block", $logic = array()) {
	$array = array(
		'type' => "flexible_content",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'min' => 0,
		'sub_fields' => array(
			formatFlexAcf('title', '見出し', setAcfTitle('title', '', array('main', 'sub', 'h'), 'table')),
			formatFlexAcf('text', '本文', setAcfText('text', '')),
			formatFlexAcf('li', '箇条書き', setAcfLi('li', '', 'table')),
			formatFlexAcf('table3', '表組', setAcfTable3("table3", ""),),
			formatFlexAcf('ppcontact', 'お問い合わせ先', setAcfPpcontact('ppcontact', '')),
			formatFlexAcf('sign', '署名', setAcfSign('sign', '')),
		),
		'button_label' => "コンテンツを追加",
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}

// for topics and news
function setAcfBody2($name = "body", $label = "投稿", $layout = "block", $logic = array()) {
	$array = array(
		'type' => "flexible_content",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'min' => 0,
		'sub_fields' => array(
			formatFlexAcf('title', '見出し', setAcfTitle('title', '', array('main', 'sub', 'h'), 'table')),
			formatFlexAcf('text', '本文', setAcfText('text', '')),
			formatFlexAcf('li', '箇条書き', setAcfLi('li', '', 'table')),
			formatFlexAcf('link', 'リンク', setAcfLink()),
			formatFlexAcf('image', '画像', setAcfImage('image', '')),
			formatFlexAcf('youtube', 'Youtube', setAcfYoutube('youtube', '')),
			formatFlexAcf('map', 'Map', setAcfMap('map', '', 'rows')),
		),
		'button_label' => "コンテンツを追加",
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
