<?php
$valueKey = "recruit";
/*
* HTML　
*/

function setHtmlRecruitTable($body, $class = "p-recruit__table", $dlDisplay = false)
{
?>
	<div class="<?php echo $class; ?>">
		<table>
			<tbody>
				<tr>
					<th><span>職 種</span></th>
					<td><span><?php echo $body['fix']['name']; ?></span></td>
				</tr>
				<tr>
					<th><span>雇用形態</span></th>
					<td><span><?php echo $body['fix']['type']; ?></span></td>
				</tr>
				<tr>
					<th><span>業務内容</span></th>
					<td><span><?php echo $body['fix']['content']; ?></span></td>
				</tr>
				<tr>
					<th><span>給 与</span></th>
					<td><span><?php echo $body['fix']['salary']; ?></span></td>
				</tr>
				<tr>
					<th><span>勤務地</span></th>
					<td><span><?php echo $body['fix']['area']; ?></span></td>
				</tr>
				<?php if ($dlDisplay) : ?>
					<?php foreach ($body["dl"] as $dl) : ?>
						<tr>
							<th><span><?php echo $dl['dt']; ?></span></th>
							<td><span><?php echo $dl['dd']; ?></span></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

<?php

}


/*
* valueでの形
*/
$valueFormat[$valueKey] = array(
	'fix' => array(
		'name' => 'マーケティング・セールス',
		'type' => '業務委託／契約社員／正社員',
		'content' => '- R&Dのお客様の支援<br />- 当社各種サービスのマーケティングセールス全般',
		'salary' => '入社時の想定年収500万円～1500万円 （スキル・経験に応じて優遇、年俸制）<br />給与改定年2回（4月、10月)',
		'area' => 'フルリモート<br />（お客様訪問時に出張あり）',
	),
	'dl' => array(
		array(
			'dt' => '福利厚生',
			'dd' => '交通費全額支給、社会保険完備（雇用・労災・健康・厚生年金）',
		),
		array(
			'dt' => '求める人材',
			'dd' => '- 自らマーケティングの施策を考案し実行できる方<br />- お客様へセールスができる方',
		),
		array(
			'dt' => 'その他',
			'dd' => '成果報酬型の業務委託も可能です。',
		),
	),
);

/*
* ACF設定用
*/

function setAcfRecruit($name = "recruit", $label = "採用情報", $layout = "block", $logic = array())
{
	$array = array(
		'type' => "group",
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'sub_fields' =>  array(
			array(
				'type' => "group",
				'label' => '採用情報：固定の項目',
				'name' => 'fix',
				'layout' => 'rows',
				'sub_fields' =>  array(
					setAcfText('name', '職種名', 2),
					setAcfText('type', '雇用形態', 2),
					setAcfText('content', '仕事内容'),
					setAcfText('salary', '給料'),
					setAcfText('area', '勤務地'),
				)
			),
			setAcfDl('dl', '追加項目'),
		)
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
