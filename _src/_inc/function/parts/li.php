<?php
$valueKey = "li";

/*
* HTML　
*/

function setHtmlUl($body = array(), $class = "p-ul")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["li"];
?>
	<ul class="<?php echo $class; ?>">
		<?php foreach ($body as $li) : ?>
			<li><span><?php echo $li['text']; ?></span></li>
		<?php endforeach; ?>
	</ul>
<?php }

function setHtmlOl($body = array(), $class = "p-ol")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["li"];
?>
	<ol class="<?php echo $class; ?>">
		<?php foreach ($body as $li) : ?>
			<li><span><?php echo $li['text']; ?></span></li>
		<?php endforeach; ?>
	</ol>
<?php }

function setHtmlCap($body = array(), $class = "p-attention")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["li"];
?>
	<ul class="<?php echo $class; ?>">
		<?php foreach ($body as $li) : ?>
			<li><span><?php echo $li['text']; ?></span></li>
		<?php endforeach; ?>
	</ul>
<?php }

function setHtmlLi($body = array(), $class = array())
{
	// 自由レイアウト用のfunction
	$func = "setHtml" .	strtoupper($body["type"]);
	$func($body["li"]);
}



/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	array('text' => $dummy_text . $dummy_text . $dummy_text,),
	array('text' => $dummy_text . $dummy_text . $dummy_text,),
	array('text' => $dummy_text . $dummy_text . $dummy_text,),
);


/*
* ACF設定用
*/

function setAcfLi($name = "li", $label = "箇条書き・注意書き", $layout = "table", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		array(
			'type' => 'select',
			'label' => "",
			'name' => 'type',
			'return_format' => 'value',
			'width' => 20,
			'choices' => array(
				'ul' => '箇条書き(・)',
				'ol' => '箇条書き(1.)',
				'cap' => '注意書き(※)',
			),
		),
		array(
			'type' => "repeater",
			'label' => '',
			'name' => 'li',
			'layout' => 'table',
			"min" => 1,
			'sub_fields' =>  array(
				array(
					'type' => 'textarea',
					'label' => '',
					'name' => 'text',
					'rows' => 3,
					'placeholder' => (!empty($label)) ? $label . 'を入力' : 'テキストを入力',
				),
			),
			'button_label' => (!empty($label)) ? $label .  "を追加" : '箇条書きを追加',
		)
	), $layout, $logic);
}


function setAcfCap($name = "li", $label = "注意書き", $layout = "table", $logic = array())
{
	$array = array(
		'type' => "repeater",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		"min" => 1,
		'sub_fields' =>  array(
			array(
				'type' => 'textarea',
				'label' => '',
				'name' => 'text',
				'rows' => 3,
				'placeholder' => (!empty($label)) ? $label . 'を入力' : 'テキストを入力',
			),
		),
		'button_label' => (!empty($label)) ? $label .  "を追加" : '注意書きを追加',
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
