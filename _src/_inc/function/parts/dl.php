<?php
$valueKey = "dl";

/*
* HTML　
*/
function setHtmlDl($body = array(), $class = "p-dl", $type = "dl")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["dl"];
	$isFaq = ($type === 'faq' || $type === true);
	$js_dl = ($isFaq) ? 'js-accordion' : '';
	$js_dt = ($isFaq) ? 'js-accordion__head' : '';
	$js_dd = ($isFaq) ? 'js-accordion__body' : ''; ?>
	<div class="<?php echo $class; ?>">
		<?php foreach ($body as $dl) { ?>
			<dl class="<?php echo $class . '__dl ' . $js_dl; ?>">
				<dt class="<?php echo $class . '__dt ' . $js_dt; ?>"><span><?php echo $dl['dt']; ?></span></dt>
				<dd class="<?php echo $class . '__dd ' . $js_dd; ?>">
					<span><?php echo $dl['dd']; ?></span>
				</dd>
			</dl>
		<?php } ?>
	</div>
<?php }


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	array(
		'dt' => "データの見出し",
		'dd' => $dummy_text . $dummy_text,
	),
	array(
		'dt' => "データの見出し",
		'dd' => $dummy_text . $dummy_text,
	),
	array(
		'dt' => "データの見出し",
		'dd' => $dummy_text . $dummy_text,
	)
);


/*
* ACF設定用
*/
function setAcfDl($name = "dl", $label = "データリスト", $layout = "table", $logic = array())
{
	$array = setAcfDtDdRepeater($name, '', '', array(
		'button_label'   => $label . "を追加",
		'layout'         => $layout,
		'dt_rows'        => 4,
		'dd_rows'        => 4,
		'dt_width'       => 30,
		'dt_placeholder' => '見出しを入力',
		'dd_placeholder' => '内容を入力',
	));
	$array['label'] = $label;
	$array = formatAcfLogic($array, $logic);
	return $array;
}
