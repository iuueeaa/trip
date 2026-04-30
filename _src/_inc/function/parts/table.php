<?php
$valueKey = "table";

/*
* HTML　
*/
function setHtmlTable($body, $class = "p-table")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["table"];
?>
	<div class="<?php echo $class; ?>">
		<table>
			<?php if (!empty($body['header'])) : ?>
				<thead>
					<tr>
						<?php foreach ($body['header'] as $th) : ?>
							<th><span><?php echo $th['c']; ?></span></th>
						<?php endforeach; ?>
					</tr>
				</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach ($body['body'] as $tr) : ?>
					<tr>
						<?php if (!empty($tr['dt'])) : ?>
							<th><span><?php echo $tr['dt'] ?></span></th>
							<td><span><?php echo $tr['dd'] ?></span></td>
						<?php else : ?>
							<?php foreach ($tr as $td) : ?>
								<td><span><?php echo $td['c'] ?></span></td>
							<?php endforeach; ?>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php
}

/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'header' => array(
		array(
			'c' => 'この文章はダミーです。',
		),
		array(
			'c' => 'この文章はダミーです。',
		),
	),
	'body' => array(
		array(
			array(
				'c' => $dummy_text,
			),
			array(
				'c' => $dummy_text,
			),
		),
		array(
			array(
				'c' => $dummy_text,
			),
			array(
				'c' => $dummy_text,
			),
		),
	),
);

/*
* ACF設定用
*/

// ACF指定のtableの形
function setAcfTable($name = "table", $label = "表組", $logic = array())
{
	$array = array(
		'type' => "table",
		'label' => $label,
		'name' => $name,
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}

// htmlを直接入力
function setAcfTable2($name = "table2", $label = "表(HTML)", $rows = 10, $logic = array())
{
	$array = array(
		'type' => "textarea",
		'label' => $label,
		'name' => $name,
		'rows' => $rows,
		'new_lines' => '',
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}

// th,tdだけのシンプル表組
function setAcfTable3($name = "table3", $label = "表(見出し・本文)", $layout = "table", $logic = array())
{
	$array = array(
		'type' => "repeater",
		'label' => $label,
		'name' => $name,
		'layout' => 'table',
		'button_label' => "行を追加",
		'sub_fields' =>  array(
			array(
				'type' => 'textarea',
				'label' => '',
				'name' => 'th',
				'rows' => 4,
				'width' => 30,
				'placeholder' => '見出しを入力'
			),
			array(
				'type' => 'textarea',
				'label' => '',
				'name' => 'td',
				'rows' => 4,
				'placeholder' => '内容を入力'
			),
		),
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
