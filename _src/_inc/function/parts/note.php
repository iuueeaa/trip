<?php
$valueKey = "note";

/*
* HTML
* type: 'note'(補足) / 'info'(情報) / 'warning'(注意) / 'caution'(重要)
* → <div class="p-note is-{type}"> でスタイルを切り替える
*/
function setHtmlNote($body = array(), $class = "p-note")
{
	global $valueFormat;
	$body  = (!empty($body)) ? $body : $valueFormat["note"];
	$type  = !empty($body['type']) ? $body['type'] : 'note';
?>
	<div class="<?php echo $class; ?> is-<?php echo $type; ?>">
		<?php if (!empty($body['title'])) : ?>
			<p class="<?php echo $class; ?>__title"><?php echo $body['title']; ?></p>
		<?php endif; ?>
		<?php if (!empty($body['text'])) : ?>
			<?php setHtmlText($body['text'], $class . '__text'); ?>
		<?php endif; ?>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'type'  => 'note',
	'title' => '',
	'text'  => 'この文章はダミーです。補足・注意書きを入力します。',
);


/*
* ACF設定用
*/
function setAcfNote($name = "note", $label = "注釈", $layout = "block", $logic = array())
{
	$array = array(
		'type'       => "group",
		'label'      => $label,
		'name'       => $name,
		'layout'     => $layout,
		'sub_fields' => array(
			array(
				'type'          => 'select',
				'label'         => '種別',
				'name'          => 'type',
				'choices'       => array(
					'note'    => '補足',
					'info'    => '情報',
					'warning' => '注意',
					'caution' => '重要',
				),
				'default_value' => 'note',
				'return_format' => 'value',
			),
			array(
				'type'        => 'text',
				'label'       => 'タイトル（省略可）',
				'name'        => 'title',
				'placeholder' => 'タイトルを入力',
			),
			setAcfText('text', 'テキスト', 3),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
