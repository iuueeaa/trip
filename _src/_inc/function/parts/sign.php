<?php
$valueKey = "sign";
/*
* HTML　
*/
function setHtmlSign($body, $class = array("p-sign"))
{
	$outerclass = SetboxClass($class);
	$class = (is_array($class)) ? $class[0] : $class;

?>
	<div class="<?php echo $outerclass ?>">
		<?php if (!empty($body['date'])) : ?>
			<p class="date"><span><?php echo $body['date'] ?></span></p>
		<?php endif; ?>
		<p class="people"><span><small><?php echo $body['title'] ?></small><?php echo $body['people'] ?></span></p>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'date' => "制定日：2022年11月12日<br>改訂日：2022年11月12日<br>",
	'title' => "代表取締役",
	'people' => "山田太郎",
);


/*
* ACF設定用
*/
function setAcfSign($name = "sign", $label = "署名", $layout = "rows", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		array(
			'type' => 'textarea',
			'label' => '日付',
			'name' => 'date',
			'rows' => 2,
		),
		array(
			'type' => 'textarea',
			'label' => '肩書き',
			'name' => 'title',
			'rows' => 1,
		),
		array(
			'type' => 'textarea',
			'label' => '代表者名',
			'name' => 'people',
			'rows' => 1,
		),
	), $layout, $logic);
}
