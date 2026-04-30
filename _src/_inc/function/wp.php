<?php
/*
*
* Wordpressの設定(ACF / Custom post)に関わるfunction
* _wp/function/acfの中身もこちらに移植する・・・？
* 　
*/

$flexAcfFormat = array();
$valueFormat = array();
$acfvalues = array();
$acf_format_array = array(
	'display' => array(
		'type' => 'true_false',
		'label' => '表示',
		'name' => 'display',
		'ui_on_text' => '表示',
		'ui_off_text' => '非表示',
	),
	'rl' => array(
		'type' => 'true_false',
		'label' => '画像位置',
		'name' => 'r',
		'ui_on_text' => '右',
		'ui_off_text' => '左',
	),
	'message' => array(
		'type' => 'message',
		'label' => '本文',
		'name' => '',
		'message' => '文中には下記のhtmlタグが使用できます<br>[リンク] &lt;a href=&quot;ここにURL&quot;&gt;表示する文字列&lt;/a&gt;'
	),
	'lang' =>	array(
		'type' => "group",
		'label' => '言語',
		'name' => 'lang',
		'layout' => 'table',
		'sub_fields' =>  array(
			array(
				'type' => 'text',
				'label' => '英語',
				'name' => 'en',
			),
			array(
				'type' => 'text',
				'label' => '日',
				'name' => 'jp',
			),
		),
	),
);

function setAcfPostList($name = "postList", $label = "投稿", $layout = "table", $logic = array()) {
	$array = array(
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'type' => "group",
		'sub_fields' =>  array(
			array(
				'type' => 'text',
				'label' => '投稿タイプ',
				'name' => 'cpt',
			),
			array(
				'type' => 'text',
				'label' => '件数',
				'name' => 'num',
				'append' => '件 / 1ページ',
			),
			array(
				'type' => 'true_false',
				'label' => 'ページャー',
				'name' => 'pager',
			),
		)

	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}

function setAcfSelectPostList($name = "selectPostList", $label = "関連投稿", $layout = "table", $posttype = "", $max = 3, $logic = array(), $taxonomy = []) {
	$array = array(
		'label' => $label,
		'name' => $name,
		'layout' => $layout,
		'type' => "group",
		'sub_fields' =>  array(
			'relationship' => array(
				'type' => 'relationship',
				'label' => '',
				'name' => 'list',
				'return_format' => 'id',
				'max' => $max,
				'filters' => array('search', 'taxonomy'),
			),
		)

	);
	if (!empty($posttype)) {
		$array['sub_fields']['relationship']['post_type'] = $posttype;
	}
	if (!empty($taxonomy)) {
		$taxFilters = formatAcfTaxonomyFilters($posttype, $taxonomy);
		if (!empty($taxFilters)) {
			$array['sub_fields']['relationship']['taxonomy'] = $taxFilters;
			$array['sub_fields']['relationship']['filters'] = array('search');
		}
	}
	$array = formatAcfLogic($array, $logic);
	return $array;
}

function addAcfValueArray($key, $title, $type, $field) {
	global $wpflg;
	$arr = array();
	if ($wpflg) {
		if ($type == "archive") {
			$arr = array(
				'name' => $key . '_archive',
				'title' => $title . '一覧',
				'field' => $field,
				'location' => array(
					array(
						// array(
						// 	'param' => 'admin_page',
						// 	'operator' => '==',
						// 	'value' => $key,
						// ),
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => $key . '_archive',
						),
					),
				),
			);
		} elseif ($type == "single") {
			$arr = array(
				'name' => $key . "_single",
				'title' => $title,
				'field' => $field,
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => $key,
						),
					),
				),
			);
		} elseif ($type == "page") {
			$page = get_page_by_path($key, OBJECT, 'page');

			// 2) 見つからない時のフォールバック：テンプレ名で判定
			//    例: 'top' → 'page-top.php' / セクションなら default
			$template = ($key === 'section') ? 'default' : 'page-' . $key . '.php';

			if ($page instanceof WP_Post) {
				$arr = [
					'name'     => $key,
					'title'    => $title,
					'field'    => $field,
					'location' => [
						[
							[
								'param'    => 'page',
								'operator' => '==',
								'value'    => $page->ID, // ← IDでバッチリ
							],
						],
					],
				];
			} else {
				// ページ未作成・多言語でスラッグ不一致などの時も出せるようテンプレで紐づけ
				$arr = [
					'name'     => $key,
					'title'    => $title,
					'field'    => $field,
					'location' => [
						[
							[
								'param'    => 'page_template',
								'operator' => '==',
								'value'    => $template, // 'default' or 'page-xxx.php'
							],
						],
					],
				];
			}
		} elseif ($type == "block") {
			$arr = array(
				'name' => $type . "_" . $key,
				'title' => $title,
				'field' => $field,
				'location' => array(
					array(
						array(
							'param' => 'block',
							'operator' => '==',
							'value' =>  'acf/' . $key,
						),
					),
				),
			);
		} else {
		}
	}
	return 	$arr;
}



