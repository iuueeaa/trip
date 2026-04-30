<?php
$valueKey = "faq";
/*
* HTML
*/
function setHtmlFaq($body = array(), $class = "p-faq")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["faq"];
	setHtmlDl($body, $class, 'faq');
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	array(
		'dt' => $dummy_text,
		'dd' => $dummy_text . $dummy_text . $dummy_text,
	),
	array(
		'dt' => $dummy_text,
		'dd' => $dummy_text . $dummy_text . $dummy_text,
	),
	array(
		'dt' => $dummy_text,
		'dd' => $dummy_text . $dummy_text . $dummy_text,
	)
);


/*
* ACF設定用
*/

function setAcfFaq($name = "faq", $label = "FAQ", $layout = "table", $logic = array())
{
	$array = setAcfDtDdRepeater($name, 'Q', 'A', array(
		'button_label'   => $label . "を追加",
		'layout'         => $layout,
		'dt_rows'        => 4,
		'dd_rows'        => 4,
		'dt_width'       => 30,
		'dt_placeholder' => 'Qを入力',
		'dd_placeholder' => 'Aを入力',
	));
	$array['label'] = $label;
	$array = formatAcfLogic($array, $logic);
	return $array;
}
