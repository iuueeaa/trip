<?php
$valueKey = "text";

/*
* HTML　
*/
function setHtmlText($body = "", $class = "p-text", $attr = "p")
{
?>
	<<?php echo $attr; ?> class="<?php echo $class; ?>"><span><?php echo $body; ?></span></<?php echo $attr; ?>>
<?php }


/*
* valueでの形
*/
$valueFormat[$valueKey] = $dummy_text;


/*
* ACF設定用
*/

function setAcfText($name = "text", $label = "本文", $rows = 4, $logic = array())
{
	$array = array(
		'type' => "textarea",
		'label' => $label,
		'name' => $name,
		'rows' => $rows,
		'placeholder' => (!empty($label)) ? $label . 'を入力' : '本文を入力',
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