/**
 * relation/postList の taxonomy 指定を ACF relationship 用フィルタ形式へ変換
 * 例: ['category' => ['news']] -> ['topics_category:news']
 *
 * @param string $cpt CPT slug
 * @param array $taxonomy taxonomy 条件
 * @return array ACF relationship の taxonomy 形式配列
 */
function formatAcfTaxonomyFilters($cpt, $taxonomy = []) {
	if (empty($cpt) || empty($taxonomy) || !is_array($taxonomy)) return [];

	$filters = [];
	foreach ($taxonomy as $tax_key => $terms) {
		if (empty($tax_key) || empty($terms)) continue;
		$tax_name = $cpt . '_' . $tax_key;
		$terms = is_array($terms) ? $terms : [$terms];

		foreach ($terms as $term) {
			$slug = '';
			if (is_array($term)) {
				$slug = $term['slug'] ?? '';
			} elseif (is_object($term) && !empty($term->slug)) {
				$slug = $term->slug;
			} else {
				$slug = (string)$term;
			}
			$slug = trim((string)$slug);
			if ($slug === '') continue;
			$filters[] = $tax_name . ':' . $slug;
		}
	}

	return array_values(array_unique($filters));
}

function buildAcfField($key, $value) {
	// 1. bool値 → スキップ
	if (is_bool($value)) return null;

	// 2. 数値 → textarea (rows: 4)
	if (is_int($value) || is_float($value)) {
		return array(
			'type'          => 'textarea',
			'label'         => $key,
			'name'          => $key,
			'rows'          => 4,
			'default_value' => preg_replace('/<br\s*\/?>/i', "\n", (string)$value),
		);
	}

	// 3. 文字列 → textarea
	if (is_string($value)) {
		if ($value === '') return null;
		$rows = 4;
		return array(
			'type'          => 'textarea',
			'label'         => $key,
			'name'          => $key,
			'rows'          => $rows,
			'default_value' => preg_replace('/<br\s*\/?>/i', "\n", $value),
		);
	}

	// 配列以外 / 空配列はスキップ
	if (!is_array($value) || empty($value)) return null;

	// 4. title構造 → setAcfTitle('title', '見出し', array('main', 'sub'), 'table')
	if ($key === 'title') {
		if (isset($value['main'])) {
			if (!empty($value['sub'])) {
				$field = setAcfTitle($key, '見出し', array('main', 'sub'), 'table');
				$field['name']                            = $key;
				$field['label']                           = $key;
				$field['sub_fields'][0]['default_value']  = $value['main'];
				$field['sub_fields'][1]['default_value']  = $value['sub'];
				return $field;
			} else {
				$field = setAcfTitle($key, '見出し', array('main'), 'table');
				$field['name']                            = $key;
				$field['label']                           = $key;
				$field['sub_fields'][0]['default_value']  = $value['main'];
				return $field;
			}
		}
	}

	// 5. 画像構造 → setAcfImage()
	if (isset($value['sizes'])) {
		return setAcfImage($key);
	}

	// 6. リンク構造 → setAcfLink()
	if (isset($value['mode']) && isset($value['link'])) {
		return setAcfLink($key);
	}

	// 7. form構造 → フォーム設定用グループ
	if ($key === 'form' && is_array($value) && isset($value['type'])) {
		return array(
			'type' => 'group',
			'label' => 'フォーム',
			'name' => 'form',
			'layout' => 'rows',
			'sub_fields' => array(
				array(
					'type' => 'button_group',
					'label' => 'フォームタイプ',
					'name' => 'type',
					'return_format' => 'value',
					'choices' => array(
						'input' => '入力画面',
						'confirm' => '確認画面',
						'thanks' => '完了画面',
					),
					'default_value' => $value['type'],
				),
				setAcfText('lead', 'リード文章', 2),
				setAcfText(),
				setAcfLink(),
			),
		);
	}

	// relation構造 → relationship(list) + taxonomy条件
	if ($key === 'relation' && is_array($value) && isset($value['cpt'])) {
		$relationField = [
			'type'          => 'relationship',
			'label'         => '選択する投稿',
			'name'          => 'list',
			'post_type'     => [$value['cpt']],
			'filters'       => ['search', 'taxonomy'],
			'return_format' => 'id',
		];
		if (isset($value['max'])) {
			$relationField['max'] = $value['max'];
		}
		if (isset($value['min'])) {
			$relationField['min'] = $value['min'];
		}
		$tax_filters = formatAcfTaxonomyFilters($value['cpt'], $value['taxonomy'] ?? []);
		if (!empty($tax_filters)) {
			$relationField['taxonomy'] = $tax_filters;
			// taxonomy条件は固定指定するため、UIフィルタは検索のみ
			$relationField['filters'] = ['search'];
		}

		return [
			'type'       => 'group',
			'label'      => ucfirst($key),
			'name'       => $key,
			'layout'     => 'block',
			'sub_fields' => [$relationField],
		];
	}

	// recruit構造 → 採用情報用グループ
	if ($key === 'recruit' && is_array($value)) {
		return setAcfRecruit();
	}

	// 8. postList構造 → setAcfPostList()
	if (isset($value['cpt'])) {
		$field = setAcfPostList($key);
		$field['sub_fields'][0]['default_value'] = $value['cpt'];
		if (isset($value['num'])) $field['sub_fields'][1]['default_value'] = $value['num'];
		if (isset($value['pager'])) $field['sub_fields'][2]['default_value'] = $value['pager'];

		return $field;
	}

	// 8. 数値キー配列
	if (array_keys($value) === range(0, count($value) - 1)) {
		if (empty($value[0]) || !is_array($value[0])) return null;

		// a. flexible content → 静的データのレイアウトから動的に組み立て
		if (isset($value[0]['acf_fc_layout'])) {
			// 対応する setAcf* 関数を parts/ に実装してから $layoutMap に追加する
			$layoutMap = array(
				'title'     => array('label' => '見出し',         'func' => setAcfTitle('title', '', array('main', 'sub', 'h'), 'table')),
				'text'      => array('label' => '本文',           'func' => setAcfText('text', '')),
				'li'        => array('label' => '箇条書き',       'func' => setAcfLi('li', '', 'table'), 'layout' => 'table'),
				'table3'    => array('label' => '表組',           'func' => setAcfTable3('table3', '')),
				'ppcontact' => array('label' => 'お問い合わせ先', 'func' => setAcfPpcontact('ppcontact', '')),
				'sign'      => array('label' => '署名',           'func' => setAcfSign('sign', '')),
				'link'      => array('label' => 'リンク',         'func' => setAcfLink()),
				'links'     => array('label' => 'リンク一覧',     'func' => setAcfLinks()),
				'image'     => array('label' => '画像',           'func' => setAcfImage('image', '')),
				'youtube'   => array('label' => 'Youtube',        'func' => setAcfYoutube('youtube', '')),
				'map'       => array('label' => 'Map',            'func' => setAcfMap('map', '', 'rows')),
				'imagebox'  => array('label' => 'イメージボックス', 'func' => setAcfImageBox('imagebox', '')),
				'chart'     => array('label' => 'グラフ',         'func' => setAcfChart('chart', '')),
				'note'      => array('label' => '注釈',           'func' => setAcfNote('note', '')),
			);
			$usedLayouts = array_unique(array_column($value, 'acf_fc_layout'));
			$subFields = array();
			foreach ($usedLayouts as $layout) {
				if (isset($layoutMap[$layout])) {
					$field = formatFlexAcf($layout, $layoutMap[$layout]['label'], $layoutMap[$layout]['func']);
					if (isset($layoutMap[$layout]['layout'])) {
						$field['layout'] = $layoutMap[$layout]['layout'];
					}
					$subFields[] = $field;
				}
			}
			return array(
				'type'         => 'flexible_content',
				'label'        => ucfirst($key),
				'name'         => $key,
				'layout'       => 'block',
				'min'          => 0,
				'sub_fields'   => $subFields,
				'button_label' => 'コンテンツを追加',
			);
		}

		// b. dt/dd構造 → setAcfHistory() or setAcfTable3()
		if (isset($value[0]['dt']) && isset($value[0]['dd'])) {
			if (strpos($key, 'career') !== false || strpos($key, 'history') !== false) {
				return setAcfHistory($key);
			} else {
				return setAcfTable3($key);
			}
		}

		// c. それ以外 → repeater
		$layout_value = isset($value['acf_layout']) ? $value['acf_layout'] : 'row';
		$label_value  = ucfirst($key);

		$sub_fields = array();
		foreach ($value[0] as $sub_key => $sub_value) {
			if ($sub_key === 'acf_layout') continue;
			$sub_field = buildAcfField($sub_key, $sub_value);
			if ($sub_field) $sub_fields[] = $sub_field;
		}

		return array(
			'type'         => 'repeater',
			'layout'       => $layout_value,
			'label'        => $label_value,
			'name'         => $key,
			'button_label' => $label_value . 'を追加',
			'sub_fields'   => $sub_fields,
		);
	}

	// 9a. repeater + flexible_content パターン
	// 全ての子が title + box（acf_fc_layout含む）を持つ連想配列
	$isRepeaterFlex = true;
	$allBoxLayouts = array();
	foreach ($value as $child) {
		if (!is_array($child) || !isset($child['title']['main']) || !isset($child['box']) || !is_array($child['box'])) {
			$isRepeaterFlex = false;
			break;
		}
		foreach ($child['box'] as $boxItem) {
			if (isset($boxItem['acf_fc_layout'])) {
				$allBoxLayouts[] = $boxItem['acf_fc_layout'];
			}
		}
	}

	if ($isRepeaterFlex && !empty($allBoxLayouts)) {
		// 対応する setAcf* 関数を parts/ に実装してから $layoutMap に追加する
		$layoutMap = array(
			'title'     => array('label' => '見出し',         'func' => setAcfTitle('title', '', array('main', 'sub', 'h'), 'table')),
			'text'      => array('label' => '本文',           'func' => setAcfText('text', '')),
			'li'        => array('label' => '箇条書き',       'func' => setAcfLi('li', '', 'table'), 'layout' => 'table'),
			'table3'    => array('label' => '表組',           'func' => setAcfTable3('table3', '')),
			'ppcontact' => array('label' => 'お問い合わせ先', 'func' => setAcfPpcontact('ppcontact', '')),
			'sign'      => array('label' => '署名',           'func' => setAcfSign('sign', '')),
			'link'      => array('label' => 'リンク',         'func' => setAcfLink()),
			'links'     => array('label' => 'リンク一覧',     'func' => setAcfLinks()),
			'image'     => array('label' => '画像',           'func' => setAcfImage('image', '')),
			'youtube'   => array('label' => 'Youtube',        'func' => setAcfYoutube('youtube', '')),
			'map'       => array('label' => 'Map',            'func' => setAcfMap('map', '', 'rows')),
			'imagebox'  => array('label' => 'イメージボックス', 'func' => setAcfImageBox('imagebox', '')),
			'chart'     => array('label' => 'グラフ',         'func' => setAcfChart('chart', '')),
		);

		$usedLayouts = array_unique($allBoxLayouts);
		$boxSubFields = array();
		foreach ($usedLayouts as $layout) {
			if (isset($layoutMap[$layout])) {
				$field = formatFlexAcf($layout, $layoutMap[$layout]['label'], $layoutMap[$layout]['func']);
				if (isset($layoutMap[$layout]['layout'])) {
					$field['layout'] = $layoutMap[$layout]['layout'];
				}
				$boxSubFields[] = $field;
			}
		}

		// title の ACF を生成（最初の子の title から）
		$firstChild = reset($value);
		$titleField = buildAcfField('title', $firstChild['title']);

		return array(
			'type'         => 'repeater',
			'label'        => ucfirst($key),
			'name'         => $key,
			'layout'       => 'block',
			'button_label' => 'セクションを追加',
			'sub_fields'   => array(
				$titleField,
				array(
					'type'         => 'flexible_content',
					'label'        => 'コンテンツ',
					'name'         => 'box',
					'layout'       => 'block',
					'min'          => 0,
					'sub_fields'   => $boxSubFields,
					'button_label' => 'コンテンツを追加',
				),
			),
		);
	}

	// 9b. 連想配列 → group
	$layout_value = isset($value['acf_layout']) ? $value['acf_layout'] : 'block';
	$label_value  = ucfirst($key);

	$sub_fields = array();
	foreach ($value as $sub_key => $sub_value) {
		if ($sub_key === 'acf_layout') continue;
		$sub_field = buildAcfField($sub_key, $sub_value);
		if ($sub_field) $sub_fields[] = $sub_field;
	}

	return array(
		'type'       => 'group',
		'layout'     => $layout_value,
		'label'      => $label_value,
		'name'       => $key,
		'sub_fields' => $sub_fields,
	);
}

