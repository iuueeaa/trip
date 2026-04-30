<?php
$valueKey = "xxxxxxxx";
/*
* HTML　
*/
function setHtmlxxxxxxxx($body = array(), $class = "")
{
	global $local_path;
?>
	<div class="<?php echo $class; ?>">
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
function setAcfXxxxxxx($name = "", $label = "", $layout = "block", $logic = array())
{
	$array = array(
		'type' => "",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'button_label' => $label . "を追加",

	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
