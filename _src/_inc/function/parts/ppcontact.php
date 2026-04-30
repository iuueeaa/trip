<?php
$valueKey = "ppcontact";
/*
* HTML　
*/
function setHtmlPpcontact($body = array(), $class = "p-ppcontact")
{
	global  $client_name, $email;
	$body = (!empty($body)) ? $body : array(
		'layout' => 'ppcontact',
		'title' => "お問い合わせ先",
		'text' => $client_name . ' 広報担当<br><a class="p-link" href="mailto:' . $email . '"><span>' . $email . '</span></a>'
	);
?>
	<div class="<?php echo $class ?>">
		<dl>
			<dt><span><?php echo $body['title'] ?></span></dt>
			<dd><span><?php echo $body['text'] ?></span></dd>
		</dl>
	</div>
<?php
}



/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'title' => "お問い合わせ先",
	'text' => $client_name . ' 広報担当<br><a class="p-link" href="mailto:' . $email . '"><span>' . $email . '</span></a>'
);

/*
* ACF設定用
*/

function setAcfPpcontact($name = "ppcontact", $label = "お問い合わせ先", $layout = "rows", $logic = array())
{
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			setAcfText('title', '見出し', 1),
			setAcfText('text', '本文', 4),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
