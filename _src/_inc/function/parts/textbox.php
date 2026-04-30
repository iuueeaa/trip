<?php
$valueKey = "textBox";

/*
* HTML
*/
function setHtmlTextBox($body, $class = "p-textbox")
{
	if (empty($body)) return;
	?>
	<div class="<?php echo $class; ?>">
		<?php if (!empty($body['title'])) : ?>
			<?php setHtmlTitle($body['title'], $class . '__title', 'h3'); ?>
		<?php endif; ?>
		<?php if (!empty($body['text'])) : ?>
			<?php setHtmlText($body['text'], $class . '__text'); ?>
		<?php endif; ?>
		<?php if (!empty($body['link'])) : ?>
			<?php setHtmlLink($body['link'], $class . '__link'); ?>
		<?php endif; ?>
	</div>
	<?php
}

function setHtmlTextBoxList($list, $class = "p-textbox-list", $type = "plain")
{
	if (empty($list) || !is_array($list)) return;
	?>
	<ul class="<?php echo $class; ?>__ul">
		<?php foreach ($list as $key => $value) : ?>
			<li class="<?php echo $class; ?>__li">
				<?php if ($type === 'number') : ?>
					<div class="<?php echo $class; ?>__number"><?php echo sprintf('%03d', $key + 1); ?></div>
				<?php elseif ($type === 'icon' && !empty($value['icon'])) : ?>
					<div class="<?php echo $class; ?>__icon"><?php setHtmlSvg($value['icon']); ?></div>
				<?php endif; ?>
				<div class="<?php echo $class; ?>__textBox textBox">
					<?php setHtmlTextBox($value, $class . '__box'); ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'title' => setValueTitle('見出しが入ります'),
	'text'  => 'この文章はダミーです。',
	'link'  => setValueLink('/', 'VIEW MORE'),
);


/*
* ACF設定用
*/
function setAcfTextBox($name = "textBox", $label = "テキストボックス", $layout = "block", $logic = array())
{
	$array = array(
		'type'       => "group",
		'label'      => $label,
		'name'       => $name,
		'layout'     => $layout,
		'sub_fields' => array(
			setAcfTitle('title', '見出し', array('main', 'sub'), 'table'),
			setAcfText('text', 'テキスト', 'table'),
			setAcfLink('link', 'リンク', 'table'),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