function buildAcfFromValue($pageValue, $skipKeys = array()) {
	$mainKeys = array(
		'title' => 'ページタイトル',
		'image' => '代表画像',
		'text'  => 'リードテキスト',
	);
	$sideKeys = array('meta', 'thumbnail');

	$mainFields  = array();
	$fields      = array();
	$sideFields  = array();

	foreach ($pageValue as $key => $value) {
		if (in_array($key, $skipKeys)) continue;
		$field = buildAcfField($key, $value);
		if (!$field) continue;

		// 1階層目の group は layout: row
		if (isset($field['type']) && $field['type'] === 'group') {
			$field['layout'] = 'row';
		}

		if (array_key_exists($key, $mainKeys)) {
			$field['label'] = $mainKeys[$key];
			$mainFields[] = $field;
		} elseif (in_array($key, $sideKeys)) {
			$sideFields[] = $field;
		} else {
			$fields[] = $field;
		}
	}

	return array(
		'fields' => array_merge($mainFields, $fields),
		'side'   => $sideFields,
	);
}

function registerAcfFromValue($p_key, $title, $type, $pageValue, $skipKeys = array()) {
	global $acfvalues;

	$default_skipkeys = array(
		'nav',
		'parent',
		'class',
		'date',
		'id',
		'slug',
		'post_type',
		'section_mode',
		'pankuzu',
		'taxonomy',
	);
	$skipKeys = array_merge($default_skipkeys, $skipKeys);
	$built = buildAcfFromValue($pageValue, $skipKeys);

	// 通常フィールド
	if (!empty($built['fields'])) {
		$acfKey = ($type === 'page') ? $p_key : $p_key . '_' . $type;
		$acfvalues[$acfKey] = addAcfValueArray($p_key, $title, $type, $built['fields']);
	}

	// pagesetting_side (side)
	if (!empty($built['side'])) {
		$sideKey = ($type === 'page') ? $p_key . '_side' : $p_key . '_' . $type . '_side';
		$arr = addAcfValueArray($p_key, 'ページ設定', $type, $built['side']);
		$arr['name'] = $sideKey;
		$arr['option'] = array('position' => 'side');
		$acfvalues[$sideKey] = $arr;
	}
}



