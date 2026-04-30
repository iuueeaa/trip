<?php
/*
*
* 投稿系を操作するためのfunction
* 　
*/

// postList/relation の taxonomy 指定を WP_Query の tax_query へ変換
function buildTaxQueryFromTaxonomy($cpt, $taxonomy) {
	if (empty($cpt) || empty($taxonomy) || !is_array($taxonomy)) return [];

	$tax_query = ['relation' => 'AND'];
	foreach ($taxonomy as $tax_key => $terms) {
		if (empty($tax_key) || empty($terms)) continue;
		$terms = is_array($terms) ? $terms : [$terms];
		$slugs = [];
		foreach ($terms as $term) {
			if (is_array($term)) {
				$slug = $term['slug'] ?? '';
			} elseif (is_object($term) && !empty($term->slug)) {
				$slug = $term->slug;
			} else {
				$slug = (string)$term;
			}
			$slug = trim((string)$slug);
			if ($slug !== '') $slugs[] = $slug;
		}
		$slugs = array_values(array_unique($slugs));
		if (empty($slugs)) continue;

		$tax_query[] = [
			'taxonomy' => $cpt . '_' . $tax_key,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		];
	}

	return count($tax_query) > 1 ? $tax_query : [];
}

// WP_Query用引数生成
function formatPostListToArgs($body) {
	$args = [
		'post_type' => $body['cpt'] ?? 'any',
		'posts_per_page' => $body['num'] ?? -1,
		'post_status' => 'publish',
		'has_password' => false,
		'paged' => isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1,
	];
	// list が -1 の場合は全件取得、それ以外は指定IDのみ取得
	if (isset($body['list']) && ($body['list'] === -1 || $body['list'] === '-1')) {
		$args['posts_per_page'] = -1;
	} elseif (!empty($body['list'])) {
		$args['post__in'] = $body['list'];
		$args['orderby'] = 'post__in';
	}
	if (!empty($body['tax_query'])) {
		$args['tax_query'] = $body['tax_query'];
	}
	$tax_query = buildTaxQueryFromTaxonomy($body['cpt'] ?? '', $body['taxonomy'] ?? []);
	if (!empty($tax_query)) $args['tax_query'] = $tax_query;
	return $args;
}

// 一覧データ取得（WP/静的両対応）
function getPostListArray($body) {
  global $wpflg;

  if ($wpflg) {
    $args = formatPostListToArgs($body);

    // paged 補完（どちらも拾う：/page/系と/paged/系）
    if (empty($args['paged'])) {
      $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;
      $args['paged'] = max(1, (int)$paged);
    }

    // 追加クエリ
    if (!empty($body['query_args']) && is_array($body['query_args'])) {
      // ★ tax_query が“配列で非空”のときだけセット
      if (!empty($body['query_args']['tax_query']) && is_array($body['query_args']['tax_query'])) {
        $args['tax_query'] = $body['query_args']['tax_query'];
      }
      // paged もあれば反映
      if (!empty($body['query_args']['paged'])) {
        $args['paged'] = (int)$body['query_args']['paged'];
      }
    }

    // （デバッグ用：一度だけ中身確認したいとき）
    // error_log('[getPostListArray args] ' . print_r($args, true));

    $the_query = new WP_Query($args);
    $list = [];
    if ($the_query->have_posts()) {
      while ($the_query->have_posts()) {
        $the_query->the_post();
        $list[] = setValueThumbnail();
      }
    }
    wp_reset_postdata();
    return $list;
  }

  // 静的
  global ${$body['cpt'] . '_list'};
  $cpt_data = ${$body['cpt'] . '_list'} ?? [];
  $list = [];

  // list が -1 の場合は全件取得
  if (isset($body['list']) && ($body['list'] === -1 || $body['list'] === '-1')) {
    foreach ($cpt_data as $item) {
      $list[] = setValueThumbnail($item);
    }
  } else {
    foreach ((array)($body['list'] ?? []) as $id) {
      $list[] = setValueThumbnail($cpt_data[$id] ?? []);
    }
  }
  return $list;
}

