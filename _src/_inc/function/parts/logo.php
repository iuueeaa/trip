<?php
$valueKey = "logo";
/*
* HTML　
*/
function setHtmlLogo($icon = "logo", $class = "p-logo", $title = '')
{
	if (empty($title)) {
		global $site_title;
		$title = $site_title;
	}
	if (empty($icon)) {
		$icon = 'logo';
	}
?>
<span class="<?php echo $class; ?>">
	<?php setHtmlSvg($icon); ?>
	<span><?php echo $title; ?></span>
</span>
<?php }



/*
* valueでの形
*/
$valueFormat[$valueKey] = "typo";


/*
* ACF設定用
*/
function setAcfLogo($name = "logo", $label = "ロゴ", $layout = "block", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		array(
			'type' => 'select',
			'label' => '',
			'name' => 'logo',
			'return_format' => 'value',
			'width' => 20,
			'choices' => array(
				'' => 'メイン',
				'typo' => '文字',
				'icon' => 'マーク',
				'yoko' => '横組',
			),
		),
	), $layout, $logic);
}