function formatFlexAcf($key, $label, $func, $mode = array()) {
	global $layoutWidth;
	$setting = array(
		"class" =>	array(
			"label" => "クラス",
			"name" => "class",
			"type" => "text",
		),
		'width' => array(
			'type' => 'select',
			'label' => "横幅",
			'name' => 'width',
			'return_format' => 'value',
			// 'width' => 20,
			'choices' => array(
				'' => '通常',
				'narrow' => '狭く',
				'full' => '広く',
				'left' => '右寄せ',
				'right' => '左寄せ',
			),
		),
		'widthForText' => array(
			'type' => 'select',
			'label' => "横幅",
			'name' => 'width',
			'return_format' => 'value',
			// 'width' => 20,
			'default_value' => 'narrow',
			'choices' => array(
				'' => '通常',
				'narrow' => '狭く',
				// 'full' => '広く',
				// 'left' => '右寄せ',
				// 'right' => '左寄せ',
			),
		),
		'textAlign' => array(
			'type' => 'select',
			'label' => "テキスト揃え",
			'name' => 'textAlign',
			'return_format' => 'value',
			// 'width' => 20,
			'choices' => array(
				'' => '左揃え',
				'center' => '中央揃え',
			),
		),
		'rl' => array(
			'type' => 'true_false',
			'label' => '画像位置',
			'name' => 'r',
			'ui_on_text' => '右',
			'ui_off_text' => '左',
		),
		'layout' => array(
			'type' => 'select',
			'label' => 'レイアウト',
			'name' => 'layout',
			'return_format' => 'value',
			'choices' => array(
				'r2' => '2列並び',
				'r3' => '3列並び',
				'r4' => '4列並び',
				'point' => '流れ・ポイント(縦積み)',
				'point_yoko' => '流れ・ポイント(横並び)',
			),
		),
	);
	if ($layoutWidth) {
		$option = array();
		foreach ($mode as $value) {
			$option[] = $setting[$value];
		}
		$array = array(
			'label' => $label,
			'name' => $key,
			'layout' => 'block',
			'sub_fields' => array_merge(
				array(
					array(
						'type' => 'tab',
						'name' => '内容',
						'label' => '内容',
					),
				),
				isset($func['type']) ? array($func) : $func,
				array(
					array(
						'type' => 'tab',
						'name' => 'レイアウト',
						'label' => 'レイアウト',
					),
					array(
						'type' => "group",
						'label' => '',
						'name' => "boxsetting",
						'layout' => 'block',
						'sub_fields' => $option
					)
				)
			),
		);
	} else {
		$subFields = isset($func['type']) ? array($func) : $func;
		$array = array(
			'label' => $label,
			'name' => $key,
			'layout' => 'block',
			'sub_fields' => $subFields,
		);
	}

	return $array;
}