// サムネイル情報取得（WP/静的両対応）
function setValueThumbnail($thispost = null)
{
	global $menu_list, $wpflg, $date_format;
	// WP
	if ($wpflg && is_null($thispost)) {
		$array = get_fields();
		$array['title'] = empty(get_field("title")) ? ['main' => get_the_title()] : $array['title'];
		$array['date'] = get_post_time($date_format);
		if (!empty($array['thumbnail']['image'])) $array['image'] = $array['thumbnail']['image'];
		if (!empty($array['thumbnail']['text']))  $array['text']  = $array['thumbnail']['text'];
		if (empty($array['link']['link']['url'])) {
			$array['link'] = [
				'mode' => 'link',
				'link' => [
					'title' => 'VIEW MORE',
					'url' => get_the_permalink(),
					'target' => '',
				],
			];
		}

		$post_id   = get_the_ID();
		$post_type = get_post_type($post_id);
		$tax_objects  = get_object_taxonomies($post_type, 'objects');
		$array['taxonomy'] = []; // ['category'=>[...], 'tag'=>[...]] の形に統一
    foreach ($tax_objects as $tax_slug => $tax_obj) {
      // 表示用キーに正規化: posttype_ プレフィクスを外す（例: news_category → category）
      $prefix = $post_type . '_';
      $key = (strpos($tax_slug, $prefix) === 0) ? substr($tax_slug, strlen($prefix)) : $tax_slug;

      $terms = get_the_terms($post_id, $tax_slug);
      if (is_wp_error($terms) || empty($terms)) continue;

      // WP_Term[] → array[] に揃える
      $array['taxonomy'][$key] = array_map('term_to_array', $terms);
    }

		return $array;
	}
	// 静的
	$array = $thispost;
	// dateがない場合は空文字をセット
	if (!isset($array['date'])) {
		$array['date'] = '';
	}
	if (empty($array['nav']['url']) && !empty($array['parent']) && !empty($menu_list[$array['parent']]['url']) && !empty($array['slug'])) {
		$array['nav']['url'] = $menu_list[$array['parent']]['url'] . $array['slug'];
	}
		if (!empty($array['thumbnail']['image'])) $array['image'] = $array['thumbnail']['image'];
		if (!empty($array['thumbnail']['text']))  $array['text']  = $array['thumbnail']['text'];
	if (empty($array['link'])) {
		$array['link'] = [
			'mode' => 'link',
			'link' => [
				'title' => 'VIEW MORE',
				'url' => $array["nav"]["url"] ?? '',
				'target' => $array["nav"]["target"] ?? '',
			],
		];
	}
		if (empty($array['taxonomy']) && !empty($thispost['taxonomy'])) {
		$array['taxonomy'] = $thispost['taxonomy'];
	}
	return $array;
}

/**
 * relation データから投稿一覧を取得
 * @param array $body relation 構造
 *   - cpt: 投稿タイプ
 *   - list: 投稿ID配列（WP: relationship の値 / 静的: ダミーID）
 *   - max: 最大件数（省略可）
 * @return array 投稿データ配列
 */
function getRelationArray($body) {
	global $wpflg;

	if ($wpflg) {
		$ids = !empty($body['list']) && is_array($body['list']) ? array_filter($body['list']) : [];

		if (empty($ids)) return [];

		$args = [
			'post_type'      => $body['cpt'] ?? 'any',
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => !empty($body['max']) ? (int)$body['max'] : -1,
			'post_status'    => 'publish',
		];
		$tax_query = buildTaxQueryFromTaxonomy($body['cpt'] ?? '', $body['taxonomy'] ?? []);
		if (!empty($tax_query)) {
			$args['tax_query'] = $tax_query;
		}

		$the_query = new WP_Query($args);
		$list = [];
		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$list[] = setValueThumbnail();
			}
		}
		wp_reset_postdata();
		return $list;
	}

	// 静的モード
	global ${$body['cpt'] . '_list'};
	$cpt_data = ${$body['cpt'] . '_list'} ?? [];
	$list = [];
	foreach ((array)($body['list'] ?? []) as $id) {
		$list[] = setValueThumbnail($cpt_data[$id] ?? []);
	}
	return $list;
}

/**
 * セクションvalueから postList または relation の投稿一覧を取得
 * どちらが入っていても同じデータ構造で返すため、テンプレート側で分岐不要
 *
 * @param array $sectionValue セクションの value（postList か relation を含む）
 * @return array 投稿データ配列
 */
function getContentListArray($sectionValue) {
	if (!empty($sectionValue['relation'])) {
		return getRelationArray($sectionValue['relation']);
	}
	if (!empty($sectionValue['postList'])) {
		return getPostListArray($sectionValue['postList']);
	}
	return [];
}
