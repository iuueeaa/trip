<?php
$valueKey = "chart";

/*
* HTML
* Chart.js を使用。<canvas class="js-chart" data-chart='JSON'> を出力。
* JS側: document.querySelectorAll('.js-chart') で取得し new Chart(el, JSON.parse(el.dataset.chart)) で初期化。
*/
function setHtmlChart($body = array(), $class = "p-chart")
{
	global $valueFormat;
	$body = (!empty($body)) ? $body : $valueFormat["chart"];

	$list   = $body['list'] ?? [];
	$labels = array_column($list, 'label');
	$values = array_map(function ($item) { return (float)($item['value'] ?? 0); }, $list);
	$colors = array_map(function ($item) {
		return !empty($item['color']) ? $item['color'] : 'rgb(var(--Key1-rgb, 51,102,204))';
	}, $list);

	$config = array(
		'type' => $body['type'] ?? 'bar',
		'data' => array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label'           => $body['unit'] ?? '',
					'data'            => $values,
					'backgroundColor' => $colors,
					'borderColor'     => $colors,
					'borderWidth'     => 1,
				),
			),
		),
		'options' => array(
			'responsive' => true,
			'plugins' => array(
				'title' => array(
					'display' => !empty($body['title']),
					'text'    => $body['title'] ?? '',
				),
				'legend' => array(
					'display' => false,
				),
			),
			'scales' => array(
				'y' => array(
					'title' => array(
						'display' => !empty($body['unit']),
						'text'    => $body['unit'] ?? '',
					),
				),
			),
		),
	);
?>
	<div class="<?php echo $class; ?>">
		<canvas class="js-chart" data-chart='<?php echo htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8'); ?>'></canvas>
	</div>
<?php
}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'type'  => 'bar',
	'title' => '売上推移',
	'unit'  => '百万円',
	'list'  => array(
		array('label' => '2020', 'value' => '4254', 'color' => ''),
		array('label' => '2021', 'value' => '4474', 'color' => ''),
		array('label' => '2022', 'value' => '4909', 'color' => ''),
		array('label' => '2023', 'value' => '5340', 'color' => ''),
	),
);


/*
* ACF設定用
*/
function setAcfChart($name = "chart", $label = "グラフ", $layout = "block", $logic = array())
{
	return buildAcfGroup($name, $label, array(
		array(
			'type'    => 'select',
			'label'   => 'グラフ種別',
			'name'    => 'type',
			'choices' => array(
				'bar'               => '棒グラフ（縦）',
				'horizontalBar'     => '棒グラフ（横）',
				'line'              => '折れ線グラフ',
				'pie'               => '円グラフ',
			),
			'default_value' => 'bar',
			'return_format' => 'value',
		),
		array(
			'type'        => 'text',
			'label'       => 'タイトル',
			'name'        => 'title',
			'placeholder' => 'グラフのタイトルを入力',
		),
		array(
			'type'        => 'text',
			'label'       => '単位',
			'name'        => 'unit',
			'placeholder' => '例: 百万円、%',
		),
		array(
			'type'         => 'repeater',
			'label'        => 'データ',
			'name'         => 'list',
			'layout'       => 'table',
			'button_label' => '項目を追加',
			'min'          => 1,
			'sub_fields'   => array(
				array(
					'type'        => 'text',
					'label'       => 'ラベル',
					'name'        => 'label',
					'width'       => 40,
					'placeholder' => '例: 2023年',
				),
				array(
					'type'        => 'number',
					'label'       => '値',
					'name'        => 'value',
					'width'       => 30,
				),
				array(
					'type'        => 'text',
					'label'       => '色 (省略可)',
					'name'        => 'color',
					'width'       => 30,
					'placeholder' => '例: rgb(255,99,132)',
				),
			),
		),
	), $layout, $logic);
}