function formatAcfLogic($array, $logic) {
	if (!empty($logic)) {
		$array["conditional_logic"] = array(
			array(
				$logic
			),
		);
	}
	return $array;
}

function setAcfDtDdRepeater(string $name, string $dtLabel = 'dt', string $ddLabel = 'dd', array $options = array()): array
{
	$min            = isset($options['min'])            ? (int)$options['min']            : 0;
	$max            = isset($options['max'])            ? (int)$options['max']            : -1;
	$button_label   = isset($options['button_label'])   ? $options['button_label']        : '項目を追加';
	$layout         = isset($options['layout'])         ? $options['layout']              : 'block';
	$dt_type        = isset($options['dt_type'])        ? $options['dt_type']             : 'textarea';
	$dt_rows        = isset($options['dt_rows'])        ? (int)$options['dt_rows']        : 2;
	$dt_width       = isset($options['dt_width'])       ? (int)$options['dt_width']       : 0;
	$dt_placeholder = isset($options['dt_placeholder']) ? $options['dt_placeholder']      : '';
	$dd_type        = isset($options['dd_type'])        ? $options['dd_type']             : 'textarea';
	$dd_rows        = isset($options['dd_rows'])        ? (int)$options['dd_rows']        : 2;
	$dd_width       = isset($options['dd_width'])       ? (int)$options['dd_width']       : 0;
	$dd_placeholder = isset($options['dd_placeholder']) ? $options['dd_placeholder']      : '';

	$dt = array(
		'type'  => $dt_type,
		'label' => $dtLabel,
		'name'  => 'dt',
		'rows'  => $dt_rows,
	);
	if ($dt_width > 0) {
		$dt['width'] = $dt_width;
	}
	if ($dt_placeholder !== '') {
		$dt['placeholder'] = $dt_placeholder;
	}

	$dd = array(
		'type'  => $dd_type,
		'label' => $ddLabel,
		'name'  => 'dd',
		'rows'  => $dd_rows,
	);
	if ($dd_width > 0) {
		$dd['width'] = $dd_width;
	}
	if ($dd_placeholder !== '') {
		$dd['placeholder'] = $dd_placeholder;
	}

	$array = array(
		'type'         => 'repeater',
		'name'         => $name,
		'layout'       => $layout,
		'button_label' => $button_label,
		'sub_fields'   => array($dt, $dd),
	);

	if ($min > 0) {
		$array['min'] = $min;
	}
	if ($max > 0) {
		$array['max'] = $max;
	}

	return $array;
}

function buildAcfGroup(string $name, string $label, array $subFields, string $layout = 'block', array $logic = array()): array
{
	$array = array(
		'type'       => 'group',
		'label'      => $label,
		'name'       => $name,
		'layout'     => $layout,
		'sub_fields' => $subFields,
	);
	$array = formatAcfLogic($array, $logic);
	return $array;
}
