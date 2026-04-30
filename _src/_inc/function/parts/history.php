<?php
$valueKey = "history";

/*
* HTML
*/
function setHtmlHistory($body = array(), $class = "p-history")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["history"];
?>
	<div class="<?php echo $class; ?>">
		<ul>
			<?php foreach ($body as $value) : ?>
				<li>
					<div class="year"><span><?php echo $value["year"] ?><span>年</span></span></div>
					<div class="list">
						<?php foreach ($value["list"] as $list) : ?>
							<dl>
								<dt><span><?php echo $list["month"] ?><span>月</span></span></dt>
								<dd><span><span><?php echo $list["title"] ?></span><?php echo $list["text"] ?></span></dd>
							</dl>
						<?php endforeach; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	array(
		'year' => '2023',
		'list' => array(
			array(
				'month' => '7',
				'title' => 'この文章はダミーです。',
				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
			),
			array(
				'month' => '7',
				'title' => 'この文章はダミーです。',
				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
			),
		),
	),
	array(
		'year' => '2022',
		'list' => array(
			array(
				'month' => '7',
				'title' => 'この文章はダミーです。',
				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
			),
			array(
				'month' => '7',
				'title' => 'この文章はダミーです。',
				'text' => 'この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。この文章はダミーです。文字サイズと量・字間・行間等を確認のために入れており、ここまでが50文字です。',
			),
		),
	),
);

/*
* ACF設定用
*/

function setAcfHistory($name = "history", $label = "沿革", $layout = "table", $logic = array())
{
	$array = array(
		'type' => "repeater",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'button_label' => $label . "を追加",
		'min' => 1,
		'sub_fields' =>  array(
			array(
				'type' => 'textarea',
				'label' => '年',
				'name' => 'year',
				'rows' => 1,
				'width' => 20,
				'placeholder' => "年を入力"
			),
			array(
				'type' => "repeater",
				'label' => '内容',
				'name' => "list",
				'layout' => 'row',
				'button_label' => "内容を追加",
				'min' => 1,
				'sub_fields' =>  array(
					array(
						'type' => 'textarea',
						'label' => '月',
						'name' => 'month',
						'rows' => 1,
						'width' => 20,
						'placeholder' => "月を入力"
					),
					array(
						'type' => 'textarea',
						'label' => '内容',
						'name' => 'title',
						'rows' => 2,
						'placeholder' => "内容を入力"
					),
					array(
						'type' => 'textarea',
						'label' => '注釈',
						'name' => 'text',
						'rows' => 2,
						'placeholder' => "注釈を入力"
					),
				)
			)
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
